<?php

namespace App\Bundle\OrderBundle\Message\Cart;

use App\Bundle\CoreBundle\Validator\Constraints\EntityExists\EntityExists;
use Symfony\Component\Messenger\Attribute\Message;

#[Message]
class DecreaseCartProductInSessionMessage
{
    #[EntityExists(message: "Товар не найден!", entityClass: "\App\Bundle\ProductBundle\Entity\Product")]
    private int $productId;

    public function __construct(int $productId) {
        $this->productId = $productId;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }
}
