<?php

namespace App\Bundle\CoreBundle\Controller\Frontend;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class OAuthController extends AbstractController
{
    #[Route('/connect/google', name: 'connect_google_start')]
    public function connectGoogle(ClientRegistry $clientRegistry): RedirectResponse
    {
        return $clientRegistry
            ->getClient('google')
            ->redirect();
    }

    #[Route('/connect/vk', name: 'connect_vk_start')]
    public function connectVK(ClientRegistry $clientRegistry): RedirectResponse
    {
        return $clientRegistry
            ->getClient('vkontakte')
            ->redirect(['email']);
    }

    // Эти методы будут перехвачены системой безопасности
    #[Route('/connect/google/check', name: 'connect_google_check')]
    public function connectGoogleCheck(Request $request, ClientRegistry $clientRegistry): Response
    {
        return new Response('Авторизация прошла успешно');
    }

    #[Route('/connect/vk/check', name: 'connect_vk_check')]
    public function connectVKCheck(Request $request, ClientRegistry $clientRegistry): Response
    {
        return new Response('Авторизация прошла успешно');
    }
}
