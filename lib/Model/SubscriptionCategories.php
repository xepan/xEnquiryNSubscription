<?php
namespace xEnquiryNSubscription;


class Model_SubscriptionCategories extends \Model_Table {
	var $table= "xEnquiryNSubscription_Subscription_Categories";
	function init(){
		parent::init();

		$this->hasOne('Epan','epan_id');
		$this->addField('name');
		$this->addField('is_active')->type('boolean')->defaultValue(true);

		$this->hasMany('xEnquiryNSubscription/Subscription','category_id');
		$this->hasMany('xEnquiryNSubscription/HostsTouched','category_id');

		$this->addExpression('total_emails')->set(function($m,$q){
			return $m->refSQL('xEnquiryNSubscription/Subscription')->count();
		})->type('int');
		
		$this->addHook('beforeSave',$this);
		$this->addHook('afterInsert',$this);

		$this->addCondition('epan_id',$this->api->current_website->id);

		// $this->add('dynamic_model/Controller_AutoCreator');

	}

	function beforeSave(){
		$this['name'] = trim($this['name']);
		$old_check = $this->add('xEnquiryNSubscription/Model_SubscriptionCategories');
		$old_check->addCondition('name',$this['name']);
		$old_check->addCondition('id','<>',$this->id);
		$old_check->tryLoadAny();
		if($old_check->loaded())
			throw $this->exception('Category Already Exists, Must be Unique', 'ValidityCheck')->setField('name');
	}

	function afterInsert($obj,$new_id){
		$config = $this->add('xEnquiryNSubscription/Model_SubscriptionConfig');
		$config['category_id'] = $new_id;
		$config->save();
	}

}