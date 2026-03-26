<?php

namespace App\Bundle\OrderBundle\Message\Order;

use App\Bundle\OrderBundle\Entity\Order;
use App\Bundle\CoreBundle\Entity\User;
use Symfony\Component\Messenger\Attribute\Message;

#[Message]
class CreateOrderMessage
{
    private User $user;

    private Order $orderCart;

    public function __construct(User $user, Order $orderCart) {
        $this->user = $user;
        $this->orderCart = $orderCart;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getOrderCart(): Order
    {
        return $this->orderCart;
    }
}
