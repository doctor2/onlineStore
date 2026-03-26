<?php

namespace App\Bundle\OrderBundle\Message\Cart\Handler;

use App\Bundle\OrderBundle\Entity\Order;
use App\Bundle\OrderBundle\Message\Cart\IncreaseCartProductMessage;
use App\Bundle\OrderBundle\Service\GetOrderCartService;
use App\Bundle\ProductBundle\Entity\Product;
use App\Bundle\CoreBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class IncreaseCartProductHandler
{
    public function __construct(private EntityManagerInterface $entityManager, private RequestStack $requestStack, private GetOrderCartService $getOrderCartService)
    {
    }

    public function __invoke(IncreaseCartProductMessage $message): void
    {
        $orderCart = $this->getOrderCartService->getOrderCart($message->getUser());

        $this->saveOrderItem($orderCart, $message->getProduct(), $message->getUser());
    }

    private function saveOrderItem(Order $orderCart, Product $product, ?User $user): void
    {
        $orderItem = $orderCart->increaseNumberOfProducts($product);

        if ($user) {
            $this->entityManager->persist($orderItem);
            $this->entityManager->flush();
        } else {
            $this->requestStack->getSession()->set('cart', $orderCart);
        }
    }
}
