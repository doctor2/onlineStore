<?php

namespace App\Bundle\CoreBundle\Controller\Api;

use App\Bundle\CoreBundle\Service\JwtTokenProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class TokenController extends AbstractController
{
    #[Route('/mercure-token', name: 'mercure_token')]
    public function mercureToken(JwtTokenProvider $tokenProvider): JsonResponse
    {
        $user = $this->getUser();
        $token = $tokenProvider->generateSubscribeToken($user->getId());

        return $this->json(['token' => $token]);
    }
}
