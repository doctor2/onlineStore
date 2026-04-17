<?php

namespace App\Bundle\LocationBundle\Repository;

use App\Bundle\LocationBundle\Entity\Locality;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<City>
 */
class LocalityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Locality::class);
    }


    public function searchPaginated(?string $search, int $page, int $limit): array
    {
        $qb = $this->createQueryBuilder('l');

        if ($search) {
            $qb->andWhere('l.name LIKE :s')
                ->setParameter('s', "%$search%");
        }

        return $qb
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->orderBy('l.population', 'DESC')
            ->addOrderBy('l.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
