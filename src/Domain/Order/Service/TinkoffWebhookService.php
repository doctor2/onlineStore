<?php

namespace App\Domain\Order\Service;

use App\Domain\Order\Entity\Enum\OrderStatusTransition;
use App\Domain\Order\Entity\Enum\PaymentStatusTransitions;
use App\Domain\Order\Entity\Enum\TransactionStatus;
use App\Domain\Order\Repository\TransactionRepository;
use App\Domain\Order\TransferObject\Tinkoff\WebhookRequest;
use App\StateMachine\StateMachineInterface;
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