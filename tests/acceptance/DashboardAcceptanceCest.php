<?php
 
class DashboardAcceptanceCest 

{
	public $id;
	protected $data = [ ];
	
	public function _data(){
			}
	
	public function _before(AcceptanceTester $I) 
	{
			Helper::login($I);
	}
	public function _after(AcceptanceTester $I) {}

	 public function IndexWorks(AcceptanceTester $I) 
	{
			$I->amOnPage ( '/dashboard/index' );
			$I->canSeeResponseCodeIs(200);
			$I->seeElement ( '.grid-view' );
			$this->_data(); 
	}  
	
	
	public function ViewWorks(AcceptanceTester $I) 
	{
			$I->amOnPage ( '/dashboard/' . $this->id );
			$I->amGoingTo ( 'View dashboard details' );
			$I->canSeeResponseCodeIs(200);
			$I->see ( array_key_exists('title',$this->data)?$this->data['title']:'', 'h1' );
			$I->seeElement ( '.table-bordered');
	}
	/* public function UpdateWorks(AcceptanceTester $I) 
{
			$I->amOnPage ( '/dashboard/index/'. $this->id  );
			$I->seeElement ( '#dashboard-form' );
			$I->amGoingTo ( 'add form with right data' );
			
			$I->click ( '#user-form-submit' );
			$I->canSeeResponseCodeIs(200);
			$I->dontseeElement ( '#dashboard-form' );
			$I->see (array_key_exists('Users',$this->data)?$this->data['Users']:'', 'h1'  );
		$I->seeElement ( '.table-bordered');
	} */
	public function DeleteWorks(AcceptanceTester $I) 
	{
		$I->sendAjaxPostRequest ( '/bulk-delete/' . $this->id );
			$I->expectTo ( 'delete dashboard works' );
			$I->amOnPage ( '/bulk-delete/' . $this->id );
			$I->canSeeResponseCodeIs(404);
	}
	
	
}
