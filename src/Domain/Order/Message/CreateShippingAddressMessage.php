<?php

namespace App\Domain\Order\Message;

use App\Entity\User;
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