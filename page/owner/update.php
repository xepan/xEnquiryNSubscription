<?php

class page_xEnquiryNSubscription_page_owner_update extends page_componentBase_page_update {
	
	public $git_path = 'https://github.com/xepan/xEnquiryNSubscription';

	function init(){
		parent::init();
		$this->update();
		$this->add('View_Info')->set('Component Is SuccessFully Updated');
		// Code to run after installation
	}
}