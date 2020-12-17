<?php

/* use Codeception\Util\Fixtures; */

use Codeception\Module\CodeHelper;
use AcceptanceTester;


class AcceptanceCest
{

    public function _before(AcceptanceTester $I)
    {}

    public function _after(AcceptanceTester $I)
    {}

    public function SignUpFormUserAdmin(AcceptanceTester $I)
    {
        $I->amOnPage('/user/add-admin');
        try {
            $I->seeElement('#form-add-admin');
            $I->amGoingTo('submit sign up form for admin user');
            $this->name = 'admin';
            $this->email = 'er.amudeep@gmail.com';
            $this->password = 'admin@123';
            Helper::$email = $this->email;
            Helper::$password = $this->password;
            $I->fillField('#user-full_name', $this->name);
            $I->fillField('#user-email', $this->email);
            $I->fillField('#user-password', $this->password);
            $I->fillField('#user-confirm_password', $this->password);
            $I->click('signup-button');
            $I->dontSeeElement('#form-signup');
        } catch (Exception $e) {
            return $e;
        }
    }
    

    public function LoginFormWorks(AcceptanceTester $I)
    {
        $I->amOnPage('/user/login');
        $I->seeElement('#login-form');
    }

    public function LogInFormCanBeSubmittedEmpty(AcceptanceTester $I)
    {
        $I->amOnPage('/user/login');
        $I->seeElement('#login-form');
        $I->amGoingTo('submit login form without credentials');
        $I->click('login-button');
        $I->expectTo('see validations errors');
        $req = $I->grabMultiple('.required');
        $count = count($req);
        $I->seeNumberOfElements('.has-error', $count);
    }

    public function LoginFormWithRightCredentials(AcceptanceTester $I)
    {
        $I->amOnPage('/user/login');
        $I->seeElement('#login-form');
        $I->amGoingTo('submit login form with right credentials');
        $this->email = 'er.amudeep@gmail.com';
        $this->password = 'admin@123';
        \Helper::$email = $this->email;
        \Helper::$password = $this->password;
        $I->fillField('#loginform-username', \Helper::$email);
        $I->fillField('#loginform-password', \Helper::$password);
        $I->click('login-button');
        // $I->seeElement ( 'span.brand-name' );
    }

    public function LoginFormWithWrongEmailFormat(AcceptanceTester $I)
    {
        $I->amOnPage('/user/login');
        $I->seeElement('#login-form');
        $I->amGoingTo('submit login form with wrong email');
        $I->fillField('#loginform-username', 'abc');
        $I->fillField('#loginform-password', 'wrong');
        $I->click('login-button');
        $I->expectTo('see validations errors');
        $I->seeElement('.has-error');
    }

    public function LoginFormWithWrongCredentials(AcceptanceTester $I)
    {
        $I->amOnPage('/user/login');
        $I->seeElement('#login-form');
        $I->amGoingTo('submit login form with wrong credentials');
        $I->fillField('#loginform-username', 'abc@gmail.com');
        $I->fillField('#loginform-password', 'wrong');
        $I->click('login-button');
        $I->expectTo('see validations errors');
        // $I->seeElement('.has-error');
    }

    public function PasswordRecoverFormWorks(AcceptanceTester $I)
    {
        $I->amOnPage('/user/login');
        $I->click('Forgot Password? ');
        $I->seeElement('#request-password-reset-form');
    }

    public function PasswordRecoverFormWithoutEmail(AcceptanceTester $I)
    {
        $I->amOnPage('/user/recover');
        $I->seeElement('#request-password-reset-form');
        $I->amGoingTo('submit email field without email');
        $I->click('send-button');
        $I->expectTo('see validations errors');
        $I->seeElement('.required');
    }
}