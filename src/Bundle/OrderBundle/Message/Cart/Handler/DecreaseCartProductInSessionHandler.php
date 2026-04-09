<?php

namespace App\Bundle\OrderBundle\Message\Cart\Handler;

use App\Bundle\OrderBundle\Message\Cart\DecreaseCartProductInSessionMessage;
use App\Bundle\OrderBundle\Service\GetOrderCartService;
use App\Bundle\ProductBundle\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class DecreaseCartProductInSessionHandler
{
    public function __construct(private EntityManagerInterface $entityManager, private RequestStack $requestStack, private GetOrderCartService $getOrderCartService)
    {
    }

    public function __invoke(DecreaseCartProductInSessionMessage $message): void
    {
        $product = $this->entityManager->getRepository(Product::class)->find($message->getProductId());

        $this->saveProductToSession($product);
    }

    private function saveProductToSession(Product $product): void
    {
        $orderCart = $this->getOrderCartService->getOrderCartFromSession();

        $orderCart->decreaseNumberOfProducts($product);

        $this->requestStack->getSession()->set('cart', $orderCart);
    }
}
