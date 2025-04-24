<?php

namespace App\Domain\Order\TransferObject;

class ReceiptData
{
    private $email;
    private $phone;
    private $taxation;
    private $items = [];

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    public function setTaxation($taxation)
    {
        $this->taxation = $taxation;
    }

    public function addItem(PaidProductData $item)
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