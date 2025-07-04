<?php

namespace App\Bundle\CartBundle\Message;

use App\Bundle\ProductBundle\Entity\Product;
use App\Bundle\CoreBundle\Entity\User;
use Symfony\Component\Messenger\Attribute\Message;

#[Message]
class AddProductToCartMessage
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
