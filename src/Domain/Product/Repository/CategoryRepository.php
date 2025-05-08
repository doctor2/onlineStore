<?php

namespace App\Domain\Product\Repository;

use App\Domain\Product\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

class CategoryRepository extends NestedTreeRepository
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, $em->getClassMetadata(Category::class));
    }

    public function findOneByName($name): ?Category
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
