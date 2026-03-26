<?php

namespace App\Bundle\OrderBundle\Service;

use App\Bundle\CoreBundle\Entity\User;
use App\Bundle\OrderBundle\Entity\Order;
use App\Bundle\OrderBundle\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class GetOrderCartService
{
    public function __construct(private RequestStack $requestStack, private OrderRepository $orderRepository)
    {}

    public function getOrderCart(?User $user): Order
    {
        $request =  $this->requestStack->getCurrentRequest();

        if($user) {
            $orderCart = $this->orderRepository->findOrderCartByUser($user);
            if (empty($orderCart)) {
                $orderCart = (new Order())->setUser($user);
                $this->orderRepository->save($orderCart);
            }
        } elseif($request !== null && $request->hasSession()) {
            $orderCart = $this->requestStack->getSession()->get('cart', new Order());
        } else {
            $orderCart = new Order();
        }

        return $orderCart;
    }
}