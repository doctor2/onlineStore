<?php

namespace App\DataFixtures;

use App\Factory\CategoryFactory;
use App\Factory\ProductFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LoadProduct extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        ProductFactory::createMany(40);

        ProductFactory::new([
            'category' => CategoryFactory::new()->withParent(CategoryFactory::new(['name' => 'Some parent'])->create())
        ])->create();
    }
}
