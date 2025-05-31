<?php

namespace App\Domain\Order\Message\Handler;

use App\Domain\Order\Entity\Enum\PaymentStatus;
use App\Domain\Order\Entity\Payment;
use App\Domain\Order\Message\CreatePaymentMessage;
use App\Domain\Order\Repository\PaymentRepository;
use App\Domain\Order\Service\TinkoffClient;
use App\Domain\Order\TransferObject\PaymentResponse;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsMessageHandler]
class CreatePaymentHandler
{
    public function __construct(private TinkoffClient $tinkoffClient, private PaymentRepository $paymentRepository, private UrlGeneratorInterface $urlGenerator)
    {}

    public function __invoke(CreatePaymentMessage $message): PaymentResponse
    {
        $paymentResponse = $this->tinkoffClient->initPayment(
            $message->getOrder(),
            $this->urlGenerator->generate('order_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            $this->urlGenerator->generate('order_failure', [], UrlGeneratorInterface::ABSOLUTE_URL)
        );

        $this->createPayment($message, $paymentResponse);

        return $paymentResponse;
    }

    private function createPayment(CreatePaymentMessage $message, $paymentResponse): void
    {
        $payment = new Payment($message->getOrder(), $message->getPaymentMethod());

        if (!$paymentResponse->isSuccess()) {
            $payment->setStatus(PaymentStatus::FAILED);
        }

        $this->paymentRepository->save($payment);
    }
}
