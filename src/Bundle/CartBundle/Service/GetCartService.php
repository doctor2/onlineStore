<?php

namespace App\Bundle\CartBundle\Service;

use App\Bundle\CartBundle\Entity\ShoppingCart;
use App\Bundle\CoreBundle\Entity\User;
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