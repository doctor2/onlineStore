<?php

namespace App\Domain\Cart\Message;

use App\Domain\Product\Entity\Product;
use App\Entity\User;
use Symfony\Component\Messenger\Attribute\Message;

#[Message]
class CreateCartItemMessage
{
    private ?User $user;

    private Product $product;

    public function __construct(?User $user, Product $product) {
        $this->user = $user;
        $this->product = $product;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }
}
