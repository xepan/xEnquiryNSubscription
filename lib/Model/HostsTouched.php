<?php

namespace xEnquiryNSubscription;

class Model_HostsTouched extends \Model_Table{
	public $table ='xEnquiryNSubscription_hosts_touched';

	function init(){
		parent::init();

		$this->hasOne('xEnquiryNSubscription/SubscriptionCategories','category_id');
		$this->addField('name');
		// $this->add('dynamic_model/Controller_AutoCreator');
	}

}