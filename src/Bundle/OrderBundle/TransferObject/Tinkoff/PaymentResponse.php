<?php

namespace App\Bundle\OrderBundle\TransferObject\Tinkoff;

use Symfony\Component\Serializer\Attribute\SerializedName;

class PaymentResponse
{
    #[SerializedName('Success')]
    public ?bool $success = null;
    #[SerializedName('ErrorCode')]
    public ?int $errorCode = null;
    #[SerializedName('PaymentId')]
    public ?string $paymentId = null;
    #[SerializedName('PaymentURL')]
    public ?string $paymentURL = null;
    public ?string $errorMessage = null;

    public function isSuccess(): ?bool
    {
        return $this->Success;
    }

    public function getErrorMessage(): ?string
    {
        if ($this->errorCode) {
            return 'Tinkoff error: ' . $this->errorCode;
        }

        return $this->errorMessage;
    }

    public function getPaymentUrl(): ?string
    {
        return $this->paymentURL;
    }

    public function getPaymentId(): ?string
    {
        return $this->paymentId;
    }
}
