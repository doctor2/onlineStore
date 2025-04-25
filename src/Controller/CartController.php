<?php

namespace App\Controller;

use App\Domain\Cart\Message\CreateCartItemMessage;
use App\Domain\Cart\Service\GetCartService;
use App\Domain\Order\Repository\OrderRepository;
use App\Domain\Product\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'cart')]
    public function index(OrderRepository $orderRepository, GetCartService $getCartService): Response
    {
        $cart = $getCartService->getCart($this->getUser());

        if ($this->getUser()) {
            $pendingOrders = $orderRepository->findPendingOrdersByUser($this->getUser());
        }

        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
            'totalAmount' => $cart->getTotalAmount(),
            'pendingOrders' => $pendingOrders ?? [],
        ]);
    }

    #[Route('/cart/add-product/{id}', name: 'add_to_cart')]
    public function addToCart(?Product $product, MessageBusInterface $bus): Response
    {
        if (!$product) {
            $this->addFlash('error', 'Товар не найден!');

            return $this->redirectToRoute('product_list');
        }

        $bus->dispatch(new CreateCartItemMessage($this->getUser(), $product));

        $this->addFlash('success', 'Товар добавлен в корзину!');

        return $this->redirectToRoute('product_list');
    }
}