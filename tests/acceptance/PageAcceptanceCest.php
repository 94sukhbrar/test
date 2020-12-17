<?php
 
class PageAcceptanceCest 

{
	public $id;
	protected $data = [ ];
	
	public function _data(){
					$this->data['title'] = \Helper::faker()->text(10);
			$this->data['description'] = \Helper::faker()->text;
			$this->data['state_id'] = 0;
			$this->data['type_id'] = 0;
	}
	
	public function _before(AcceptanceTester $I) 
	{
			Helper::login($I);
	}
	public function _after(AcceptanceTester $I) {}

	public function IndexWorks(AcceptanceTester $I) 
	{
			$I->amOnPage ( '/page/index' );
			$I->canSeeResponseCodeIs(200);
			$I->seeElement ( '.grid-view' );
			$this->_data();
	}
	public function AddFormCanBeSubmittedEmpty(AcceptanceTester $I) 
	{
			$I->amOnPage ( '/page/add' );
			$I->seeElement ( '#page-form' );
			$I->amGoingTo ( 'add form without credentials' );
			$I->fillField ('#page-title',$this->data['title']);
			$I->fillField ('#page-description',$this->data['description'] );
			$I->selectOption ('#page-state_id',$this->data['state_id']);
			$I->selectOption ('#page-type_id',$this->data['type_id']);
			$I->click ( 'page-button' );
			$I->canSeeResponseCodeIs(200);
			$I->expectTo ( 'see validations errors' );
			$req = $I->grabMultiple ( '.required' );
			$count = count ( $req );
			$I->seeNumberOfElements ( '.has-error', $count );
	}
	public function AddWorksWithData(AcceptanceTester $I) 
	{
			$I->amOnPage ( '/page/add' );
			$I->seeElement ( '#page-form' );
			$I->amGoingTo ( 'add form with right data' );
			
			$I->fillField ('#page-title',$this->data['title']);
			$I->fillField ('#page-description',$this->data['description'] );
			$I->selectOption ('#page-state_id',$this->data['state_id']);
			$I->selectOption ('Page[type_id]',$this->data['type_id']);
			$I->click ( 'page-button' );
			$I->canSeeResponseCodeIs(200);
			$I->dontseeElement ( '#page-form' );
			$I->see (array_key_exists('title',$this->data)?$this->data['title']:'', 'h3'  );
			$I->seeElement ( '.table-bordered');
			$this->id = $I->grabFromCurrentUrl ( '/[=\/](\d+)/' );
	}
	
	public function ViewWorks(AcceptanceTester $I) 
	{
			$I->amOnPage ( '/page/' . $this->id );
			$I->amGoingTo ( 'View page details' );
			$I->canSeeResponseCodeIs(200);
			$I->see ( array_key_exists('title',$this->data)?$this->data['title']:'', 'h3' );
			$I->seeElement ( '.table-bordered');
	}
	public function UpdateWorks(AcceptanceTester $I) 
{
			$I->amOnPage ( '/page/update/'. $this->id  );
			$I->seeElement ( '#page-form' );
			$I->amGoingTo ( 'add form with right data' );
			
			$I->fillField ('Page[title]',$this->data['title']);
			$I->fillField ('Page[description]',$this->data['description'] );
			$I->selectOption ('Page[state_id]',$this->data['state_id']);
			$I->selectOption ('Page[type_id]',$this->data['type_id']);
			$I->click ( 'page-button' );
			$I->canSeeResponseCodeIs(200);
			$I->dontseeElement ( '#page-form' );
			$I->see (array_key_exists('title',$this->data)?$this->data['title']:'', 'h3'  );
		$I->seeElement ( '.table-bordered');
	}
	public function DeleteWorks(AcceptanceTester $I) 
	{
		$I->sendAjaxPostRequest ( '/page/delete/' . $this->id );
			$I->expectTo ( 'delete page works' );
			$I->amOnPage ( '/page/delete' . $this->id );
			$I->canSeeResponseCodeIs(404);
	}
	
		
	public function SaveWorks(AcceptanceTester $I) 
	{
		$I->amOnPage ( '/page/save/' . $this->id );
		$I->amGoingTo ( 'check  Save works ' );
		$I->canSeeResponseCodeIs(200);
		//$I->see ( 'title', 'h1' );
	}
	
		
	public function MassWorks(AcceptanceTester $I) 
	{
		$I->amOnPage ( '/page/save/' . $this->id );
		$I->amGoingTo ( 'check  Mass works ' );
		$I->canSeeResponseCodeIs(200);
		//$I->see ( 'title', 'h1' );
	}
	
	
}
