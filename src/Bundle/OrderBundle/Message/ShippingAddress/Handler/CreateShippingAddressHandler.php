<?php


namespace App\Bundle\OrderBundle\Message\ShippingAddress\Handler;

use App\Abstraction\StateMachine\StateMachineInterface;
use App\Bundle\OrderBundle\Entity\Enum\OrderStatusTransitions;
use App\Bundle\OrderBundle\Entity\ShippingAddress;
use App\Bundle\OrderBundle\Message\ShippingAddress\CreateShippingAddressMessage;
use App\Bundle\OrderBundle\Repository\ShippingAddressRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateShippingAddressHandler
{
    public function __construct(private ShippingAddressRepository $shippingAddressRepository, private StateMachineInterface $stateMachine)
    {
    }

    public function __invoke(CreateShippingAddressMessage $message): ShippingAddress
    {
        $shippingAddress = new ShippingAddress($message);
        $message->getOrderCart()->setShippingAddress($shippingAddress);

        $this->stateMachine->apply($message->getOrderCart(), OrderStatusTransitions::GRAPH, OrderStatusTransitions::TRANSITION_PENDING);

        $this->shippingAddressRepository->save($shippingAddress);

        return $shippingAddress;
    }
}
