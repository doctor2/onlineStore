<?php

namespace App\Bundle\OrderBundle\Message;

use App\Bundle\OrderBundle\Entity\Enum\PaymentMethod;
use App\Bundle\OrderBundle\Entity\Order;
use Symfony\Component\Messenger\Attribute\Message;
use Symfony\Component\Validator\Constraints as Assert;

#[Message]
class CreatePaymentMessage
{
    #[Assert\NotBlank()]
    public ?PaymentMethod $paymentMethod;

    private Order $order;

    public function __construct(Order $order) {
        $this->order = $order;
    }

    public function getPaymentMethod(): PaymentMethod
    {
        return $this->paymentMethod;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }
}
