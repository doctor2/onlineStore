<?php

namespace App\Bundle\OrderBundle\Message\Cart;

use App\Bundle\CoreBundle\Validator\Constraints\EntityExists\EntityExists;
use Symfony\Component\Messenger\Attribute\Message;

#[Message]
class IncreaseCartProductMessage
{
    #[EntityExists(message: "Пользователь не найден!", entityClass: "\App\Bundle\CoreBundle\Entity\User")]
    private int $userId;

    #[EntityExists(message: "Товар не найден!", entityClass: "\App\Bundle\ProductBundle\Entity\Product")]
    private int $productId;

    public function __construct(int $userId, int $productId) {
        $this->userId = $userId;
        $this->productId = $productId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }
}
