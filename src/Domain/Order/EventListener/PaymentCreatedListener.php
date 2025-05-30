<?php

namespace App\Domain\Order\EventListener;

use App\Domain\Order\Entity\Enum\OrderStatus;
use App\Domain\Order\Entity\Enum\PaymentStatus;
use App\Domain\Order\Entity\Payment;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\ObjectManager;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: Payment::class)]
class PaymentCreatedListener
{
    public function postPersist(Payment $payment, PostPersistEventArgs $args): void
    {
        $this->addPaymentDataToOrder($payment, $args->getObjectManager());
    }

    private function addPaymentDataToOrder(Payment $payment, ObjectManager $entityManager): void
    {
        $order = $payment->getOrder();
        $order->setPaymentMethod($payment->getPaymentMethod());

        if ($payment->getStatus() !== PaymentStatus::FAILED) {
            $order->setStatus(OrderStatus::PAID);
        }

        $entityManager->persist($order);
        $entityManager->flush();
    }
}