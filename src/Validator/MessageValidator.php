<?php

namespace App\Validator;

use Symfony\Component\Validator\Validator\ValidatorInterface;

class MessageValidator
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    public function validate($message): ?string
    {
        $errors = $this->validator->validate($message);

        if (count($errors) > 0) {
            return (string) $errors->get(0)->getMessage();
        }

        return null;
    }
}