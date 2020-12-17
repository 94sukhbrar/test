<?php
 
class LoginHistoryAcceptanceCest 

{
	public $id;
	protected $data = [ ];
	
	public function _data(){
					/* $this->data['user_id'] = 1; */
			$this->data['user_ip'] = \Helper::faker()->text(10);
			$this->data['user_agent'] = \Helper::faker()->text(10);
			/* $this->data['failer_reason'] = \Helper::faker()->text(10); */
			$this->data['state_id'] = 0;
			$this->data['type_id'] = 0;
			$this->data['code'] = \Helper::faker()->text(10);
	}
	
	public function _before(AcceptanceTester $I) 
	{
			Helper::login($I);
	}
	public function _after(AcceptanceTester $I) {}

	public function IndexWorks(AcceptanceTester $I) 
	{
			$I->amOnPage ( '/login-history/index' );
			$I->canSeeResponseCodeIs(200);
			$I->seeElement ( '.grid-view' );
			$this->_data();
	}
	
	public function ViewWorks(AcceptanceTester $I) 
	{
			$I->amOnPage ( '/login-history/' . $this->id );
			$I->amGoingTo ( 'View login-history details' );
			$I->canSeeResponseCodeIs(200);
			$I->see ( array_key_exists('title',$this->data)?$this->data['title']:'', 'h3' );
			$I->seeElement ( '.table-bordered');
	}

	public function DeleteWorks(AcceptanceTester $I) 
	{
		$I->sendAjaxPostRequest ( '/login-history/delete/' . $this->id );
			$I->expectTo ( 'delete login-history works' );
			$I->amOnPage ( '/bulk-delete/' . $this->id );
			$I->canSeeResponseCodeIs(404);
	}
	

	
	
}
