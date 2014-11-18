<?php

class page_xEnquiryNSubscription_page_owner_main extends page_componentBase_page_owner_main {

	function page_index(){
		// parent::init();

		$this->h1->setHTML('<i class="fa fa-bullhorn"></i> '.$this->component_name. '<small>Basic Subscription and Newsletter Management</small>');

		if(!$this->api->isAjaxOutput()){
			$mh_b=$this->toolbar->addButton('Module Home');
			$mh_b->setIcon('ui-icon-home');
			$mh_b->js('click')->univ()->redirect('xEnquiryNSubscription_page_owner_main');
			$tabs = $this->add('Tabs');
			$tabs->addTabUrl('xEnquiryNSubscription_page_owner_dashboard','Dashboard');
			$s_tab = $tabs->addTabUrl('xEnquiryNSubscription_page_owner_subscriptions','Subscription Section');
			$s_tab = $tabs->addTabUrl('xEnquiryNSubscription_page_owner_form','Custom Form Section');
			$news_letter_tab = $tabs->addTabURL($this->api->url('xEnquiryNSubscription_page_owner_subscriptions_newsletter'),'News Letters');
		}
	}

	function page_config(){
		$this->add('H1')->set('Default Config Page');
	}
}