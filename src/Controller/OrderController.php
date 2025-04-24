<?php

namespace App\Controller;


use App\Domain\Cart\Service\GetCartService;
use App\Domain\Order\Entity\Order;
use App\Domain\Order\Message\CreateOrderMessage;
use App\Domain\Order\Message\CreateShippingAddressMessage;
use App\Domain\Order\Message\UpdateShippingAddressMessage;
use App\Domain\Order\Service\TinkoffPayment;
use App\Domain\Order\Form\OrderType;
use App\Domain\Order\Form\ShippingAddressType;
use App\Domain\Order\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class OrderController extends AbstractController
{
    #[Route('/order/new/shipping-address', name: 'order_shipping_address')]
    public function createShippingAddress(Request $request, MessageBusInterface $bus, GetCartService $getCartService, AuthorizationCheckerInterface $authChecker): Response
    {
        if (!$authChecker->isGranted('NEW_ORDER')) {
            return $this->redirectToRoute('cart');
        }

        $createAddressMessage = new CreateShippingAddressMessage($this->getUser());
        $form = $this->createForm(ShippingAddressType::class, $createAddressMessage);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $envelope = $bus->dispatch($createAddressMessage);

            $handledStamp = $envelope->last(HandledStamp::class);
            $shippingAddress = $handledStamp->getResult();

            $envelope = $bus->dispatch(new CreateOrderMessage($shippingAddress, $getCartService->getCart($this->getUser())));

            $handledStamp = $envelope->last(HandledStamp::class);
            $pendingOrder = $handledStamp->getResult();

            return $this->redirectToRoute('order_payment', ['id' => $pendingOrder->getId()]);
        }

        return $this->render('order/shipping_address/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/order/{id}/shipping-address/edit', name: 'order_edit_shipping_address')]
    public function editShippingAddress(Order $pendingOrder, Request $request, MessageBusInterface $bus, AuthorizationCheckerInterface $authChecker): Response
    {
        if (!$authChecker->isGranted('EDIT_ORDER', $pendingOrder)) {
            return $this->redirectToRoute('cart');
        }

        $updateAddressMessage = new UpdateShippingAddressMessage($pendingOrder->getShippingAddress());
        $form = $this->createForm(ShippingAddressType::class, $updateAddressMessage);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $bus->dispatch($updateAddressMessage);

            $this->addFlash('success', 'Адрес доставки успешно сохранен.');
            return $this->redirectToRoute('order_payment', ['id' => $pendingOrder->getId()]);
        }

        return $this->render('order/shipping_address/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/order/{id}/payment', name: 'order_payment')]
    public function payment(Order $pendingOrder, Request $request, TinkoffPayment $tinkoffPayment, OrderRepository $orderRepository, AuthorizationCheckerInterface $authChecker): Response
    {
        if (!$authChecker->isGranted('EDIT_ORDER', $pendingOrder)) {
            return $this->redirectToRoute('cart');
        }

        $form = $this->createForm(OrderType::class, $pendingOrder);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $orderRepository->save($pendingOrder);

            $paymentResponse = $tinkoffPayment->pay(
                $pendingOrder,
                $this->generateUrl('order_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
                $this->generateUrl('order_failure', [], UrlGeneratorInterface::ABSOLUTE_URL),
            );

            if ($paymentResponse->isSuccess()){
                return $this->redirect($paymentResponse->getPaymentUrl());
            } else {
                $this->addFlash('error', $paymentResponse->getErrorMessage());
            }
        }

        return $this->render('order/payment.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/order/success', name: 'order_success')]
    public function success(): Response
    {
        return new Response('Ваш заказ успешно оформлен!');
    }

    #[Route('/order/failure', name: 'order_failure')]
    public function failure(): Response
    {
        return new Response('Ошибка при оформлении заказа!');
    }
}
