<?php

namespace App\Bundle\OrderBundle\TransferObject\Tinkoff;

use App\Bundle\OrderBundle\Entity\OrderItem;

readonly class PaidProductData {
    private ?string $name;
    private ?int $price;
    private ?int $quantity;
    private int $amount;
    private string $tax;

    public function __construct(OrderItem $item, string $tax) {
        $this->name = $item->getProduct()->getName();
        $this->price = $item->getPrice();
        $this->quantity = $item->getQuantity();
        $this->amount = $item->getPrice() * $item->getQuantity();
        $this->tax = $tax;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getTax(): string
    {
        return $this->tax;
    }
}