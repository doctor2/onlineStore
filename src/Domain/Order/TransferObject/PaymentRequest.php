<?php

namespace App\Domain\Order\TransferObject;

class PaymentRequest
{
    private $terminalKey;
    private $amount;
    private $orderId;
    private $description;
    private $data;
    private $paymentMethod;
    private $successURL;
    private $failURL;
    private $receipt;
    private $token;

    public function __construct(string $terminalKey, string $merchantPass, int $amount, int $orderId, string $successURL, string $failURL)
    {
        $this->terminalKey = $terminalKey;
        $this->amount = $amount;
        $this->orderId = $orderId;
        $this->description = 'Оплата заказа';
        $this->data = new CustomerData(); // Инициализируем класс данных о клиенте
        $this->paymentMethod = 'CreditCard'; // Устанавливаем метод оплаты по умолчанию
        $this->successURL = $successURL;
        $this->failURL = $failURL;
        $this->receipt = new ReceiptData(); // Инициализируем класс квитанции
        $this->token = $this->getToken($merchantPass);
    }

    public function setCustomerData($email, $phone)
    {
        $this->data->setEmail($email);
        $this->data->setPhone($phone);
    }

    public function setReceiptData($email, $phone, $taxation, $items)
    {
        $this->receipt->setEmail($email);
        $this->receipt->setPhone($phone);
        $this->receipt->setTaxation($taxation);
        foreach ($items as $item) {
            $this->receipt->addItem($item);
        }
    }

    private function getToken(string $merchantPass): string
    {
        $sortedByKeys = [
            'TerminalKey' => $this->terminalKey,
            'Amount' => $this->amount,
            'OrderId' => $this->orderId,
            'Description' => $this->description,
            'Password' => $merchantPass
        ];

        ksort($sortedByKeys);

        return hash('sha256', implode( '', array_values($sortedByKeys)));
    }

    public function toArray()
    {
        return [
            'TerminalKey' => $this->terminalKey,
            'Amount' => $this->amount,
            'OrderId' => $this->orderId,
            'Description' => $this->description,
            'Token' => $this->token,
            'Data' => $this->data->toArray(),
            'PaymentMethod' => $this->paymentMethod,
            'SuccessURL' => $this->successURL,
            'FailURL' => $this->failURL,
            'Receipt' => $this->receipt->toArray(),
        ];
    }
}
