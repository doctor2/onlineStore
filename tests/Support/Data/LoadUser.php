<?php

namespace App\Tests\Support\Data;

use App\Tests\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Tests\Support\Helper\DefaultUserPasswordGenerator;

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
