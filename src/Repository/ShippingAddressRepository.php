<?php

namespace App\Repository;

use App\Entity\ShippingAddress;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ShippingAddress>
 */
class ShippingAddressRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShippingAddress::class);
    }

    public function save(ShippingAddress $address): void
    {
        $this->getEntityManager()->persist($address);
        $this->getEntityManager()->flush($address);
    }
}
