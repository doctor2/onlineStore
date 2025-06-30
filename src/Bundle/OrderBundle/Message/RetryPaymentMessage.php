<?php

namespace App\Bundle\OrderBundle\Message;

use App\Bundle\OrderBundle\Entity\Payment;
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
