<?php

namespace App\Domain\Order\TransferObject\Tinkoff;

class CustomerData
{
    private ?string $email;
    private ?string $phone;

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    public function toArray(): array
    {
        return [
            'Email' => $this->email,
            'Phone' => $this->phone,
        ];
    }
}
