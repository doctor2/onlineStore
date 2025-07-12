<?php

namespace App\Bundle\CoreBundle\Controller\Api;

use App\Bundle\CartBundle\Message\DecreaseCartProductMessage;
use App\Bundle\CartBundle\Message\IncreaseCartProductMessage;
use App\Bundle\CartBundle\Service\GetCartService;
use App\Bundle\ProductBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart/products/', name: 'cart_products')]
    public function getCartProducts(GetCartService $getCartService): Response
    {
        $cart = $getCartService->getCart($this->getUser());

        return $this->json($cart->getCartItems(), 200, [], ['groups' => ['cart']]);
    }

    #[Route('/cart/products/{id}/increase', name: 'increase_number_of_cart_products', methods: ['POST'])]
    public function increaseNumberOfCartProducts(?Product $product, MessageBusInterface $bus): Response
    {
        if (!$product) {
            return $this->json(['error' => 'Товар не найден!']);
        }

        $bus->dispatch(new IncreaseCartProductMessage($this->getUser(), $product));

        return $this->json(['success' => 'Товар добавлен в корзину!']);
    }

    #[Route('/cart/products/{id}/decrease', name: 'decrease_number_of_cart_products', methods: ['POST'])]
    public function decreaseNumberOfCartProducts(?Product $product, MessageBusInterface $bus): Response
    {
        if (!$product) {
            return $this->json(['error' => 'Товар не найден!']);
        }

        $bus->dispatch(new DecreaseCartProductMessage($this->getUser(), $product));

        return $this->json(['success' => 'Товар убран из корзины!']);
    }
}