<?php

namespace App\Domain\Order\TransferObject\Tinkoff;

readonly class WebhookRequest
{
    public string $PaymentId;

    public function getPaymentId(): string
    {
        return $this->PaymentId;
    }
}
