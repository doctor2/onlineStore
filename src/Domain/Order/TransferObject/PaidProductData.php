<?php

namespace App\Domain\Order\TransferObject;

use App\Domain\Order\Entity\OrderItem;

class PaidProductData {
    private $name;
    private $price;
    private $quantity;
    private $amount;
    private $tax;

    public function __construct(OrderItem $item, string $tax) {
        $this->name = $item->getProduct()->getName();
        $this->price = $item->getPrice();
        $this->quantity = $item->getQuantity();
        $this->amount = $item->getPrice() * $item->getQuantity();
        $this->tax = $tax;
    }

    public function getName() {
        return $this->name;
    }

    public function getPrice() {
        return $this->price;
    }

    public function getQuantity() {
        return $this->quantity;
    }

    public function getAmount() {
        return $this->amount;
    }

    public function getTax() {
        return $this->tax;
    }
}