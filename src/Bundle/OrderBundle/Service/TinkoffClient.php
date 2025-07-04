<?php

namespace App\Bundle\OrderBundle\Service;

use App\Bundle\OrderBundle\TransferObject\Tinkoff\PaymentRequest;
use App\Bundle\OrderBundle\TransferObject\Tinkoff\PaymentResponse;
use App\Bundle\OrderBundle\Entity\Order;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TinkoffClient
{
    public function __construct(private string $apiUrl, private PaymentRequestFactory $paymentRequestFactory,
                                private SerializerInterface $serializer, private HttpClientInterface $client)
    {}

    public function initPayment(Order $order, string $description): PaymentResponse
    {
        return $this->sendPaymentRequest($this->paymentRequestFactory->createPaymentRequest($order, $description));
    }

    private function sendPaymentRequest(PaymentRequest $paymentRequest): PaymentResponse
    {
        try {
            $response = $this->client->request('POST', $this->apiUrl . 'Init', [
                'json' => $paymentRequest->toArray(),
            ]);

            $paymentResponse = $this->serializer->deserialize($response->getContent(), PaymentResponse::class, 'json');

        } catch (ExceptionInterface $e) {
            $paymentResponse = new PaymentResponse();
            $paymentResponse->Success = false;
            $paymentResponse->errorMessage = 'Ошибка при отправке запроса: ' . $e->getMessage();
        }

        return $paymentResponse;
    }
}