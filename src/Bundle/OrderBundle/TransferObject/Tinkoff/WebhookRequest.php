<?php

namespace App\Bundle\OrderBundle\TransferObject\Tinkoff;

use Symfony\Component\Serializer\Attribute\SerializedName;

readonly class WebhookRequest
{
    #[SerializedName('PaymentId')]
    public string $paymentId;

    public function getPaymentId(): string
    {
        return $this->paymentId;
    }
}
