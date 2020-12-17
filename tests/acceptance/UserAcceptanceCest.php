<?php
 
class UserAcceptanceCest 

{
	public $id;
	protected $data = [ ];
	
	public function _data(){
			$this->data['full_name'] = \Helper::faker()->name;
			$this->data['email'] = \Helper::faker()->email;
			/* $this->data['date_of_birth'] = 2018-05-21 17:01:03; */
			/* $this->data['gender'] = \Helper::faker()->text(10); */
			/* $this->data['about_me'] = \Helper::faker()->text(10); */
			$this->data['contact_no'] = \Helper::faker()->text(10);
			/* $this->data['address'] = \Helper::faker()->text(10); */
			/* $this->data['latitude'] = \Helper::faker()->text(10); */
			/* $this->data['longitude'] = \Helper::faker()->text(10); */
			/* $this->data['city'] = \Helper::faker()->text(10); */
			/* $this->data['country'] = \Helper::faker()->text(10); */
			/* $this->data['zipcode'] = \Helper::faker()->text(10); */
			/* $this->data['language'] = \Helper::faker()->text(10); */
			/* $this->data['email_verified'] = \Helper::faker()->boolean; */
			/* $this->data['profile_file'] = \Helper::faker()->text(10); */
			/* $this->data['tos'] = \Helper::faker()->text(10); */
			//$this->data['role_id'] = 1;
			//$this->data['state_id'] = 0;
			/* $this->data['type_id'] = 0; */
			/* $this->data['last_visit_time'] = 2018-05-21 17:01:03; */
			/* $this->data['last_action_time'] = 2018-05-21 17:01:03; */
			/* $this->data['last_password_change'] = 2018-05-21 17:01:03; */
			/* $this->data['login_error_count'] = \Helper::faker()->text(10); */
			/* $this->data['access_token'] = \Helper::faker()->text(10); */
			/* $this->data['timezone'] = \Helper::faker()->text(10); */
	}
	
	public function _before(AcceptanceTester $I) 
	{
			Helper::login($I);
	}
	public function _after(AcceptanceTester $I) {}

	public function IndexWorks(AcceptanceTester $I) 
	{
			$I->amOnPage ( '/user/index' );
			$I->canSeeResponseCodeIs(200);
			$I->seeElement ( '.grid-view' );
			$this->_data();
	}
	public function AddFormCanBeSubmittedEmpty(AcceptanceTester $I) 
	{
			$I->amOnPage ( '/user/add' );
			$I->seeElement ( '#user-form' );
			$I->amGoingTo ( 'add form without credentials' );
			$I->click ( '#user-form-submit' );
			$I->canSeeResponseCodeIs(200);
			$I->expectTo ( 'see validations errors' );
			$req = $I->grabMultiple ( '.required' );
			$count = count ( $req );
			$I->seeNumberOfElements ( '.has-error', $count );
	}
	public function AddWorksWithData(AcceptanceTester $I) 
	{
			$I->amOnPage ( '/user/add' );
			$I->seeElement ( '#user-form' );
			$I->amGoingTo ( 'add form with right data' );
			$I->fillField ('#user-full_name','test');
			$I->fillField ('#user-email',$this->data['email']);
			$I->fillField ('#user-password','admin123');
			$I->fillField ('#user-confirm_password','admin123');
		     	
			/*$I->fillField ('User[date_of_birth]',$this->data['date_of_birth']); */
			/*$I->fillField ('User[gender]',$this->data['gender']); */
			/*$I->fillField ('User[about_me]',$this->data['about_me']); */
			$I->fillField ('#user-contact_no',$this->data['contact_no']);
			/*$I->fillField ('User[address]',$this->data['address']); */
			/*$I->fillField ('User[latitude]',$this->data['latitude']); */
			/*$I->fillField ('User[longitude]',$this->data['longitude']); */
			/*$I->fillField ('User[city]',$this->data['city']); */
			/*$I->fillField ('User[country]',$this->data['country']); */
			/*$I->fillField ('User[zipcode]',$this->data['zipcode']); */
			/*$I->fillField ('User[language]',$this->data['language']); */
			/*$I->fillField ('User[email_verified]',$this->data['email_verified']); */
			/*$I->attachFile('User[profile_file]',''); */
			//$I->fillField ('User[tos]',$this->data['tos']); */
			// $I->fillField ('#User role_id',$this->data['role_id']); 
			// $I->selectOption ('User[state_id]',$this->data['state_id']);
			/*$I->selectOption ('User[type_id]',$this->data['type_id']); */
			/*$I->fillField ('User[last_visit_time]',$this->data['last_visit_time']); */
			/*$I->fillField ('User[last_action_time]',$this->data['last_action_time']); */
			/*$I->fillField ('User[last_password_change]',$this->data['last_password_change']); */
			/*$I->fillField ('User[login_error_count]',$this->data['login_error_count']); */
			/*$I->fillField ('User[access_token]',$this->data['access_token']); */
			/*$I->fillField ('User[timezone]',$this->data['timezone']); */
			$I->click ( '#user-form-submit' );
			$I->canSeeResponseCodeIs(200);
			$I->dontseeElement ( '#user-form' );
			$I->see (array_key_exists('Users',$this->data)?$this->data['Users']:'', 'h3'  );
			$I->seeElement ( '.table-bordered');
			$this->id = $I->grabFromCurrentUrl ( '/[=\/](\d+)/' );
	}
	
	public function ViewWorks(AcceptanceTester $I) 
	{
			$I->amOnPage ( '/user/' . $this->id );
			$I->amGoingTo ( 'View user details' );
			$I->canSeeResponseCodeIs(200);
			$I->see ( array_key_exists('Users',$this->data)?$this->data['Users']:'', 'h3' );
			$I->seeElement ( '.table-bordered');
	}
	public function UpdateWorks(AcceptanceTester $I) 
{
			$I->amOnPage ( '/user/update/'. $this->id  );
			$I->seeElement ( '#user-form' );
			$I->amGoingTo ( 'add form with right data' );
			$I->fillField ('#user-full_name',$this->data['full_name']);
			$I->fillField ('#user-email',$this->data['email']);
			/*$I->fillField ('User[date_of_birth]',$this->data['date_of_birth']); */
			/*$I->fillField ('User[gender]',$this->data['gender']); */
			/*$I->fillField ('User[about_me]',$this->data['about_me']); */
			$I->fillField ('#user-contact_no',$this->data['contact_no']);
			/*$I->fillField ('User[address]',$this->data['address']); */
			/*$I->fillField ('User[latitude]',$this->data['latitude']); */
			/*$I->fillField ('User[longitude]',$this->data['longitude']); */
			/*$I->fillField ('User[city]',$this->data['city']); */
			/*$I->fillField ('User[country]',$this->data['country']); */
			/*$I->fillField ('User[zipcode]',$this->data['zipcode']); */
			/*$I->fillField ('User[language]',$this->data['language']); */
			/*$I->fillField ('User[email_verified]',$this->data['email_verified']); */
			/*$I->attachFile('User[profile_file]',''); */
			/*$I->fillField ('User[tos]',$this->data['tos']); */
			//$I->fillField ('User[role_id]',$this->data['role_id']);
	        //$I->selectOption ('User[state_id]',$this->data['state_id']) ; 
			/*$I->selectOption ('User[type_id]',$this->data['type_id']); */
			/*$I->fillField ('User[last_visit_time]',$this->data['last_visit_time']); */
			/*$I->fillField ('User[last_action_time]',$this->data['last_action_time']); */
			/*$I->fillField ('User[last_password_change]',$this->data['last_password_change']); */
			/*$I->fillField ('User[login_error_count]',$this->data['login_error_count']); */
			/*$I->fillField ('User[access_token]',$this->data['access_token']); */
			/*$I->fillField ('User[timezone]',$this->data['timezone']); */
			$I->click ( '#user-form-submit' );
			$I->canSeeResponseCodeIs(200);
			$I->dontseeElement ( '#user-form' );
			$I->see (array_key_exists('Users',$this->data)?$this->data['Users']:'', 'h3'  );
		$I->seeElement ( '.table-bordered');
	}
	public function DeleteWorks(AcceptanceTester $I) 
	{
		$I->sendAjaxPostRequest ( '/bulk-delete/' . $this->id );
			$I->expectTo ( 'delete user works' );
			$I->amOnPage ( '/bulk-delete/' . $this->id );
			$I->canSeeResponseCodeIs(404);
	}
	
		

}