<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Tests\DataFixtures\Helper\DefaultUserPasswordGenerator;
use App\Tests\Support\TransferObject\User;

/**
 * Inherited Methods
 * @method void wantTo($text)
 * @method void wantToTest($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause($vars = [])
 *
 * @SuppressWarnings(PHPMD)
*/
class AcceptanceTester extends \Codeception\Actor
{
    use _generated\AcceptanceTesterActions;

    private $authorized = false;

    public function getAnonymousUser(): User
    {
        return new User();
    }

    public function authAs(User $user, bool $remember = false): void
    {
        if ($this->isAuthorized()) {
            $this->logout();
        }

        $this->amOnPage('/login/');

        $this->fillField(['id' => 'username'], $user->email);
        $this->fillField(['id' => 'password'], DefaultUserPasswordGenerator::DEFAULT_USER_PASSWORD);

//        if ($remember === false) {
//            $this->uncheckOption('[name="login[remember]"]');
//        } else {
//            $this->checkOption('[name="login[remember]"]');
//        }

        $this->click('//form/*[@type="submit"]');

        $this->authorized = true;
    }

    public function isAuthorized(): bool
    {
        return $this->authorized;
    }

    public function logout(): void
    {
        if (!$this->authorized) {
            return;
        }
        $this->amOnPage('/logout/');
        $this->authorized = false;
    }
}
