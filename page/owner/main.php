<?php

class page_xEnquiryNSubscription_page_owner_main extends page_componentBase_page_owner_main {

	function page_index(){
		// parent::init();
		if(!$this->api->isAjaxOutput()){
			$this->toolbar->addButton('Module Home')->js('click')->univ()->redirect('xEnquiryNSubscription_page_owner_main');
			$tabs = $this->add('Tabs');
			$s_tab = $tabs->addTabUrl('xEnquiryNSubscription_page_owner_subscriptions','Subscription Section');
			$s_tab = $tabs->addTabUrl('xEnquiryNSubscription_page_owner_form','Custom Form Section');
		}
	}

	function page_config(){
		$this->add('H1')->set('Default Config Page');
	}
}