<?php

namespace App\Domain\Order\Service;

use App\Domain\Order\Entity\Payment;
use App\Domain\Order\Entity\Transaction;
use App\Domain\Order\TransferObject\Tinkoff\PaymentResponse;
use Doctrine\ORM\EntityManagerInterface;

class TransactionService
{
    public function __construct(private EntityManagerInterface $entityManager)
    {}

    public function createTransaction(Payment $payment, PaymentResponse $paymentResponse): void
    {
        $transaction = new Transaction($payment, $paymentResponse->getPaymentId());

        $this->entityManager->persist($transaction);
        $this->entityManager->flush();
    }
}