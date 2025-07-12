<?php

namespace App\Bundle\CartBundle\Message\Handler;

use App\Bundle\CartBundle\Message\IncreaseCartProductMessage;
use App\Bundle\CartBundle\Service\GetCartService;
use App\Bundle\ProductBundle\Entity\Product;
use App\Bundle\CartBundle\Entity\ShoppingCart;
use App\Bundle\CoreBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class IncreaseCartProductHandler
{
    public function __construct(private EntityManagerInterface $entityManager, private RequestStack $requestStack, private GetCartService $getCartService)
    {
    }

    public function __invoke(IncreaseCartProductMessage $message): void
    {
        $cart = $this->getCartService->getCart($message->getUser());

        $this->saveCartItem($cart, $message->getProduct(), $message->getUser());
    }

    private function saveCartItem(ShoppingCart $cart, Product $product, ?User $user): void
    {
        $cartItem = $cart->increaseNumberOfProducts($product);

        if ($user) {
            $this->entityManager->persist($cartItem);
            $this->entityManager->flush();
        } else {
            $this->requestStack->getSession()->set('cart', $cart);
        }
    }
}
