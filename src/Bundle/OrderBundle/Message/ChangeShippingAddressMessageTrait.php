<?php

namespace App\Bundle\OrderBundle\Message;

use Symfony\Component\Validator\Constraints as Assert;

trait ChangeShippingAddressMessageTrait
{
    #[Assert\NotBlank()]
    public string $firstName;
    #[Assert\NotBlank()]
    public string $lastName;
    #[Assert\NotBlank()]
    public string $addressLine;
    #[Assert\NotBlank()]
    public string $city;
    #[Assert\NotBlank()]
    #[Assert\Length(min: 6,max: 6,exactMessage: 'Должно быть ровно 6 цифр.')]
    #[Assert\Regex(pattern: '/^\d{6}$/',message: 'Должны быть введены только цифры.')]
    public string $postalCode;

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getAddressLine(): string
    {
        return $this->addressLine;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }
}



