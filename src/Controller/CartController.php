<?php

namespace App\Controller;

use App\Domain\Cart\Message\CreateCartItemMessage;
use App\Domain\Cart\Service\GetCartService;
use App\Domain\Order\Repository\OrderRepository;
use App\Validator\MessageValidator;
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
            $pendingOrder = $orderRepository->findPendingOrderByUser($this->getUser());
        }

        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
            'totalAmount' => $cart->getTotalAmount(),
            'isPendingOrder' => (bool) ($pendingOrder ?? false),
        ]);
    }

    #[Route('/cart/add-product/{id}', name: 'add_to_cart')]
    public function addToCart(int $id, MessageBusInterface $bus, MessageValidator $messageValidator): Response
    {
        $createCartItemMessage = new CreateCartItemMessage($this->getUser(), $id);

        if ($error = $messageValidator->validate($createCartItemMessage)) {
            $this->addFlash('error', (string) $error);

            return $this->redirectToRoute('product_list');
        }

        $bus->dispatch($createCartItemMessage);

        $this->addFlash('success', 'Товар добавлен в корзину!');

        return $this->redirectToRoute('product_list');
    }
}