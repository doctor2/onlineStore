<?php

namespace App\Bundle\CoreBundle\Controller\Api;

use App\Bundle\OrderBundle\Message\Cart\DecreaseCartProductInSessionMessage;
use App\Bundle\OrderBundle\Message\Cart\DecreaseCartProductMessage;
use App\Bundle\OrderBundle\Message\Cart\IncreaseCartProductInSessionMessage;
use App\Bundle\OrderBundle\Message\Cart\IncreaseCartProductMessage;
use App\Bundle\OrderBundle\Service\GetOrderCartService;
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
            'totalAmount' => $orderCart->getTotalAmount(),
        ]);
    }

    #[Route('/cart/products/{id}/increase', name: 'increase_number_of_cart_products', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function increaseNumberOfCartProducts(int $id, MessageBusInterface $bus): Response
    {
        if ($this->getUser()) {
            $bus->dispatch(new IncreaseCartProductMessage($this->getUser()->getId(), $id));
        } else {
            $bus->dispatch(new IncreaseCartProductInSessionMessage($id));
        }

        return $this->json(['success' => 'Товар добавлен в корзину!']);
    }

    #[Route('/cart/products/{id}/decrease', name: 'decrease_number_of_cart_products', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function decreaseNumberOfCartProducts(int $id, MessageBusInterface $bus): Response
    {
        if ($this->getUser()) {
            $bus->dispatch(new DecreaseCartProductMessage($this->getUser()->getId(), $id));
        } else {
            $bus->dispatch(new DecreaseCartProductInSessionMessage($id));
        }

        return $this->json(['success' => 'Товар убран из корзины!']);
    }
}