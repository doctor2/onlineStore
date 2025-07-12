<?php

namespace App\Bundle\CoreBundle\Controller\Frontend;


use App\Bundle\CartBundle\Service\GetCartService;
use App\Bundle\OrderBundle\Entity\Order;
use App\Bundle\OrderBundle\Entity\Payment;
use App\Bundle\OrderBundle\Message\CreateOrderMessage;
use App\Bundle\OrderBundle\Message\CreatePaymentMessage;
use App\Bundle\OrderBundle\Message\CreateShippingAddressMessage;
use App\Bundle\OrderBundle\Message\RetryPaymentMessage;
use App\Bundle\OrderBundle\Message\UpdateShippingAddressMessage;
use App\Bundle\OrderBundle\Form\OrderType;
use App\Bundle\OrderBundle\Form\ShippingAddressType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class OrderController extends AbstractController
{
    use HandleTrait;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    #[Route('/order/new/shipping-address', name: 'order_shipping_address')]
    public function createShippingAddress(Request $request, GetCartService $getCartService, AuthorizationCheckerInterface $authChecker): Response
    {
        if (!$authChecker->isGranted('NEW_ORDER')) {
            return $this->redirectToRoute('cart');
        }

        $createAddressMessage = new CreateShippingAddressMessage($this->getUser());
        $form = $this->createForm(ShippingAddressType::class, $createAddressMessage);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $shippingAddress = $this->handle($createAddressMessage);

            $pendingOrder = $this->handle(new CreateOrderMessage($shippingAddress, $getCartService->getCart($this->getUser())));

            return $this->redirectToRoute('order_payment', ['id' => $pendingOrder->getId()]);
        }

        return $this->render('order/shipping_address/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/order/{id}/shipping-address/edit', name: 'order_edit_shipping_address')]
    public function editShippingAddress(?Order $pendingOrder, Request $request, AuthorizationCheckerInterface $authChecker): Response
    {
        if (!$authChecker->isGranted('EDIT_ORDER', $pendingOrder)) {
            return $this->redirectToRoute('cart');
        }

        $updateAddressMessage = new UpdateShippingAddressMessage($pendingOrder->getShippingAddress());
        $form = $this->createForm(ShippingAddressType::class, $updateAddressMessage);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->messageBus->dispatch($updateAddressMessage);

            $this->addFlash('success', 'Адрес доставки успешно сохранен.');

            return $this->redirectToRoute('order_payment', ['id' => $pendingOrder->getId()]);
        }

        return $this->render('order/shipping_address/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/order/{id}/payment', name: 'order_payment')]
    public function payment(?Order $pendingOrder, Request $request, AuthorizationCheckerInterface $authChecker): Response
    {
        if (!$authChecker->isGranted('EDIT_ORDER', $pendingOrder)) {
            return $this->redirectToRoute('cart');
        }

        $createPaymentMessage = new CreatePaymentMessage($pendingOrder);
        $form = $this->createForm(OrderType::class, $createPaymentMessage);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $paymentResponse = $this->handle($createPaymentMessage);

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

    #[Route('/payment/{paymentId}/retry', name: 'payment_retry')]
    public function retryPayment(Payment $payment): Response
    {
        $paymentResponse = $this->handle(new RetryPaymentMessage($payment));

        if ($paymentResponse->isSuccess()){
            return $this->redirect($paymentResponse->getPaymentUrl());
        }

        $this->addFlash('error', $paymentResponse->getErrorMessage());

        return $this->redirectToRoute('cart');

    }
}
