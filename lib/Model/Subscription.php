<?php
namespace xEnquiryNSubscription;


class Model_Subscription extends \Model_Table {
	var $table= "xEnquiryNSubscription_Subscription";
	function init(){
		parent::init();

		$this->hasOne('Epan','epan_id');
		$this->addCondition('epan_id',$this->api->current_website->id);
		
		$this->hasOne('xEnquiryNSubscription/SubscriptionCategories','category_id');

		$this->addField('email')->mandatory(true);
		$this->addField('ip')->caption('IP');
		$this->addField('subscribed_on')->type('datetime')->defaultValue(date('Y-m-d H:i:s'));
		$this->addField('send_news_letters')->type('boolean')->defaultValue(true);

		$this->addExpression('name')->set('email');

		$this->addHook('beforeSave',$this);

		// $this->add('dynamic_model/Controller_AutoCreator');

	}

	function beforeSave(){
		if(!$this['ip']){
			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			    $ip = $_SERVER['HTTP_CLIENT_IP'];
			} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
			    $ip = $_SERVER['REMOTE_ADDR'];
			}
			
			$this['ip'] = $ip;
		}

	}
}