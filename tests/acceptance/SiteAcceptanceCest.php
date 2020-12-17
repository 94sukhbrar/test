<?php
class SiteAcceptanceCest {
    public function _before(AcceptanceTester $I) {
    }
    public function _after(AcceptanceTester $I) {
    }
    
    // tests
    public function Homepage(AcceptanceTester $I) {
        $I->wantTo ( 'frontpage works' );
        $I->amOnPage ( '/' );
        $I->see ( 'Admin Project', 'h3' );
    }
   
}

