<?php

namespace App\Bundle\OrderBundle\Message\Cart\Handler;

use App\Bundle\OrderBundle\Message\Cart\IncreaseCartProductInSessionMessage;
use App\Bundle\OrderBundle\Service\GetOrderCartService;
use App\Bundle\ProductBundle\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class IncreaseCartProductInSessionHandler
{
    public function __construct(private EntityManagerInterface $entityManager, private RequestStack $requestStack, private GetOrderCartService $getOrderCartService)
    {
    }

    public function __invoke(IncreaseCartProductInSessionMessage $message): void
    {
        $product = $this->entityManager->getRepository(Product::class)->find($message->getProductId());

        $this->saveProductToSession($product);
    }

    private function saveProductToSession(Product $product): void
    {
        $orderCart = $this->getOrderCartService->getOrderCartFromSession();

        $orderCart->increaseNumberOfProducts($product);

        $this->requestStack->getSession()->set('cart', $orderCart);
    }
}
