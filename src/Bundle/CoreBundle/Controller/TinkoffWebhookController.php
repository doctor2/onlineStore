<?php

namespace App\Bundle\CoreBundle\Controller;

use App\Bundle\OrderBundle\Service\TinkoffWebhookService;
use App\Bundle\OrderBundle\TransferObject\Tinkoff\WebhookRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

final class TinkoffWebhookController extends AbstractController
{
    public function __construct(private TinkoffWebhookService $tinkoffWebhookService, private SerializerInterface $serializer)
    {
    }

    #[Route('/order/success', name: 'order_success')]
    public function success(#[MapRequestPayload] WebhookRequest $webhookRequest): Response
    {
        $this->tinkoffWebhookService->onSuccess($webhookRequest);

        return new Response('Ваш заказ успешно оформлен!');
    }

    #[Route('/order/failure', name: 'order_failure')]
    public function failure(#[MapRequestPayload] WebhookRequest $webhookRequest): Response
    {
        $this->tinkoffWebhookService->onFailure($webhookRequest);

        return new Response('Ошибка при оформлении заказа!');
    }
}
