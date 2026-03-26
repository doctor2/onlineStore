<?php

namespace App\Bundle\CoreBundle\Controller\Api;

use App\Bundle\OrderBundle\Message\Cart\DecreaseCartProductMessage;
use App\Bundle\OrderBundle\Message\Cart\IncreaseCartProductMessage;
use App\Bundle\OrderBundle\Service\GetOrderCartService;
use App\Bundle\ProductBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class CartController extends AbstractController
{
    #[Route('/cart/products/', name: 'cart_products')]
    public function getCartProducts(GetOrderCartService $getOrderCartService, SerializerInterface $serializer): Response
    {
        $orderCart = $getOrderCartService->getOrderCart($this->getUser());
        $orderCartItems = $serializer->serialize($orderCart->getOrderItems(), 'json', ['groups' => ['cart']]);

        return $this->json([
            'cartItems' => json_decode($orderCartItems),
            'totalAmount' => $orderCart->calculateTotalAmount(),
        ]);
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
        // @todo можно перенести в сообщения а в security отлавливать ошибку чтобы отправлять правильный формат
        if (!$product) {
            return $this->json(['error' => 'Товар не найден!']);
        }

        $bus->dispatch(new DecreaseCartProductMessage($this->getUser(), $product));

        return $this->json(['success' => 'Товар убран из корзины!']);
    }
}