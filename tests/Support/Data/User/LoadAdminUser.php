<?php

namespace App\Tests\Support\Data\User;

use App\Entity\Enum\UserRole;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Tests\Support\Helper\DefaultUserPasswordGenerator;

class LoadAdminUser extends Fixture
{
    public const USER_ADMIN = 'user-admin';

    public function __construct(private DefaultUserPasswordGenerator $defaultUserPasswordGenerator)
    {}

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin
            ->setLastName('admin')
            ->setFirstName('admin')
            ->setEmail('admin@test.ru')
            ->setUsername('admin')
            ->setPassword($this->defaultUserPasswordGenerator->generate($admin))
            ->setRoles([UserRole::ADMIN])
        ;

        $this->addReference(self::USER_ADMIN, $admin);
        $manager->persist($admin);

        $manager->flush();
    }
}
