<?php


namespace App\Domain\Order\Message\Handler;

use App\Entity\ShippingAddress;
use App\Domain\Order\Message\CreateOrderMessage;
use App\Domain\Order\Message\CreateShippingAddressMessage;
use App\Repository\ShippingAddressRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
class CreateShippingAddressHandler
{
    public function __construct(private ShippingAddressRepository $shippingAddressRepository, private MessageBusInterface $bus)
    {
    }

    public function __invoke(CreateShippingAddressMessage $message): void
    {
        $user = $message->getUser();

        $shippingAddress = new ShippingAddress();
        $shippingAddress->setUser($user);
        $shippingAddress->setFirstName($message->getFirstName());
        $shippingAddress->setLastName($message->getLastName());
        $shippingAddress->setAddressLine($message->getAddressLine());
        $shippingAddress->setCity($message->getCity());
        $shippingAddress->setPostalCode($message->getPostalCode());

        $this->shippingAddressRepository->save($shippingAddress);

        $this->bus->dispatch(new CreateOrderMessage($user, $shippingAddress, $user->getShoppingCart()->getTotalAmount()));
    }
}
