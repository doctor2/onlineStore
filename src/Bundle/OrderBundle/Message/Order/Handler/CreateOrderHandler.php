<?php

namespace App\Bundle\OrderBundle\Message\Order\Handler;

use App\Bundle\OrderBundle\Entity\Order;
use App\Bundle\OrderBundle\Entity\OrderItem;
use App\Bundle\OrderBundle\Message\Order\CreateOrderMessage;
use App\Bundle\ProductBundle\Entity\Product;
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
        $orderCart = $message->getOrderCart();
        $orderCart->setUser($message->getUser());

        $this->persistOrderItems($orderCart);

        $this->entityManager->persist($orderCart);
        $this->entityManager->flush();

        return $orderCart;
    }

    private function persistOrderItems(Order $orderCart): void
    {
        $productRepository = $this->entityManager->getRepository(Product::class);

        /** @var OrderItem $item */
        foreach ($orderCart->getOrderItems() as $item) {
            $item->setProduct($productRepository->find($item->getProduct()));

            $this->entityManager->persist($item);
        }
    }
}
