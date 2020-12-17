<?php
 
class LogsAcceptanceCest 

{
	public $id;
	protected $data = [ ];
	
	public function _data(){
					$this->data['error'] = \Helper::faker()->text(10);
			/* $this->data['api'] = \Helper::faker()->text; */
			/* $this->data['description'] = \Helper::faker()->text; */
			/* $this->data['state_id'] = 0; */
			$this->data['link'] = \Helper::faker()->text(10);
			$this->data['type_id'] = 0;
	}
	
	public function _before(AcceptanceTester $I) 
	{
			Helper::login($I);
	}
	public function _after(AcceptanceTester $I) {}

	public function IndexWorks(AcceptanceTester $I) 
	{
			$I->amOnPage ( '/log/index' );
			$I->canSeeResponseCodeIs(200);
			$I->seeElement ( '.grid-view' );
			$this->_data();
	}
	 
	
	public function ViewWorks(AcceptanceTester $I) 
	{
			$I->amOnPage ( '/log/' . $this->id );
			$I->amGoingTo ( 'View log details' );
			$I->canSeeResponseCodeIs(200);
			$I->see ( array_key_exists('title',$this->data)?$this->data['title']:'', 'h3' );
			$I->seeElement ( '.table-bordered');
	}
	
	public function DeleteWorks(AcceptanceTester $I) 
	{
		$I->sendAjaxPostRequest ( '/log/delete/' . $this->id );
			$I->expectTo ( 'delete log works' );
			$I->amOnPage ( '/bulk-delete/' . $this->id );
			$I->canSeeResponseCodeIs(404);
	}
	
		
	public function ClearWorks(AcceptanceTester $I) 
	{
		$I->amOnPage ( '/log/clear' . $this->id );
		$I->amGoingTo ( 'check  Clear works ' );
		$I->canSeeResponseCodeIs(200);
		//$I->see ( 'title', 'h1' );
	}
	
	
	
}
