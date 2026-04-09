<?php

namespace App\Bundle\CoreBundle\Event;

use App\Bundle\CoreBundle\Entity\User;

readonly class UserRegisteredEvent
{
    public function __construct(private User $user) {}

    public function getUser(): User
    {
        return $this->user;
    }
}