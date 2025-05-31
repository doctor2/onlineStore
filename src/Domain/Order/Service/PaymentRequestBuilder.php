<?php

namespace App\Domain\Order\Service;

use App\Domain\Order\Entity\Order;
use App\Domain\Order\TransferObject\PaidProductData;
use App\Domain\Order\TransferObject\PaymentRequest;

class PaymentRequestBuilder
{
    public const TAX = 'vat10';
    public const TAXATION = 'osn';

    public function __construct(private string $terminalKey, private string $merchantPass, private string $merchantEmail,
                                private string $merchantPhone)
    {}

    public function build(Order $order, string $successUrl, string $failureUrl): PaymentRequest
    {
        $paymentRequest = new PaymentRequest($this->terminalKey, $this->merchantPass, $order->getTotalAmount(), $order->getId(), $successUrl, $failureUrl);

        $paymentRequest->setCustomerData($order->getUser()->getEmail(), null);

        $paymentRequest->setReceiptData($this->merchantEmail, $this->merchantPhone, self::TAXATION, $this->getPaidProductsData($order));

        return $paymentRequest;
    }

    private function getPaidProductsData(Order $order): array
    {
        $items = [];

        foreach ($order->getOrderItems() as $item) {
            $items[] = new PaidProductData($item, self::TAX);
        }

        return $items;
    }
}