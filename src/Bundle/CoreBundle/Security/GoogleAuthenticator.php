<?php

namespace App\Bundle\CoreBundle\Security;

use App\Bundle\CoreBundle\Entity\User;
use App\Bundle\CoreBundle\Repository\UserRepository;
use KnpU\OAuth2ClientBundle\Client\Provider\GoogleClient;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class GoogleAuthenticator extends OAuth2Authenticator implements AuthenticationEntryPointInterface
{
    public function __construct(
        private GoogleClient $client,
        private UserRepository $userRepository,
        private RouterInterface $router
    ) {}

    public function supports(Request $request): bool
    {
        return $request->attributes->get('_route') === 'connect_google_check';
    }

    public function authenticate(Request $request): Passport
    {
        $accessToken = $this->client->getAccessToken();
        /** @var GoogleUser $googleUser */
        $googleUser = $this->client->fetchUserFromToken($accessToken);

        $email = $googleUser->getEmail();

        return new SelfValidatingPassport(
            new UserBadge($email, function ($userIdentifier) use ($googleUser) {
                $user = $this->userRepository->findOneBy(['email' => $userIdentifier]);

                if (!$user) {
                    $user = new User();
                    $user->setEmail($userIdentifier);
                    $user->setUsername($googleUser->getName());
                    $user->setFirstName($googleUser->getFirstName());
                    $user->setLastName($googleUser->getLastName());

                    $this->userRepository->save($user);
                }

                return $user;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, $token, string $firewallName): RedirectResponse
    {
        return new RedirectResponse($this->router->generate('home'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): RedirectResponse
    {
        return new RedirectResponse($this->router->generate('app_login'));
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new RedirectResponse($this->router->generate('app_login'), Response::HTTP_TEMPORARY_REDIRECT);
    }
}
