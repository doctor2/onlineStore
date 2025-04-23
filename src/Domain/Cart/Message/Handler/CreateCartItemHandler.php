<?php

namespace App\Domain\Cart\Message\Handler;

use App\Domain\Cart\Message\CreateCartItemMessage;
use App\Domain\Cart\Service\GetCartService;
use App\Entity\CartItem;
use App\Entity\Product;
use App\Entity\ShoppingCart;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateCartItemHandler
{
    public function __construct(private EntityManagerInterface $entityManager, private RequestStack $requestStack, private GetCartService $getCartService)
    {
    }

    public function __invoke(CreateCartItemMessage $message): void
    {
        $product = $this->entityManager->getRepository(Product::class)->find($message->getProductId());

        $cart = $this->getCartService->getCart($message->getUser());

        $this->saveCartItem($cart, $product, $message);
    }

    private function saveCartItem(ShoppingCart $cart, Product $product, CreateCartItemMessage $message): void
    {
        $cartItem = null;
        foreach ($cart->getCartItems() as $item) {
            if ($item->getProduct()->getId() === $product->getId()) {
                $cartItem = $item;
                break;
            }
        }

        if ($cartItem) {
            $cartItem->setQuantity($cartItem->getQuantity() + 1);
        } else {
            $cartItem = new CartItem();
            $cartItem->setProduct($product);
            $cartItem->setPrice($product->getPrice());
            $cartItem->setCart($cart);
            $cartItem->setQuantity(1);
            $cart->addCartItem($cartItem);
        }

        if ($message->getUser()) {
            $this->entityManager->persist($cartItem);
            $this->entityManager->flush();
        } else {
            $this->requestStack->getSession()->set('cart', $cart);
        }
    }
}
