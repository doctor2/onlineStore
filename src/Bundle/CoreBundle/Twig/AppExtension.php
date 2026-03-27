<?php

namespace App\Bundle\CoreBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('format_price', [$this, 'formatPrice']),
        ];
    }

    public function formatPrice($price): string
    {
        return number_format($price, 2, ',', ' ') . ' ₽';
    }
}