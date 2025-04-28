<?php

namespace App\Tests\Acceptance\Admin;

use Tests\Support\AcceptanceTester;

class DashboardAccessCest
{
    public function testAccessForAnonymousUser(AcceptanceTester $I): void
    {
        $I->amOnPage('/admin/');

        $I->seeInCurrentUrl('/login');
    }

    public function testAccessForAdmin(AcceptanceTester $I): void
    {
        $I->authAs($I->findAdmin());

        $I->amOnPage('/admin');
        $I->see('Admin Dashboard');
    }

    public function testDenyAccessForCustomer(AcceptanceTester $I): void
    {
        $I->authAs($I->findCustomer());

        $I->amOnPage('/admin');
        $I->seeInCurrentUrl('/login');
    }
}
