<?php

namespace App\Domain\Order\Message;

use App\Domain\Order\Entity\Payment;
use Symfony\Component\Messenger\Attribute\Message;

#[Message]
class RetryPaymentMessage
{
    private Payment $payment;

    public function __construct(Payment $payment) {
        $this->payment = $payment;
    }

    public function getPayment(): Payment
    {
        return $this->payment;
    }
}
