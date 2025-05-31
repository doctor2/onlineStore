<?php

namespace App\Domain\Order\TransferObject\Tinkoff;

class ReceiptData
{
    private string $email;
    private string $phone;
    private string $taxation;
    private array $items = [];

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    public function setTaxation(string $taxation): void
    {
        $this->taxation = $taxation;
    }

    public function addItem(PaidProductData $item): void
    {
        $this->items[] = $item;
    }

    public function toArray()
    {
        return [
            'Email' => $this->email,
            'Phone' => $this->phone,
            'Taxation' => $this->taxation,
            'Items' => array_map(static function(PaidProductData $item) {
                return [
                    'Name' => $item->getName(),
                    'Price' => $item->getPrice(),
                    'Quantity' => $item->getQuantity(),
                    'Amount' => $item->getAmount(),
                    'Tax' => $item->getTax(),
                ];
            }, $this->items),
        ];
    }
}