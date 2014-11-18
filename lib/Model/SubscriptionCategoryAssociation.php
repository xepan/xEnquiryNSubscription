<?php

namespace xEnquiryNSubscription;

class Model_SubscriptionCategoryAssociation extends \Model_Table {
	var $table= "xEnquiryNSubscription_SubsCatAss";
	
	function init(){
		parent::init();

		$this->hasOne('xEnquiryNSubscription/SubscriptionCategories','category_id');
		$this->hasOne('xEnquiryNSubscription/Subscription','subscriber_id')->display(array('form'=>'autocomplete/Plus'));

		$this->addField('subscribed_on')->type('datetime')->defaultValue(date('Y-m-d H:i:s'));
		$this->addField('last_updated_on')->type('datetime')->defaultValue(date('Y-m-d H:i:s'));
		
		$this->addField('send_news_letters')->type('boolean')->defaultValue(true);

		$this->addHook('beforeSave',$this);

		$this->add('dynamic_model/Controller_AutoCreator');
	}

	function beforeSave(){
		$this['last_updated_on']=date('Y-m-d H:i:s');
	}
}