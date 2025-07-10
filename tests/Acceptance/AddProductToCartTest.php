<?php

declare(strict_types=1);

namespace Tests\Acceptance;

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
            ->assertElementCount('.item-details', 1)
            ->assertSeeIn('.item-name', $product->getName())
            ->assertSeeIn('.item-price', $product->getPrice() . ' ₽')
            ->assertSeeIn('#quantity-' . $product->getId(), '2')
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
            ->assertElementCount('.item-details', 2)
            ->assertSeeIn('.item-name', $product->getName())
            ->assertSeeIn('.item-price', $product->getPrice() . ' ₽')
            ->assertSeeIn('#quantity-' . $product->getId(), '1')
            ->assertSeeIn('.cart-items li:nth-child(2) .item-name', $product1->getName())
            ->assertSeeIn('.cart-items li:nth-child(2) .item-price', $product1->getPrice() . ' ₽')
            ->assertSeeIn('#quantity-' . $product1->getId(), '1')
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
