<?php

namespace App\Bundle\OrderBundle\Service;

use App\Bundle\OrderBundle\Entity\Enum\OrderStatusTransition;
use App\Bundle\OrderBundle\Entity\Enum\PaymentStatusTransitions;
use App\Bundle\OrderBundle\Entity\Enum\TransactionStatus;
use App\Bundle\OrderBundle\Repository\TransactionRepository;
use App\Bundle\OrderBundle\TransferObject\Tinkoff\WebhookRequest;
use App\Abstraction\StateMachine\StateMachineInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TinkoffWebhookService
{
    public function __construct(private TransactionRepository $transactionRepository, private EntityManagerInterface $entityManager,
                                private StateMachineInterface $stateMachine)
    {}

    public function onSuccess(WebhookRequest $webhookRequest): void
    {
        $transaction = $this->transactionRepository->findOneBy(['externalId' => $webhookRequest->getPaymentId()]);

        if (!$transaction) {
            throw new NotFoundHttpException('Transaction not found');
        }

        $transaction->setStatus(TransactionStatus::COMPLETED);

        $payment = $transaction->getPayment();
        $this->stateMachine->apply($payment, PaymentStatusTransitions::GRAPH, PaymentStatusTransitions::TRANSITION_COMPLETE);
        $this->stateMachine->apply($payment->getOrder(), OrderStatusTransition::GRAPH, OrderStatusTransition::TRANSITION_PAY);

        $this->entityManager->flush();
    }

    public function onFailure(WebhookRequest $webhookRequest): void
    {
        $transaction = $this->transactionRepository->findOneBy(['externalId' => $webhookRequest->getPaymentId()]);

        if (!$transaction) {
            throw new NotFoundHttpException('Transaction not found');
        }


        $transaction->setStatus(TransactionStatus::FAILED);

        $payment = $transaction->getPayment();
        $this->stateMachine->apply($payment, PaymentStatusTransitions::GRAPH, PaymentStatusTransitions::TRANSITION_FAIL);

        $this->entityManager->flush();
    }
}