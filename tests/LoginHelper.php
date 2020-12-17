<?php

class LoginHelper {
	public static $email;
	public static $password;
	
	public function login(AcceptanceTester $I) {
		$I->amOnPage ( '/user/login' );
		$I->seeElement ( '#login-form' );
		$I->amGoingTo ( 'submit login form with right credentials' );
		$I->fillField ( '#loginform-username', self::$email );
		$I->fillField ( '#loginform-password', self::$password );
		$I->click ( 'login-button' );
		$I->dontseeElement ( '#login-form' );
	}
	public static function randomName() {
		{
			$char = "abcdefghijklmnopqrstuvwxyz";
			$ulen = mt_rand ( 5, 10 );
			
			$a = "";
			for($i = 1; $i <= $ulen; $i ++) {
				$a .= substr ( $char, mt_rand ( 0, strlen ( $char ) ), 1 );
			}
			
			return ucfirst ( $a );
		}
	}
	public static function randomMail() {
		{
			$char = "0123456789abcdefghijklmnopqrstuvwxyz";
			$ulen = mt_rand ( 5, 10 );
			
			$a = "";
			for($i = 1; $i <= $ulen; $i ++) {
				$a .= substr ( $char, mt_rand ( 0, strlen ( $char ) ), 1 );
			}
			
			$a .= "@test.in";
			return $a;
		}
	}
}