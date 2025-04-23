<?php

namespace App\Domain\Order\TransferObject;

class CustomerData
{
    private $email;
    private $phone;

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    public function toArray()
    {
        return [
            'Email' => $this->email,
            'Phone' => $this->phone,
        ];
    }
}
