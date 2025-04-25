<?php

namespace App\Domain\Cart\Message\Handler;

use App\Domain\Cart\Message\CreateCartItemMessage;
use App\Domain\Cart\Service\GetCartService;
use App\Domain\Cart\Entity\CartItem;
use App\Domain\Product\Entity\Product;
use App\Domain\Cart\Entity\ShoppingCart;
use App\Entity\User;
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
        $cart = $this->getCartService->getCart($message->getUser());

        $this->saveCartItem($cart, $message->getProduct(), $message->getUser());
    }

    private function saveCartItem(ShoppingCart $cart, Product $product, ?User $user): void
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

        if ($user) {
            $this->entityManager->persist($cartItem);
            $this->entityManager->flush();
        } else {
            $this->requestStack->getSession()->set('cart', $cart);
        }
    }
}
