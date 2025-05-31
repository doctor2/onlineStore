<?php

namespace App\Controller;

use App\Domain\Order\Service\TinkoffWebhookService;
use App\Domain\Order\TransferObject\Tinkoff\WebhookRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

final class TinkoffWebhookController extends AbstractController
{
    public function __construct(private TinkoffWebhookService $tinkoffWebhookService, private SerializerInterface $serializer)
    {
    }

    #[Route('/order/success', name: 'order_success')]
    public function success(Request $request): Response
    {
        $webhookRequest = $this->serializer->deserialize($request->getContent(), WebhookRequest::class, 'json');

        $this->tinkoffWebhookService->onSuccess($webhookRequest);

        return new Response('Ваш заказ успешно оформлен!');
    }

    #[Route('/order/failure', name: 'order_failure')]
    public function failure(Request $request): Response
    {
        $webhookRequest = $this->serializer->deserialize($request->getContent(), WebhookRequest::class, 'json');

        $this->tinkoffWebhookService->onFailure($webhookRequest);

        return new Response('Ошибка при оформлении заказа!');
    }
}
