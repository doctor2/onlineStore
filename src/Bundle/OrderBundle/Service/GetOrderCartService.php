<?php

namespace App\Bundle\OrderBundle\Service;

use App\Bundle\CoreBundle\Entity\User;
use App\Bundle\OrderBundle\Entity\Order;
use App\Bundle\OrderBundle\Message\Order\CreateOrderMessage;
use App\Bundle\OrderBundle\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class GetOrderCartService
{
    use HandleTrait;

    public function __construct(private RequestStack $requestStack, private OrderRepository $orderRepository, private MessageBusInterface $messageBus)
    {}

    public function getOrderCart(?User $user): Order
    {
        if ($user) {
            $orderCart = $this->getOrCreateOrderCartByUser($user);
        } else {
            $orderCart = $this->getOrderCartFromSession();
        }

        return $orderCart;
    }

    public function getOrderCartFromSession(): Order
    {
        $request =  $this->requestStack->getCurrentRequest();

        if($request !== null && $request->hasSession()) {
            $orderCart = $this->requestStack->getSession()->get('cart', new Order());
        } else {
            $orderCart = new Order();
        }

        return $orderCart;
    }

    public function getOrCreateOrderCartByUser(User $user): Order
    {
        $orderCart = $this->orderRepository->findOrderCartByUser($user);

        if ($orderCart === null) {
            $orderCart = $this->handle(new CreateOrderMessage($user, new Order()));
        }

        return $orderCart;
    }
}