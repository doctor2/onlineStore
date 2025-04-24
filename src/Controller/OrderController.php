<?php

namespace App\Controller;


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
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class OrderController extends AbstractController
{
    #[Route('/order/shipping-address', name: 'order_shipping_address')]
    public function createShippingAddress(Request $request, MessageBusInterface $bus): Response
    {
        $createAddressMessage = new CreateShippingAddressMessage($this->getUser());
        $form = $this->createForm(ShippingAddressType::class, $createAddressMessage);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $bus->dispatch($createAddressMessage);

            return $this->redirectToRoute('order_payment');
        }

        return $this->render('order/shipping_address/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/order/shipping-address/edit', name: 'order_edit_shipping_address')]
    public function editShippingAddress(Request $request, MessageBusInterface $bus, OrderRepository $orderRepository): Response
    {
        $pendingOrder = $orderRepository->findPendingOrderByUser($this->getUser());

        $updateAddressMessage = new UpdateShippingAddressMessage($pendingOrder->getShippingAddress());
        $form = $this->createForm(ShippingAddressType::class, $updateAddressMessage);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $bus->dispatch($updateAddressMessage);

            $this->addFlash('success', 'Адрес доставки успешно сохранен.');
            return $this->redirectToRoute('order_payment');
        }

        return $this->render('order/shipping_address/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/order/payment', name: 'order_payment')]
    public function payment(Request $request, TinkoffPayment $tinkoffPayment, OrderRepository $orderRepository): Response
    {
        $pendingOrder = $orderRepository->findPendingOrderByUser($this->getUser());
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
