<?php

namespace App\Bundle\OrderBundle\Message\ShippingAddress;

use App\Bundle\CoreBundle\Entity\User;
use App\Bundle\OrderBundle\Entity\Order;
use Symfony\Component\Messenger\Attribute\Message;

#[Message]
class CreateShippingAddressMessage
{
    use ChangeShippingAddressMessageTrait;

    public function __construct(public User $user, public Order $orderCart)
    {}

    public function getUser(): User
    {
        return $this->user;
    }

    public function getOrderCart(): Order
    {
        return $this->orderCart;
    }
}