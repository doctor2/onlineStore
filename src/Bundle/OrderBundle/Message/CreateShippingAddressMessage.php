<?php

namespace App\Bundle\OrderBundle\Message;

use App\Bundle\CoreBundle\Entity\User;
use Symfony\Component\Messenger\Attribute\Message;

#[Message]
class CreateShippingAddressMessage
{
    use ChangeShippingAddressMessageTrait;

    public function __construct(public User $user)
    {}

    public function getUser(): User
    {
        return $this->user;
    }
}