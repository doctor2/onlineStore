<?php

namespace App\Domain\Order\TransferObject;

class PaymentResponse {
    public ?bool $Success = null;
    public ?int $ErrorCode = null;
    public ?string $errorMessage = null;
    public ?string $PaymentURL = null;

    public function isSuccess(): ?bool
    {
        return $this->Success;
    }

    public function getErrorMessage(): ?string
    {
        if ($this->ErrorCode) {
            return 'Ошибка с кодом ' . $this->errorCode;
        }

        return $this->errorMessage;
    }

    public function getPaymentUrl(): ?string
    {
        return $this->PaymentURL;
    }
}
