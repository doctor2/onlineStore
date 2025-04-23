<?php

namespace App\Domain\Order\TransferObject;

class PaymentResponse {
    private $success;
    private $errorMessage;
    private $paymentUrl;

    public function __construct($success, $errorMessage, $paymentUrl = null) {
        $this->success = $success;
        $this->errorMessage = $errorMessage;
        $this->paymentUrl = $paymentUrl;
    }

    public function isSuccess() {
        return $this->success;
    }

    public function getErrorMessage() {
        return $this->errorMessage;
    }

    public function getPaymentUrl() {
        return $this->paymentUrl;
    }
}
