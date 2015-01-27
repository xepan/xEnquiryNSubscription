<?php
namespace xEnquiryNSubscription;


class Model_Subscription extends \Model_Table {
	var $table= "xEnquiryNSubscription_Subscription";
	function init(){
		parent::init();

		$this->hasOne('Epan','epan_id')->system(true);
		$this->addCondition('epan_id',$this->api->current_website->id);
		
		// $this->hasOne('xEnquiryNSubscription/SubscriptionCategories','category_id');

		$f=$this->addField('email')->mandatory(true)->group('a~10')->sortable(true);
		$f->icon='fa fa-envelope~blue';

		$f=$this->addField('is_ok')->type('boolean')->defaultValue(1)->group('a~2')->sortable(true);
		$f->icon='fa fa-exclamation~blue';
		
		$f=$this->addField('ip')->caption('IP')->group('b~6');
		$f->icon = 'fa fa-desktop~blue';
		$f=$this->addField('created_at')->type('datetime')->defaultValue(date('Y-m-d H:i:s'))->group('b~6')->sortable(true);
		$f->icon ='fa fa-calendar~blue';
		// $this->addField('send_news_letters')->type('boolean')->defaultValue(true);


		$this->addField('from_app')->system(true)->defaultValue('xEnquiryNSubscription')->sortable(true);
		$this->addField('from_id')->type('int')->system(true);

		$this->addExpression('name')->set('email');
		$this->hasMany('xEnquiryNSubscription/SubscriptionCategoryAssociation','subscriber_id');

		$this->addHook('beforeSave',$this);
		$this->addHook('beforeDelete',$this);

		$this->add('dynamic_model/Controller_AutoCreator');

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
	function beforeDelete(){
		$this->ref('xEnquiryNSubscription/SubscriptionCategoryAssociation')->deleteAll();
	}
}