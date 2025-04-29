<?php

declare(strict_types=1);


namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

final class AddProductToCartCest
{
    public function _before(AcceptanceTester $I): void
    {

    }

    public function testAddTwoSameProductByAnonymousUser(AcceptanceTester $I): void
    {
        $I->amOnPage('/cart');
        $I->see('Пусто');

        $I->amOnPage('/');

        $product = $I->findProduct();

        $I->click(sprintf('//form[@action="/cart/add-product/%s"]/*[@type="submit"]', $product['id']));
        $I->click(sprintf('//form[@action="/cart/add-product/%s"]/*[@type="submit"]', $product['id']));
        $I->see('Товар добавлен в корзину!');

        $I->amOnPage('/cart');
        $I->see(sprintf('%s - %s ₽ (2)', $product['name'], $product['price']));
        $I->see(sprintf('Итого: %s ₽', $product['price'] * 2 ));
    }

    public function testAddTwoDifferentProductByAnonymousUser(AcceptanceTester $I): void
    {
        $I->amOnPage('/cart');
        $I->see('Пусто');

        $products = $I->findSeveralProducts(2);

        $I->amOnPage('/');

        $I->click(sprintf('//form[@action="/cart/add-product/%s"]/*[@type="submit"]', $products[0]['id']));
        $I->see('Товар добавлен в корзину!');
        $I->click(sprintf('//form[@action="/cart/add-product/%s"]/*[@type="submit"]', $products[1]['id']));
        $I->see('Товар добавлен в корзину!');

        $I->amOnPage('/cart');
        $I->see(sprintf('%s - %s ₽ (1)', $products[0]['name'], $products[0]['price']));
        $I->see(sprintf('%s - %s ₽ (1)', $products[1]['name'], $products[1]['price']));
        $I->see(sprintf('Итого: %s ₽', $products[0]['price'] + $products[1]['price']));
    }

    public function testAddTwoSameProductByCustomer(AcceptanceTester $I): void
    {
        $I->authAs($I->findCustomer());

        $I->clearCart($I->findCustomer());

        $this->testAddTwoSameProductByAnonymousUser($I);
    }

    public function testAddTwoDifferentProductByCustomer(AcceptanceTester $I): void
    {
        $I->authAs($I->findCustomer());

        $I->clearCart($I->findCustomer());

        $this->testAddTwoDifferentProductByAnonymousUser($I);
    }
}
