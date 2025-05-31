<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TinkoffWebhookController extends AbstractController
{
    #[Route('/order/success', name: 'order_success')]
    public function success(): Response
    {
        return new Response('Ваш заказ успешно оформлен!');
    }

    #[Route('/order/failure', name: 'order_failure')]
    public function failure(): Response
    {
        return new Response('Ошибка при оформлении заказа!');
    }
}
