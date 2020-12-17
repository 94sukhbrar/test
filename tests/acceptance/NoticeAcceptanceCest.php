<?php
 
class NoticeAcceptanceCest 

{
	public $id;
	protected $data = [ ];
	
	public function _data(){
					$this->data['title'] = \Helper::faker()->text(10);
			$this->data['content'] = \Helper::faker()->text;
			/* $this->data['model_type'] = \Helper::faker()->text(10); */
/* 			$this->data['model_id'] = 1; */
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
			$I->amOnPage ( '/notice/index' );
			$I->canSeeResponseCodeIs(200);
			$I->seeElement ( '.grid-view' );
			$this->_data();
	}
	public function AddFormCanBeSubmittedEmpty(AcceptanceTester $I) 
	{
			$I->amOnPage ( '/notice/add' );
			$I->seeElement ( '#notice-form' );
			$I->amGoingTo ( 'add form without credentials' );
			$I->click ( '#notice-form-submit' );
			$I->canSeeResponseCodeIs(200);
			$I->expectTo ( 'see validations errors' );
			$req = $I->grabMultiple ( '.required' );
			$count = count ( $req );
			$I->seeNumberOfElements ( '.has-error', $count );
	}
	public function AddWorksWithData(AcceptanceTester $I) 
	{
			$I->amOnPage ('/notice/add');
			$I->seeElement ('#notice-form');
			$I->amGoingTo ( 'add form with right data' );
			
			$I->fillField ('#notice-title',$this->data['title']);
			$I->fillField ('#notice-content',$this->data['content'] );
			/*$I->fillField ('Notice[model_type]',$this->data['model_type']); */
			//$I->fillField ('Notice[model_id]',$this->data['model_id']);
			$I->selectOption ('#notice-state_id',$this->data['state_id']);
			$I->selectOption ('#notice-type_id',$this->data['type_id']);
			$I->click ( '#notice-form-submit' );
			$I->canSeeResponseCodeIs(200);
			$I->dontseeElement ('#notice-form');
			$I->see (array_key_exists('title',$this->data)?$this->data['title']:'', 'h3'  );
			$I->seeElement ( '.table-bordered');
			$this->id = $I->grabFromCurrentUrl ( '/[=\/](\d+)/' );
	}
	
	 public function ViewWorks(AcceptanceTester $I) 
	{
			$I->amOnPage ( '/notice/view/' . $this->id );
			$I->amGoingTo ( 'View notice details' );
			$I->canSeeResponseCodeIs(200);
			$I->see ( array_key_exists('Minus qui.',$this->data)?$this->data['Minus qui.']:'', 'h3' );
			$I->seeElement ( '.table-bordered');
	}  
	public function UpdateWorks(AcceptanceTester $I) 
{
			$I->amOnPage ( '/notice/update/'. $this->id  );
			$I->seeElement ( '#notice-form' );
			$I->amGoingTo ( 'add form with right data' );
			
			$I->fillField ('#notice-title',$this->data['title']);
			//$I->fillField ('#cke_notice-content',$this->data['content'] ); 
			/*$I->fillField ('Notice[model_type]',$this->data['model_type']); */
		/* 	$I->fillField ('Notice[model_id]',$this->data['model_id']); */
			$I->selectOption ('#notice-state_id',$this->data['state_id']);
			$I->selectOption ('#notice-type_id',$this->data['type_id']);
			$I->click ( '#notice-form-submit' );
			$I->canSeeResponseCodeIs(200);
			$I->dontseeElement ( '#notice-form' );
			$I->see (array_key_exists('title',$this->data)?$this->data['title']:'', 'h3'  );
		$I->seeElement ( '.table-bordered');
	}
	public function DeleteWorks(AcceptanceTester $I) 
	{
		$I->sendAjaxPostRequest ( '/notice/delete/' . $this->id );
			$I->expectTo ( 'delete notice works' );
			$I->amOnPage ( '/bulk_delete_notice-grid' . $this->id );
			$I->canSeeResponseCodeIs(404);
	}
	
	
}
	
	

 