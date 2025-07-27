<?php

namespace App\Bundle\CoreBundle\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class NotificationController extends AbstractController
{
    #[Route(path: '/notify', name: 'notify')]
    public function notify(HubInterface $hub): Response
    {
        $userId = $this->getUser()?->getId();
        $topic = "http://localhost/user/$userId";

        $update = new Update(
            $topic,//'http://localhost/notify/1',
            json_encode(['message' => sprintf('✅ Новое уведомление для пользователя %s!', $userId)]),
            true
        );

        $hub->publish($update);

        return new Response('✅ Уведомление отправлено!');
    }
}
