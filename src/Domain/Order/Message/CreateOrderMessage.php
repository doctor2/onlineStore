<?php

namespace App\Domain\Order\Message;

use App\Domain\Cart\Entity\ShoppingCart;
use App\Domain\Order\Entity\Enum\OrderStatus;
use App\Domain\Order\Entity\ShippingAddress;
use App\Entity\User;
use Symfony\Component\Messenger\Attribute\Message;

#[Message]
class CreateOrderMessage
{
    private float $totalAmount;

    private User $user;

    private ShippingAddress $shippingAddress;

    private OrderStatus $status;

    private string $paymentMethod;

    public function __construct(ShippingAddress $shippingAddress, ShoppingCart $cart) {
        $this->user = $shippingAddress->getUser();
        $this->totalAmount = $cart->getTotalAmount();
        $this->status = OrderStatus::PENDING;
        $this->shippingAddress = $shippingAddress;
        $this->paymentMethod = '';
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getShippingAddress(): ShippingAddress
    {
        return $this->shippingAddress;
    }

    public function getTotalAmount(): float
    {
        return $this->totalAmount;
    }

    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    public function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }
}
