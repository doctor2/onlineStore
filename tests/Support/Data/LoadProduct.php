<?php

namespace App\Tests\Support\Data;

use App\Tests\Factory\CategoryFactory;
use App\Tests\Factory\ProductFactory;
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
