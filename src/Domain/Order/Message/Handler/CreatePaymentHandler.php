<?php

namespace App\Domain\Order\Message\Handler;

use App\Domain\Order\Entity\Enum\PaymentStatus;
use App\Domain\Order\Entity\Order;
use App\Domain\Order\Entity\Payment;
use App\Domain\Order\Entity\Transaction;
use App\Domain\Order\Message\CreatePaymentMessage;
use App\Domain\Order\Repository\PaymentRepository;
use App\Domain\Order\Service\TinkoffClient;
use App\Domain\Order\TransferObject\PaymentResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsMessageHandler]
class CreatePaymentHandler
{
    public function __construct(private TinkoffClient $tinkoffClient, private EntityManagerInterface $entityManager, private UrlGeneratorInterface $urlGenerator)
    {}

    public function __invoke(CreatePaymentMessage $message): PaymentResponse
    {
        $paymentResponse = $this->tinkoffClient->initPayment(
            $message->getOrder(),
            $this->urlGenerator->generate('order_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            $this->urlGenerator->generate('order_failure', [], UrlGeneratorInterface::ABSOLUTE_URL)
        );

        if ($paymentResponse->isSuccess()) {
            $this->createTransaction($this->createPayment($message), $paymentResponse);
        }

        return $paymentResponse;
    }

    private function createPayment(CreatePaymentMessage $message): Payment
    {
        $payment = new Payment($message->getOrder(), $message->getPaymentMethod());

        $this->entityManager->persist($payment);

        return $payment;
    }

    private function createTransaction(Payment $payment, PaymentResponse $paymentResponse): void
    {
        $transaction = new Transaction($payment, $paymentResponse->getPaymentId());

        $this->entityManager->persist($transaction);
        $this->entityManager->flush();
    }
}
