<?php


namespace App\Domain\Order\Message\Handler;

use App\Domain\Order\Message\UpdateShippingAddressMessage;
use App\Domain\Order\Repository\ShippingAddressRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UpdateShippingAddressHandler
{
    public function __construct(private ShippingAddressRepository $shippingAddressRepository)
    {
    }

    public function __invoke(UpdateShippingAddressMessage $message): void
    {
        $shippingAddress = $message->getShippingAddress();

        $shippingAddress->setFirstName($message->getFirstName());
        $shippingAddress->setLastName($message->getLastName());
        $shippingAddress->setAddressLine($message->getAddressLine());
        $shippingAddress->setCity($message->getCity());
        $shippingAddress->setPostalCode($message->getPostalCode());

        $this->shippingAddressRepository->save($shippingAddress);
    }
}
