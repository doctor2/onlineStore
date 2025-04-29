<?php

namespace App\Tests\Support\Repository;

use App\Tests\Support\TransferObject\User;
use Codeception\Exception\ModuleException;
use Codeception\Module;
use PDO;

class CartRepository extends Module
{
    public function clearCart(User $user): void
    {
        $connection = $this->getModule('Db')->_getDbh();

        $queryString = 'DELETE ci FROM cart_items as ci JOIN shopping_carts as c ON c.id = ci.cart_id WHERE c.user_id = ' . $user->id;

        $query = $connection->prepare($queryString);

        $query->execute();
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
}
