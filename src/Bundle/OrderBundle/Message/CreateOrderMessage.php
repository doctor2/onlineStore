<?php

namespace App\Bundle\OrderBundle\Message;

use App\Bundle\CartBundle\Entity\ShoppingCart;
use App\Bundle\OrderBundle\Entity\Enum\OrderStatus;
use App\Bundle\OrderBundle\Entity\ShippingAddress;
use App\Bundle\CoreBundle\Entity\User;
use Symfony\Component\Messenger\Attribute\Message;

#[Message]
class CreateOrderMessage
{
    private int $totalAmount;

    private User $user;

    private ShippingAddress $shippingAddress;

    private OrderStatus $status;

    public function __construct(ShippingAddress $shippingAddress, ShoppingCart $cart) {
        $this->user = $shippingAddress->getUser();
        $this->totalAmount = $cart->getTotalAmount();
        $this->status = OrderStatus::PENDING;
        $this->shippingAddress = $shippingAddress;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getShippingAddress(): ShippingAddress
    {
        return $this->shippingAddress;
    }

    public function getTotalAmount(): int
    {
        return $this->totalAmount;
    }

    public function getStatus(): OrderStatus
    {
        return $this->status;
    }
}
