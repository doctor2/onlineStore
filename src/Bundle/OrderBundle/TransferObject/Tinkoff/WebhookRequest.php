<?php

namespace App\Bundle\OrderBundle\TransferObject\Tinkoff;

readonly class WebhookRequest
{
    public string $PaymentId;

    public function getPaymentId(): string
    {
        return $this->PaymentId;
    }
}
