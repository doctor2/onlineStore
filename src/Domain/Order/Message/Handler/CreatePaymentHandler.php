<?php

namespace App\Domain\Order\Message\Handler;

use App\Domain\Order\Entity\Enum\PaymentStatus;
use App\Domain\Order\Entity\Payment;
use App\Domain\Order\Message\CreatePaymentMessage;
use App\Domain\Order\Repository\PaymentRepository;
use App\Domain\Order\Service\TinkoffPaymentService;
use App\Domain\Order\TransferObject\PaymentResponse;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsMessageHandler]
class CreatePaymentHandler
{
    public function __construct(private TinkoffPaymentService $tinkoffPaymentService, private PaymentRepository $paymentRepository, private UrlGeneratorInterface $urlGenerator)
    {}

    public function __invoke(CreatePaymentMessage $message): PaymentResponse
    {
        $paymentResponse = $this->tinkoffPaymentService->pay(
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
            $payment->setPaymentStatus(PaymentStatus::FAILED);
        }

        $this->paymentRepository->save($payment);
    }
}
