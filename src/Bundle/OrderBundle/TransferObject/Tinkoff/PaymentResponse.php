<?php

namespace App\Bundle\OrderBundle\TransferObject\Tinkoff;

class PaymentResponse {
    public ?bool $Success = null;
    public ?int $ErrorCode = null;
    public ?string $PaymentId = null;
    public ?string $errorMessage = null;
    public ?string $PaymentURL = null;

    public function isSuccess(): ?bool
    {
        return $this->Success;
    }

    public function getErrorMessage(): ?string
    {
        if ($this->ErrorCode) {
            return 'Tinkoff error: ' . $this->errorCode;
        }

        return $this->errorMessage;
    }

    public function getPaymentUrl(): ?string
    {
        return $this->PaymentURL;
    }

    public function getPaymentId(): ?string
    {
        return $this->PaymentId;
    }
}
