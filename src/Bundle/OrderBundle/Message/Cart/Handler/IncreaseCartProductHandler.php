<?php

namespace App\Bundle\OrderBundle\Message\Cart\Handler;

use App\Bundle\OrderBundle\Message\Cart\IncreaseCartProductMessage;
use App\Bundle\OrderBundle\Service\GetOrderCartService;
use App\Bundle\ProductBundle\Entity\Product;
use App\Bundle\CoreBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class IncreaseCartProductHandler
{
    public function __construct(private EntityManagerInterface $entityManager, private GetOrderCartService $getOrderCartService)
    {
    }

    public function __invoke(IncreaseCartProductMessage $message): void
    {
        $user = $this->entityManager->getRepository(User::class)->find($message->getUserId());
        $product = $this->entityManager->getRepository(Product::class)->find($message->getProductId());

        $this->saveProductToCart($product, $user);
    }

    private function saveProductToCart(Product $product, User $user): void
    {
        $orderCart = $this->getOrderCartService->getOrCreateOrderCartByUser($user);

        $orderItem = $orderCart->increaseNumberOfProducts($product);

        $this->entityManager->persist($orderItem);
        $this->entityManager->flush();
    }
}
