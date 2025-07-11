<?php

declare(strict_types=1);

namespace Tests\Acceptance;

use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class DashboardAccessTest extends KernelTestCase
{
    use HasBrowser, ResetDatabase, Factories;

    public function testAccessForAnonymousUser(): void
    {
        $this->browser()
            ->visit('/admin')
            ->assertOn('/login')
        ;
    }

    public function testAccessForAdmin(): void
    {
        $admin = UserFactory::new()->admin()->create();

        $this->browser()
            ->actingAs($admin)
            ->visit('/admin')
            ->assertSee('Admin Dashboard')
        ;
    }

    public function testDenyAccessForCustomer(): void
    {
        $customer = UserFactory::new()->customer()->create();

        $this->browser()
            ->actingAs($customer)
            ->assertAuthenticated()
            ->visit('/admin')
            ->assertSee('Произошла ошибка 403')
            ->assertStatus(403)
        ;
    }
}
