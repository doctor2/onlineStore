<?php

namespace App\Bundle\OrderBundle\Entity\Enum;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum PaymentMethod: string implements TranslatableInterface
{
    case BY_CARD = 'by_card';
    case QR_CODE = 'qr_code';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return match ($this) {
            self::BY_CARD  => 'По карте',
            self::QR_CODE => 'По qr коду',
        };
    }

}