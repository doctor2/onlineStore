<?php

namespace App\Bundle\OrderBundle\Service;

use App\Bundle\OrderBundle\Entity\Order;
use App\Bundle\OrderBundle\TransferObject\Tinkoff\PaidProductData;
use App\Bundle\OrderBundle\TransferObject\Tinkoff\PaymentRequest;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentRequestFactory
{
    public const TAX = 'vat10';
    public const TAXATION = 'osn';

    public function __construct(private string $terminalKey, private string $merchantPass, private string $merchantEmail,
                                private string $merchantPhone, private UrlGeneratorInterface $urlGenerator)
    {}

    public function createPaymentRequest(Order $order, string $description): PaymentRequest
    {
        $successUrl = $this->urlGenerator->generate('order_success', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $failureUrl = $this->urlGenerator->generate('order_failure', [], UrlGeneratorInterface::ABSOLUTE_URL);

        $paymentRequest = new PaymentRequest($this->terminalKey, $this->merchantPass,
            $order->getTotalAmount(), $order->getId(), $description, $successUrl, $failureUrl
        );

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