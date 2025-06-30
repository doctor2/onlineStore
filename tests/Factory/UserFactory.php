<?php

namespace App\Tests\Factory;

use App\Bundle\CoreBundle\Entity\Enum\UserRole;
use App\Bundle\CoreBundle\Entity\User;
use App\Tests\Support\Helper\DefaultUserPasswordGenerator;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<User>
 */
final class UserFactory extends PersistentProxyObjectFactory
{
    public function __construct(private DefaultUserPasswordGenerator $defaultUserPasswordGenerator)
    {}

    public static function class(): string
    {
        return User::class;
    }

    public function admin(): self
    {
        return $this->with(['roles' => [UserRole::ADMIN]]);
    }

    public function customer(): self
    {
        return $this->with(['roles' => [UserRole::CUSTOMER]]);
    }

    public function user(): self
    {
        return $this->with(['roles' => [UserRole::USER]]);
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'email' => self::faker()->email(),
            'firstName' => self::faker()->name(),
            'lastName' => self::faker()->userName(),
            'password' => '',
            'roles' => [],
            'username' => self::faker()->userName(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
             ->afterInstantiate(function(User $user): void {
                 $user->setPassword($this->defaultUserPasswordGenerator->generate($user));
             })
        ;
    }
}
