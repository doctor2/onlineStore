<?php

namespace App\Domain\Order\Message\Handler;

use App\Domain\Order\Message\RetryPaymentMessage;
use App\Domain\Order\Service\TinkoffClient;
use App\Domain\Order\Service\TransactionService;
use App\Domain\Order\TransferObject\Tinkoff\PaymentResponse;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class RetryPaymentHandler
{
    public function __construct(private TinkoffClient $tinkoffClient, private TransactionService $transactionService)
    {}

    public function __invoke(RetryPaymentMessage $message): PaymentResponse
    {
        $order = $message->getPayment()->getOrder();

        $paymentResponse = $this->tinkoffClient->initPayment($order, 'Повторная оплата заказа #' . $order->getId());

        if ($paymentResponse->isSuccess()) {
            $this->transactionService->createTransaction($message->getPayment(), $paymentResponse);
        }

        return $paymentResponse;
    }
}
