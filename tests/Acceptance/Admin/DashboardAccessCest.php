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
        $I->amOnPage('/login');
        $I->fillField('Email', 'email');
        $I->fillField('Password', '123456');
        $I->click('Sign in');

        $I->amOnPage('/admin');
        $I->see('Admin Dashboard');
    }

//    public function denyAccess(Tester $I): void
//    {
//        $testClosure = function (Tester $I) {
//            $I->dontSee('Административный раздел', 'h1');
//            $I->see('страница не найдена');
//        };
//
//        $accessCheckPages = new AccessCheckPages($I, $this->pages, AccessCheckPages::STRATEGY_NOT_FOUND);
//        $accessCheckPages
//            ->addTest($I->findNotBannedUser(), $testClosure, $accessCheckPages->getLoginClosure(), $accessCheckPages->getLogoutClosure())
//            ->addTest($I->getAnonymousUser());
//
//        $accessCheckPages->assert();
//    }
}
