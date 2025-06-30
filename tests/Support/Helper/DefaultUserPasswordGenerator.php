<?php

namespace App\Tests\Support\Helper;

use App\Bundle\CoreBundle\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class DefaultUserPasswordGenerator
{
    public const DEFAULT_USER_PASSWORD = '123456';

    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {}

    public function generate(User $user): string
    {
        return $this->userPasswordHasher->hashPassword($user, self::DEFAULT_USER_PASSWORD);
    }
}
