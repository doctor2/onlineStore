<?php

namespace App\Tests\Factory;

use App\Domain\Order\Entity\ShippingAddress;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<ShippingAddress>
 */
final class ShippingAddressFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return ShippingAddress::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'addressLine' => self::faker()->text(255),
            'city' => self::faker()->text(255),
            'firstName' => self::faker()->text(50),
            'lastName' => self::faker()->text(50),
            'postalCode' => self::faker()->text(255),
            'user' => UserFactory::new(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(ShippingAddress $shippingAddress): void {})
        ;
    }
}
