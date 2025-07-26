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
    public function login(HubInterface $hub): Response
    {
        $update = new Update(
            'http://localhost/notify/1',
            json_encode(['message' => 'Новое уведомление!'])
        );

        $hub->publish($update);

        return new Response('Update published!');
    }
}
