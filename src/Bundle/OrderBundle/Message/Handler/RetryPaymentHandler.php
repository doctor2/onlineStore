<?php

namespace App\Bundle\OrderBundle\Message\Handler;

use App\Bundle\OrderBundle\Message\RetryPaymentMessage;
use App\Bundle\OrderBundle\Service\TinkoffClient;
use App\Bundle\OrderBundle\Service\TransactionService;
use App\Bundle\OrderBundle\TransferObject\Tinkoff\PaymentResponse;
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
