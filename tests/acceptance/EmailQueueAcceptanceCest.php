<?php
 
class EmailQueueAcceptanceCest 

{
	public $id;
	protected $data = [ ];
	
	public function _data(){
					$this->data['from_email'] = \Helper::faker()->text(10);
			 $this->data['to_email'] = \Helper::faker()->text(10); 
			/* $this->data['message'] = \Helper::faker()->text; */
			/* $this->data['subject'] = \Helper::faker()->text(10); */
			/* $this->data['date_published'] = 2018-05-23 10:47:58; */
			/* $this->data['last_attempt'] = 2018-05-23 10:47:58; */
			/* $this->data['date_sent'] = 2018-05-23 10:47:58; */
			/* $this->data['attempts'] = \Helper::faker()->text(10); */
			/* $this->data['state_id'] = 0; */
			/* $this->data['model_id'] = 1; */
			/* $this->data['model_type'] = \Helper::faker()->text(10); */
			/* $this->data['email_account_id'] = 1; */
			/* $this->data['message_id'] = 1; */
	}
	
	public function _before(AcceptanceTester $I) 
	{
			Helper::login($I);
	}
	public function _after(AcceptanceTester $I) {}

	public function IndexWorks(AcceptanceTester $I) 
	{
			$I->amOnPage ( '/email-queue/index' );
			$I->canSeeResponseCodeIs(200);
			$I->seeElement ( '.grid-view' );
			$this->_data();
	}
	
	public function DeleteWorks(AcceptanceTester $I) 
	{
		$I->sendAjaxPostRequest ( '/email-queue/index/' . $this->id );
			$I->expectTo ( 'delete email-queue works' );
			$I->amOnPage ( '/bulk-delete' . $this->id );
			$I->canSeeResponseCodeIs(404);
	}
}