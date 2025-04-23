<?php

namespace App\Domain\Cart\Message;

use App\Entity\User;
use App\Validator\Constraints\EntityExists as AppAssert;
use Symfony\Component\Messenger\Attribute\Message;

#[Message]
class CreateCartItemMessage
{
    private ?User $user;

    #[AppAssert\EntityExists(message:"Товар не найден", entityClass:"\App\Entity\Product")]
    public int $productId;

    public function __construct(?User $user, int $productId) {
        $this->user = $user;
        $this->productId = $productId;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }
}
