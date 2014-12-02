<?php

class page_xEnquiryNSubscription_page_owner_main extends page_componentBase_page_owner_main {

	function init(){
		parent::init();
		$this->rename('xEnMn');

		$this->h1->setHTML('<i class="fa fa-bullhorn"></i> '.$this->component_name. '<small>Basic Subscription and Newsletter Management</small>');
		
		if(!$this->api->isAjaxOutput() and !$_GET['cut_page']){
			$mh_b=$this->toolbar->addButton('Module Home');
			$mh_b->setIcon('ui-icon-home');
			$mh_b->js('click')->univ()->redirect('xEnquiryNSubscription_page_owner_main');
			
			$menu=$this->add('Menu');

			$dashboard = $menu->addMenuItem('xEnquiryNSubscription_page_owner_dashboard','Dashboard');
			$subs = $menu->addSubMenu('Subscription Section  <i class="fa fa-user"></i>');
			
			$subs->addMenuItem('xEnquiryNSubscription_page_owner_subscriptions_categories','Categories');
			$subs->addMenuItem('xEnquiryNSubscription_page_owner_subscriptions_total_subscriptions','Total Subscribers');

			$cust_form = $menu->addMenuItem('xEnquiryNSubscription_page_owner_form','Custom Forms');
			$cust_form = $menu->addMenuItem('xEnquiryNSubscription_page_owner_subscriptions_newsletter','News Letters');


		}
	}


	function page_config(){
		$this->add('H1')->set('Default Config Page');
	}
}