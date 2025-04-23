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

    public function addItem($item)
    {
        $this->items[] = $item;
    }

    public function toArray()
    {
        return [
            'Email' => $this->email,
            'Phone' => $this->phone,
            'Taxation' => $this->taxation,
            'Items' => array_map(function($item) {
                return [
                    'Name' => $item['Name'],
                    'Price' => $item['Price'],
                    'Quantity' => $item['Quantity'],
                    'Amount' => $item['Amount'],
                    'Tax' => $item['Tax'],
                ];
            }, $this->items),
        ];
    }
}