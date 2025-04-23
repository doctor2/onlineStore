<?php

namespace App\Domain\Order\Service;

use App\Domain\Order\TransferObject\PaymentRequest;
use App\Domain\Order\TransferObject\PaymentResponse;
use App\Entity\Order;
use App\Entity\OrderItem;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class TinkoffPayment
{
    public const TAX = 'vat10';
    public const TAXATION = 'osn';

    public function __construct(private string $apiUrl, private string $terminalKey, private string $merchantPass,
                                private string $merchantEmail, private string $merchantPhone)
    {
    }

    public function pay(Order $order, string $successUrl, string $failureUrl): PaymentResponse
    {
        return $this->sendPaymentRequest($this->getPaymentRequest($order, $successUrl, $failureUrl));
    }

    private function getPaymentRequest(Order $order, string $successUrl, string $failureUrl): PaymentRequest
    {
        $paymentRequest = new PaymentRequest($this->terminalKey, $this->merchantPass, $order->getTotalAmountInPennies(), $order->getId(), $successUrl, $failureUrl);

        $paymentRequest->setCustomerData($order->getUser()->getEmail(), null);

        $items = [];

        /** @var OrderItem $item */
        foreach ($order->getOrderItems() as $item) {
            /** @todo рефакторить  */
            $items[] = [
                "Name" => $item->getProduct()->getName(),
                "Price" => $item->getPriceInPennies(),
                "Quantity" => $item->getQuantity(),
                "Amount" => $item->getPriceInPennies() * $item->getQuantity(),
                "Tax" => self::TAX,
            ];
        }

        $paymentRequest->setReceiptData($this->merchantEmail, $this->merchantPhone, self::TAXATION, $items);

        return $paymentRequest;
    }

    private function sendPaymentRequest(PaymentRequest $paymentRequest): PaymentResponse
    {
        try {
            $client = new Client();

            $response = $client->post($this->apiUrl . 'Init', [
                'json' => $paymentRequest->toArray(),
            ]);

            $responseData = json_decode($response->getBody(), true);

            $paymentResponse = new PaymentResponse(
                $responseData['Success'],
                'Ошибка с кодом ' . $responseData['ErrorCode'],
                $responseData['PaymentURL']
            );
        } catch (GuzzleException $e) {
            $paymentResponse = new PaymentResponse(false, 'Ошибка при отправке запроса: ' . $e->getMessage());
        }

        return $paymentResponse;
    }
}