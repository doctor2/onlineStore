<?php

namespace App\Bundle\CoreBundle\EventListener;

use App\Bundle\OrderBundle\Entity\Order;
use App\Bundle\OrderBundle\Message\Order\CreateOrderMessage;
use App\Bundle\OrderBundle\Service\GetOrderCartService;
use App\Bundle\CoreBundle\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: User::class)]
class UserRegisteredListener
{

    public function __construct(private GetOrderCartService $getOrderCartService, private RequestStack $requestStack,
                                private MessageBusInterface $messageBus)
    {
    }

    public function postPersist(User $user, PostPersistEventArgs $args): void
    {
        $this->messageBus->dispatch(new CreateOrderMessage($user, $this->getOrderCartService->getOrderCartFromSession()));

        $this->requestStack->getSession()->set('cart', new Order());
    }
}