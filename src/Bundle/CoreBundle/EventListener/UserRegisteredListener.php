<?php

namespace App\Bundle\CoreBundle\EventListener;

use App\Bundle\CartBundle\Service\GetCartService;
use App\Bundle\ProductBundle\Entity\Product;
use App\Bundle\CoreBundle\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\ObjectManager;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: User::class)]
class UserRegisteredListener
{

    public function __construct(private GetCartService $getCartService)
    {
    }

    public function postPersist(User $user, PostPersistEventArgs $args): void
    {
        $this->saveCartAndCartItems($user, $args->getObjectManager());
    }

    private function saveCartAndCartItems(User $user, ObjectManager $entityManager): void
    {
        $cart = $this->getCartService->getCart(null);
        $cart->setUser($user);

        $productRepository = $entityManager->getRepository(Product::class);

        foreach ($cart->getCartItems() as $item) {
            $item->setProduct($productRepository->find($item->getProduct()));

            $entityManager->persist($item);
        }

        $entityManager->persist($cart);
        $entityManager->flush();
    }
}