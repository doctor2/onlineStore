<?php


namespace App\Bundle\OrderBundle\Message\Handler;

use App\Bundle\OrderBundle\Entity\ShippingAddress;
use App\Bundle\OrderBundle\Message\CreateShippingAddressMessage;
use App\Bundle\OrderBundle\Repository\ShippingAddressRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateShippingAddressHandler
{
    public function __construct(private ShippingAddressRepository $shippingAddressRepository)
    {
    }

    public function __invoke(CreateShippingAddressMessage $message): ShippingAddress
    {
        $shippingAddress = new ShippingAddress($message);

        $this->shippingAddressRepository->save($shippingAddress);

        return $shippingAddress;
    }
}
