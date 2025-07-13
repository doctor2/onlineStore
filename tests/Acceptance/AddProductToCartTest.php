<?php

declare(strict_types=1);

namespace Tests\Acceptance;

use App\Factory\Helper\DefaultUserPasswordGenerator;
use App\Factory\ProductFactory;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Panther\PantherTestCaseTrait;
use Zenstruck\Browser\PantherBrowser;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class AddProductToCartTest extends KernelTestCase
{
    use HasBrowser, ResetDatabase, Factories, PantherTestCaseTrait;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pantherBrowser()->client()->restart();
    }

    public function testAddTwoSameProductByAnonymousUser(): void
    {
        $this->addTwoSameProduct($this->pantherBrowser());
    }

    public function testAddTwoDifferentProductByAnonymousUser(): void
    {
        $this->addTwoDifferentProduct($this->pantherBrowser());
    }

    public function testAddTwoSameProductByCustomer(): void
    {
        $customer = UserFactory::new()->customer()->create();

        $browser = $this->loginAs($customer->getEmail(), DefaultUserPasswordGenerator::DEFAULT_USER_PASSWORD)
            ->visit('/')
            ->assertSee('Здравствуйте, ' . $customer->getUsername());

        $this->addTwoSameProduct($browser);
    }

    public function testAddTwoDifferentProductByCustomer(): void
    {
        $customer = UserFactory::new()->customer()->create();

        $browser = $this->loginAs($customer->getEmail(), DefaultUserPasswordGenerator::DEFAULT_USER_PASSWORD)
            ->visit('/')
            ->assertSee('Здравствуйте, ' . $customer->getUsername())
        ;

        $this->addTwoDifferentProduct($browser);
    }

    public function addTwoSameProduct(?PantherBrowser $browser = null): void
    {
        $product = ProductFactory::createOne();

        $browser
            ->visit('/cart')
            ->waitUntilVisible('#app-cart')
            ->assertSee('Пусто')
            ->visit('/')
            ->click(sprintf('[data-product-id="%s"]', $product->getId()))
            ->assertSee('Товар добавлен в корзину!')
            ->click(sprintf('[data-product-id="%s"]', $product->getId()))
            ->assertSee('Товар добавлен в корзину!')
            ->visit('/cart')
            ->waitUntilVisible('#app-cart')
            ->assertElementCount('.item-details', 1)
            ->assertSeeIn('.item-name', $product->getName())
            ->assertSeeIn('#quantity-' . $product->getId(), '2')
        ;
    }

    public function addTwoDifferentProduct(?PantherBrowser $browser = null): void
    {
        $product = ProductFactory::createOne();
        $product1 = ProductFactory::createOne();

        $browser
            ->visit('/cart')
            ->waitUntilVisible('#app-cart')
            ->assertSee('Пусто')
            ->visit('/')
            ->click(sprintf('button[data-product-id="%s"]', $product->getId()))
            ->assertSee('Товар добавлен в корзину!')
            ->click(sprintf('button[data-product-id="%s"]', $product1->getId()))
            ->assertSee('Товар добавлен в корзину!')
            ->visit('/cart')
            ->waitUntilVisible('#app-cart')
            ->assertElementCount('.item-details', 2)
            ->assertSeeIn('.item-name', $product->getName())
            ->assertSeeIn('#quantity-' . $product->getId(), '1')
            ->assertSeeIn('.cart-items li:nth-child(2) .item-name', $product1->getName())
            ->assertSeeIn('#quantity-' . $product1->getId(), '1')
        ;
    }

    protected function loginAs(string $email, string $password): PantherBrowser
    {
        return $this->pantherBrowser()
            ->visit('/login')
            ->fillField('username', $email)
            ->fillField('password', $password)
            ->click('Sign in')
        ;
    }
}
