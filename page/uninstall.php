<?php

class page_xEnquiryNSubscription_page_uninstall extends page_componentBase_page_uninstall{

	function init(){
		parent::init();
		// Before Uninstall
		$this->uninstall();
		// After Uninstall
	}

} 