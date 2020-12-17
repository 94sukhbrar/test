<?php
class UserCest
{
	public function signup(ApiTester $I,$scenario) {
		$scenario->incomplete();
		$I = new AcceptanceTester($scenario);
		$I->wantTo ( 'create a user via API' );
		$this->name = LoginHelper::randomName ();
		$this->email = LoginHelper::randomMail ();
		LoginHelper::$email = $this->email ;
		LoginHelper::$password = 'test@123';
		$I->sendPOST( '/user/signup', [ 
				'User[full_name]' => $this->name ,
				'User[email]' => LoginHelper::$email,
				'User[password]' => LoginHelper::$password,
				'User[contact_no]' => '9876543210' 
		] );
		$I->seeResponseContainsJson ( ["status"=>200]);
	}
	public function login(ApiTester $I,$scenario) {
	 	$scenario->incomplete();
	 	$I = new AcceptanceTester($scenario);
		$I->wantTo ( 'login a user via API' );
		$I->sendPOST ( '/user/login', [
				' LoginForm[username]' => LoginHelper::$email,
				'LoginForm[password]' =>  LoginHelper::$password,
				'LoginForm[device_token]' => '12121632',
				'LoginForm[device_type]' => '1'
		] );
		$I->seeResponseContainsJson ( ["status"=>200]);
	} 
	/* public function passwordChange(ApiTester $I) {
		$I->wantTo ( 'change password for a login user via API' );
		$I->sendPOST ( '/user/change-password', [
				'User[oldPassword]' => 'shellyphp',
				'User[newPassword]' => 'shelly@123',
				'User[confirm_password]' => 'shelly@123',
				
		] );
	} */
	
}
