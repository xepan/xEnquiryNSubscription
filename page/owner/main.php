<?php

class page_xEnquiryNSubscription_page_owner_main extends page_componentBase_page_owner_main {

	function init(){
		$this->rename('xEnMn');
		parent::init();

		$this->app->layout->template->trySetHTML('page_title','<i class="fa fa-bullhorn"></i> '.$this->component_name. '<small> Basic Subscription and Newsletter Management</small>');
		
		if(!$this->api->isAjaxOutput() and !$_GET['cut_page']){

			$xenq_m=$this->app->top_menu->addMenu($this->component_name);
			$xenq_m->addItem(array('Dashboard','icon'=>'gauge-1'),'xEnquiryNSubscription_page_owner_dashboard');
			$xenq_m->addItem(array('Subscriber Section','icon'=>'plus'),'xEnquiryNSubscription_page_owner_subscriptions');
			$xenq_m->addItem(array('Custom Form Section','icon'=>'plus'),'xEnquiryNSubscription_page_owner_form');
			$xenq_m->addItem(array('News Letters','icon'=>'plus'),'xEnquiryNSubscription_page_owner_subscriptions_newsletter');
		}
	}


	function page_config(){
		$this->add('H1')->set('Default Config Page');
	}
}