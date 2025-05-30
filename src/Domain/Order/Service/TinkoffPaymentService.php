<?php

namespace App\Domain\Order\Service;

use App\Domain\Order\TransferObject\PaidProductData;
use App\Domain\Order\TransferObject\PaymentRequest;
use App\Domain\Order\TransferObject\PaymentResponse;
use App\Domain\Order\Entity\Order;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Serializer\SerializerInterface;

class TinkoffPaymentService
{
    public const TAX = 'vat10';
    public const TAXATION = 'osn';

    public function __construct(private string $apiUrl, private string $terminalKey, private string $merchantPass,
                                private string $merchantEmail, private string $merchantPhone, private SerializerInterface $serializer)
    {
    }

    public function pay(Order $order, string $successUrl, string $failureUrl): PaymentResponse
    {
        return $this->sendPaymentRequest($this->getPaymentRequest($order, $successUrl, $failureUrl));
    }

    private function getPaymentRequest(Order $order, string $successUrl, string $failureUrl): PaymentRequest
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

    private function sendPaymentRequest(PaymentRequest $paymentRequest): PaymentResponse
    {
        try {
            $client = new Client();

            $response = $client->post($this->apiUrl . 'Init', [
                'json' => $paymentRequest->toArray(),
            ]);

            $paymentResponse = $this->serializer->deserialize($response->getBody(), PaymentResponse::class,'json');

        } catch (GuzzleException $e) {
            $paymentResponse = new PaymentResponse();
            $paymentResponse->Success = false;
            $paymentResponse->errorMessage = 'Ошибка при отправке запроса: ' . $e->getMessage();
        }

        return $paymentResponse;
    }
}