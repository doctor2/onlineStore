<?php

namespace App\Domain\Order\TransferObject;

class PaymentRequest
{
    private string $terminalKey;
    private int $amount;
    private int $orderId;
    private string $description;
    private CustomerData $data;
    private string $paymentMethod;
    private string $successURL;
    private string $failURL;
    private ReceiptData $receipt;
    private string $token;

    public function __construct(string $terminalKey, string $merchantPass, int $amount, int $orderId, string $successURL, string $failURL)
    {
        $this->terminalKey = $terminalKey;
        $this->amount = $amount;
        $this->orderId = $orderId;
        $this->description = 'Оплата заказа #' . $orderId;
        $this->data = new CustomerData(); // Инициализируем класс данных о клиенте
        $this->paymentMethod = 'CreditCard'; // Устанавливаем метод оплаты по умолчанию
        $this->successURL = $successURL;
        $this->failURL = $failURL;
        $this->receipt = new ReceiptData(); // Инициализируем класс квитанции
        $this->token = $this->getToken($merchantPass);
    }

    public function setCustomerData(?string $email, ?string $phone): void
    {
        $this->data->setEmail($email);
        $this->data->setPhone($phone);
    }

    public function setReceiptData(string $email, string $phone, string $taxation, $items): void
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

    public function toArray(): array
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
