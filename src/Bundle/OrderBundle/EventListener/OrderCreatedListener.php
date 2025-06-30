<?php

namespace App\Bundle\OrderBundle\EventListener;

use App\Bundle\CartBundle\Entity\ShoppingCart;
use App\Bundle\CartBundle\Service\GetCartService;
use App\Bundle\OrderBundle\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RequestStack;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: Order::class)]
class OrderCreatedListener
{
    public function __construct(private GetCartService $getCartService, private RequestStack $requestStack)
    {
    }

    public function postPersist(Order $order, PostPersistEventArgs $args): void
    {
        $this->clearCart($order, $args->getObjectManager());
    }

    private function clearCart(Order $order, ObjectManager $entityManager): void
    {
        $cart = $this->getCartService->getCart($order->getUser());

        foreach ($cart->getCartItems() as $item) {
            $entityManager->remove($item);
        }

        $entityManager->flush();

        $this->requestStack->getSession()->set('cart', new ShoppingCart());
    }
}