<?php


namespace App\Bundle\OrderBundle\Message\Handler;

use App\Bundle\OrderBundle\Message\UpdateShippingAddressMessage;
use App\Bundle\OrderBundle\Repository\ShippingAddressRepository;
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
