<?php

namespace App\Bundle\OrderBundle\Message\Handler;

use App\Bundle\OrderBundle\Entity\Enum\PaymentStatusTransitions;
use App\Bundle\OrderBundle\Entity\Payment;
use App\Bundle\OrderBundle\Message\CreatePaymentMessage;
use App\Bundle\OrderBundle\Service\TinkoffClient;
use App\Bundle\OrderBundle\Service\TransactionService;
use App\Bundle\OrderBundle\TransferObject\Tinkoff\PaymentResponse;
use App\Abstraction\StateMachine\StateMachineInterface;
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
