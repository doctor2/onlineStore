<?php

namespace App\Domain\Order\Message;

use App\Domain\Order\Entity\ShippingAddress;
use Symfony\Component\Messenger\Attribute\Message;

#[Message]
class UpdateShippingAddressMessage
{
    use ChangeShippingAddressMessageTrait;

    public ShippingAddress $shippingAddress;

    public function __construct(ShippingAddress $shippingAddress) {
        $this->shippingAddress = $shippingAddress;

        $this->firstName = $shippingAddress->getFirstName();
        $this->lastName = $shippingAddress->getLastName();
        $this->addressLine = $shippingAddress->getAddressLine();
        $this->city = $shippingAddress->getCity();
        $this->postalCode = $shippingAddress->getPostalCode();
    }

    public function getShippingAddress(): ShippingAddress
    {
        return $this->shippingAddress;
    }
}