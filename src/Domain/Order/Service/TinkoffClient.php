<?php

namespace App\Domain\Order\Service;

use App\Domain\Order\TransferObject\PaymentRequest;
use App\Domain\Order\TransferObject\PaymentResponse;
use App\Domain\Order\Entity\Order;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TinkoffClient
{
    public function __construct(private string $apiUrl, private PaymentRequestBuilder $paymentRequestBuilder,
                                private SerializerInterface $serializer, private HttpClientInterface $client)
    {}

    public function initPayment(Order $order, string $successUrl, string $failureUrl): PaymentResponse
    {
        return $this->sendPaymentRequest($this->paymentRequestBuilder->build($order, $successUrl, $failureUrl));
    }

    private function sendPaymentRequest(PaymentRequest $paymentRequest): PaymentResponse
    {
        try {
            $response = $this->client->request('POST', $this->apiUrl . 'Init', [
                'json' => $paymentRequest->toArray(),
            ]);

            $paymentResponse = $this->serializer->deserialize($response->getBody(), PaymentResponse::class, 'json');

        } catch (TransportExceptionInterface $e) {
            $paymentResponse = new PaymentResponse();
            $paymentResponse->Success = false;
            $paymentResponse->errorMessage = 'Ошибка при отправке запроса: ' . $e->getMessage();
        }

        return $paymentResponse;
    }
}