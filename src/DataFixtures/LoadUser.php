<?php

namespace App\DataFixtures;

use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Factory\Helper\DefaultUserPasswordGenerator;

class LoadUser extends Fixture
{
    public function __construct(private DefaultUserPasswordGenerator $defaultUserPasswordGenerator)
    {}

    public function load(ObjectManager $manager): void
    {
        UserFactory::new()->admin()->create();
        UserFactory::new()->customer()->create();
        UserFactory::new()->user()->create();
    }
}
