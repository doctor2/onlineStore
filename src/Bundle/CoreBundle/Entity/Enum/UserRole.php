<?php

namespace App\Bundle\CoreBundle\Entity\Enum;

enum UserRole: string
{
    case CUSTOMER = 'ROLE_CUSTOMER';
    case ADMIN = 'ROLE_ADMIN';
    case USER = 'ROLE_USER';

    public function toString(): string {
        return $this->value;
    }
}