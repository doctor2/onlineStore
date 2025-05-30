<?php

namespace App\Domain\Cart\Service;

use App\Domain\Cart\Entity\ShoppingCart;
use App\Entity\User;
use Symfony\Component\HttpFoundation\RequestStack;

class GetCartService
{
    public function __construct(private RequestStack $requestStack)
    {}

    public function getCart(?User $user): ShoppingCart
    {
        if($user) {
            $cart = $user->getShoppingCart();
        } elseif(getenv('APP_ENV') === 'test') {
            $cart = new ShoppingCart();
        } else {
            $cart = $this->requestStack->getSession()->get('cart', new ShoppingCart());
        }

        return $cart;
    }
}