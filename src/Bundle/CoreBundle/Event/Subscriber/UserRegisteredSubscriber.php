<?php

namespace App\Bundle\CoreBundle\Event\Subscriber;

use App\Bundle\CoreBundle\Entity\User;
use App\Bundle\CoreBundle\Event\UserRegisteredEvent;
use App\Bundle\CoreBundle\Security\EmailVerifier;
use App\Bundle\OrderBundle\Entity\Order;
use App\Bundle\OrderBundle\Message\Order\CreateOrderMessage;
use App\Bundle\OrderBundle\Service\GetOrderCartService;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Mime\Address;

class UserRegisteredSubscriber implements EventSubscriberInterface
{
    public function __construct(private EmailVerifier $emailVerifier, private GetOrderCartService $getOrderCartService,
                                private RequestStack $requestStack, private MessageBusInterface $messageBus) {}

    public static function getSubscribedEvents(): array
    {
        return [
            UserRegisteredEvent::class => 'onUserRegistered',
        ];
    }

    public function onUserRegistered(UserRegisteredEvent $event): void
    {
        // generate a signed url and email it to the user
        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $event->getUser(),
            (new TemplatedEmail())
                ->from(new Address('noreply@test.ru', 'Ivan'))
                ->to((string) $event->getUser()->getEmail())
                ->subject('Пожалуйста, подтвердите свой адрес электронной почты!')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );

        $this->createOrderFromSession($event->getUser());
    }

    public function createOrderFromSession(User $user): void
    {
        $this->messageBus->dispatch(new CreateOrderMessage($user, $this->getOrderCartService->getOrderCartFromSession()));

        $this->requestStack->getSession()->set('cart', new Order());
    }
}