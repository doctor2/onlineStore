<?php

namespace App\Bundle\OrderBundle\Service;

use App\Bundle\OrderBundle\Entity\Payment;
use App\Bundle\OrderBundle\Entity\Transaction;
use App\Bundle\OrderBundle\TransferObject\Tinkoff\PaymentResponse;
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