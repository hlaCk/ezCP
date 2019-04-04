<?php

namespace hlaCk\ezCP\Tests;

use Illuminate\Support\Facades\Auth;

class LoginTest extends TestCase
{
    public function testSuccessfulLoginWithDefaultCredentials()
    {
        $this->visit(route('ezcp.login'))
             ->type('admin@admin.com', 'email')
             ->type('password', 'password')
             ->press(__('ezcp::generic.login'))
             ->seePageIs(route('ezcp.dashboard'));
    }

    public function testShowAnErrorMessageWhenITryToLoginWithWrongCredentials()
    {
        session()->setPreviousUrl(route('ezcp.login'));

        $this->visit(route('ezcp.login'))
             ->type('john@Doe.com', 'email')
             ->type('pass', 'password')
             ->press(__('ezcp::generic.login'))
             ->seePageIs(route('ezcp.login'))
             ->see(__('auth.failed'))
             ->seeInField('email', 'john@Doe.com');
    }

    public function testRedirectIfLoggedIn()
    {
        Auth::loginUsingId(1);

        $this->visit(route('ezcp.login'))
             ->seePageIs(route('ezcp.dashboard'));
    }

    public function testRedirectIfNotLoggedIn()
    {
        $this->visit(route('ezcp.profile'))
             ->seePageIs(route('ezcp.login'));
    }

    public function testCanLogout()
    {
        Auth::loginUsingId(1);

        $this->visit(route('ezcp.dashboard'))
             ->press(__('ezcp::generic.logout'))
             ->seePageIs(route('ezcp.login'));
    }

    public function testGetsLockedOutAfterFiveAttempts()
    {
        session()->setPreviousUrl(route('ezcp.login'));

        for ($i = 0; $i <= 5; $i++) {
            $t = $this->visit(route('ezcp.login'))
                 ->type('john@Doe.com', 'email')
                 ->type('pass', 'password')
                 ->press(__('ezcp::generic.login'));
        }

        $t->see(__('auth.throttle', ['seconds' => 60]));
    }
}
