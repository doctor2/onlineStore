<?php

namespace App\Factory;

use App\Bundle\OrderBundle\Entity\Enum\OrderStatus;
use App\Bundle\OrderBundle\Entity\Order;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Order>
 */
final class OrderFactory extends PersistentProxyObjectFactory
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
        return Order::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'paymentMethod' => self::faker()->text(255),
            'shippingAddress' => ShippingAddressFactory::new(),
            'status' => self::faker()->randomElement(OrderStatus::cases()),
            'totalAmount' => self::faker()->randomNumber(6),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Order $order): void {})
        ;
    }
}
