<?php

namespace App\Domain\Order\Message\Handler;

use App\Domain\Order\Message\CreatePaymentMessage;
use App\Domain\Order\Repository\OrderRepository;
use App\Domain\Order\Service\TinkoffPaymentService;
use App\Domain\Order\TransferObject\PaymentResponse;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsMessageHandler]
class CreatePaymentHandler
{
    public function __construct(private TinkoffPaymentService $tinkoffPaymentService, private OrderRepository $orderRepository, private UrlGeneratorInterface $urlGenerator)
    {}

    public function __invoke(CreatePaymentMessage $message): PaymentResponse
    {
        $order = $message->getOrder();
        $order->setPaymentMethod($message->getPaymentMethod());

        $this->orderRepository->save($order);

        return $this->tinkoffPaymentService->pay(
            $order,
            $this->urlGenerator->generate('order_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            $this->urlGenerator->generate('order_failure', [], UrlGeneratorInterface::ABSOLUTE_URL)
        );
    }
}
