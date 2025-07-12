<?php

namespace App\Bundle\CoreBundle\Controller\Frontend;

use App\Bundle\CartBundle\Service\GetCartService;
use App\Bundle\OrderBundle\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
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
            'totalAmount' => $cart->getTotalAmount(),
            'pendingOrders' => $pendingOrders ?? [],
        ]);
    }
}