<?php

namespace App\Tests\Support\Data\User;

use App\Entity\User;
use App\Entity\Enum\UserRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Tests\Support\Helper\DefaultUserPasswordGenerator;

class LoadCustomerUser extends Fixture
{
    public const USER_CUSTOMER = 'user-customer';

    public function __construct(private DefaultUserPasswordGenerator $defaultUserPasswordGenerator)
    {}

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin
            ->setLastName('Customer')
            ->setFirstName('Customer')
            ->setEmail('customer@test.ru')
            ->setUsername('customer')
            ->setPassword($this->defaultUserPasswordGenerator->generate($admin))
            ->setRoles([UserRole::CUSTOMER])
        ;

        $this->addReference(self::USER_CUSTOMER, $admin);
        $manager->persist($admin);

        $manager->flush();
    }
}
