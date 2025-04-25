<?php

namespace App\Domain\Order\Message;

use App\Domain\Order\Entity\Order;
use Symfony\Component\Messenger\Attribute\Message;
use Symfony\Component\Validator\Constraints as Assert;

#[Message]
class CreatePaymentMessage
{
    #[Assert\NotBlank()]
    public string $paymentMethod;

    private Order $order;

    public function __construct(Order $order) {
        $this->order = $order;
    }

    public function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }
}
