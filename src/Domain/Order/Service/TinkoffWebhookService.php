<?php

namespace App\Domain\Order\Service;

use App\Domain\Order\Entity\Enum\OrderStatus;
use App\Domain\Order\Entity\Enum\PaymentStatus;
use App\Domain\Order\Entity\Enum\TransactionStatus;
use App\Domain\Order\Repository\TransactionRepository;
use App\Domain\Order\TransferObject\Tinkoff\WebhookRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TinkoffWebhookService
{
    public function __construct(private TransactionRepository $transactionRepository, private EntityManagerInterface $entityManager)
    {}

    public function onSuccess(WebhookRequest $webhookRequest): void
    {
        $transaction = $this->transactionRepository->findOneBy(['externalId' => $webhookRequest->getPaymentId()]);

        if (!$transaction) {
            throw new NotFoundHttpException('Transaction not found');
        }

        $transaction->setStatus(TransactionStatus::COMPLETED);

        $payment = $transaction->getPayment();
        $payment->setStatus(PaymentStatus::COMPLETED);

        $payment->getOrder()->setStatus(OrderStatus::PAID);

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
        $payment->setStatus(PaymentStatus::FAILED);

        $this->entityManager->flush();
    }
}