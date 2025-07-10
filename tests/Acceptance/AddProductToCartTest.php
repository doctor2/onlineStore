<?php

declare(strict_types=1);

namespace Tests\Acceptance;

use App\Bundle\CoreBundle\Entity\User;
use App\Factory\ProductFactory;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class AddProductToCartTest extends KernelTestCase
{
    use HasBrowser, ResetDatabase, Factories;

    public function testAddTwoSameProductByAnonymousUser(): void
    {
        $product = ProductFactory::createOne();

        $this->browser()
            ->visit('/cart')
            ->assertSee('Пусто')
            ->visit('/')
            ->click(sprintf('form[action="/cart/add-product/%s"] [type="submit"]', $product->getId()))
            ->assertSee('Товар добавлен в корзину!')
            ->click(sprintf('form[action="/cart/add-product/%s"] [type="submit"]', $product->getId()))
            ->assertSee('Товар добавлен в корзину!')
            ->visit('/cart')
            ->assertSee(sprintf('%s - %s ₽ (2)', $product->getName(), $product->getPrice()))
            ->assertSee(sprintf('Итого: %s ₽', $product->getPrice() * 2 ));
    }

    public function testAddTwoDifferentProductByAnonymousUser(): void
    {
        $product = ProductFactory::createOne();
        $product1 = ProductFactory::createOne();

        $this->browser()
            ->visit('/cart')
            ->assertSee('Пусто')
            ->visit('/')
            ->click(sprintf('form[action="/cart/add-product/%s"] [type="submit"]', $product->getId()))
            ->assertSee('Товар добавлен в корзину!')
            ->click(sprintf('form[action="/cart/add-product/%s"] [type="submit"]', $product1->getId()))
            ->assertSee('Товар добавлен в корзину!')
            ->visit('/cart')
            ->assertSee(sprintf('%s - %s ₽ (1)', $product->getName(), $product->getPrice()))
            ->assertSee(sprintf('%s - %s ₽ (1)', $product1->getName(), $product1->getPrice()))
            ->assertSee(sprintf('Итого: %s ₽', $product->getPrice() + $product1->getPrice() ));
    }

    public function testAddTwoSameProductByCustomer(): void
    {
        $customer = UserFactory::new()->customer()->create();

        $this->browser()
            ->actingAs($customer)
            ->visit('/')
            ->assertSee($customer->getUsername())
        ;

        $this->testAddTwoSameProductByAnonymousUser();
    }

    public function testAddTwoDifferentProductByCustomer(): void
    {
        $customer = UserFactory::new()->customer()->create();

        $this->browser()
            ->actingAs($customer)
            ->visit('/')
            ->assertSee($customer->getUsername())
        ;

        $this->testAddTwoDifferentProductByAnonymousUser();
    }
}
