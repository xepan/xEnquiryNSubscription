<?php

namespace xEnquiryNSubscription;


class Plugins_RegisterEvent extends \componentBase\Plugin {

	function init(){
		parent::init();
		$this->addHook('register-event',array($this,'Plugins_RegisterEvent'));
	}

	function Plugins_RegisterEvent($obj, $events_obj){
		$events_obj->events[] = "xenq_n_subs_newletter_before_delete";
		$events_obj->events[] = "xenq-n-subs-custom-form-submit";
	}
}
