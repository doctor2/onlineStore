<?php

namespace App\Bundle\OrderBundle\Message\Handler;

use App\Bundle\CartBundle\Entity\CartItem;
use App\Bundle\OrderBundle\Entity\Order;
use App\Bundle\OrderBundle\Entity\OrderItem;
use App\Bundle\OrderBundle\Message\CreateOrderMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateOrderHandler
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function __invoke(CreateOrderMessage $message): Order
    {

        $order = new Order($message);

        $this->persistOrderItems($message, $order);

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return $order;
    }

    private function persistOrderItems(CreateOrderMessage $message, Order $order): void
    {
        $cartItems = $message->getUser()->getShoppingCart()->getCartItems();

        /** @var CartItem $item */
        foreach ($cartItems as $item) {
            $this->entityManager->persist(new OrderItem($order, $item));
        }
    }
}
