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
        $request =  $this->requestStack->getCurrentRequest();

        if($user) {
            $cart = $user->getShoppingCart();
        } elseif($request !== null && $request->hasSession()) {
            $cart = $this->requestStack->getSession()->get('cart', new ShoppingCart());
        } else {
            $cart = new ShoppingCart();
        }

        return $cart;
    }
}