<?php

namespace App\Domain\Order\Message\Handler;

use App\Domain\Cart\Entity\CartItem;
use App\Domain\Order\Entity\Order;
use App\Domain\Order\Entity\OrderItem;
use App\Domain\Order\Message\CreateOrderMessage;
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
        $order = new Order();
        $order
            ->setUser($message->getUser())
            ->setTotalAmount($message->getTotalAmount())
            ->setStatus($message->getStatus())
            ->setShippingAddress($message->getShippingAddress());

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
            $orderItem = new OrderItem();
            $orderItem
                ->setOrder($order)
                ->setPrice($item->getPrice())
                ->setQuantity($item->getQuantity())
                ->setProduct($item->getProduct());

            $this->entityManager->persist($orderItem);
        }
    }
}
