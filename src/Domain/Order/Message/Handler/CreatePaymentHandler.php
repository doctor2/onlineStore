<?php

namespace App\Domain\Order\Message\Handler;

use App\Domain\Order\Entity\Enum\PaymentStatusTransitions;
use App\Domain\Order\Entity\Payment;
use App\Domain\Order\Message\CreatePaymentMessage;
use App\Domain\Order\Service\TinkoffClient;
use App\Domain\Order\Service\TransactionService;
use App\Domain\Order\TransferObject\Tinkoff\PaymentResponse;
use App\StateMachine\StateMachineInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreatePaymentHandler
{
    public function __construct(private TinkoffClient $tinkoffClient, private EntityManagerInterface $entityManager,
                                private TransactionService $transactionService, private StateMachineInterface $stateMachine)
    {}

    public function __invoke(CreatePaymentMessage $message): PaymentResponse
    {
        $paymentResponse = $this->tinkoffClient->initPayment($message->getOrder(), 'Оплата заказа #' . $message->getOrder()->getId());
        $payment = $this->createPayment($message);

        if ($paymentResponse->isSuccess()) {
            $this->transactionService->createTransaction($payment, $paymentResponse);
            $this->stateMachine->apply($payment, PaymentStatusTransitions::GRAPH, PaymentStatusTransitions::TRANSITION_PROCESS);
        } else {
            $this->stateMachine->apply($payment, PaymentStatusTransitions::GRAPH, PaymentStatusTransitions::TRANSITION_FAIL);
        }

        $this->entityManager->flush();

        return $paymentResponse;
    }

    private function createPayment(CreatePaymentMessage $message): Payment
    {
        $payment = new Payment($message->getOrder(), $message->getPaymentMethod());

        $this->entityManager->persist($payment);
        $this->entityManager->flush();

        return $payment;
    }
}
