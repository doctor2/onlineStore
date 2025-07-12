<?php

namespace App\Bundle\CoreBundle\Controller\Api;

use App\Bundle\CartBundle\Message\AddProductToCartMessage;
use App\Bundle\ProductBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart/add-product/{id}', name: 'add_to_cart', methods: ['POST'])]
    public function addToCart(?Product $product, MessageBusInterface $bus): Response
    {
        if (!$product) {
            return $this->json(['error' => 'Товар не найден!']);
        }

        $bus->dispatch(new AddProductToCartMessage($this->getUser(), $product));

        return $this->json(['success' => 'Товар добавлен в корзину!']);
    }
}