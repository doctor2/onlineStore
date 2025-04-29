<?php

namespace App\Tests\Support\Repository;

use Codeception\Exception\ModuleException;
use Codeception\Module;
use PDO;

class ProductRepository extends Module
{
    public function findProduct(): array
    {
        return $this->grabInformationFromDataBase('SELECT * FROM products LIMIT 1;');
    }

    public function findSeveralProducts(int $limit = 1): array
    {
        return $this->grabAllInformationFromDataBase(sprintf('SELECT * FROM products ORDER BY RAND() LIMIT %d;', $limit));
    }

    /**
     * @throws ModuleException
     */
    private function grabInformationFromDataBase(string $queryString): array
    {
        $connection = $this->getModule('Db')->_getDbh();

        $query = $connection->query($queryString, PDO::FETCH_ASSOC);

        $information = $query->fetch();
        if (!$information) {
            throw new \RuntimeException('Not found by criteria');
        }

        return $information;
    }

    private function grabAllInformationFromDataBase(string $queryString): array
    {
        $connection = $this->getModule('Db')->_getDbh();

        $query = $connection->query($queryString, PDO::FETCH_ASSOC);

        $information = $query->fetchAll();
        if (!$information) {
            throw new \RuntimeException('Not found by criteria');
        }

        return $information;
    }
}
