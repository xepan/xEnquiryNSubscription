<?php

namespace xEnquiryNSubscription;

class Model_SubscriptionCategoryAssociation extends \Model_Table {
	var $table= "xEnquiryNSubscription_SubsCatAss";
	
	function init(){
		parent::init();

		$f=$this->hasOne('xEnquiryNSubscription/SubscriptionCategories','category_id');
		$f->icon= 'fa fa-folder~red';
		$f=$this->hasOne('xEnquiryNSubscription/Subscription','subscriber_id')->display(array('form'=>'autocomplete/Plus'));
		$f->icon= 'fa fa-folder~red';

		$f=$this->addField('subscribed_on')->type('datetime')->defaultValue(date('Y-m-d H:i:s'))->group('a~4');
		$f->icon='fa fa-calander~blue';
		$f=$this->addField('last_updated_on')->type('datetime')->defaultValue(date('Y-m-d H:i:s'))->group('a~4');
		$f->icon='fa fa-calander~blue';
		
		$f=$this->addField('send_news_letters')->type('boolean')->defaultValue(true)->group('a~4');
		$f->icon='fa fa-exclamation~blue';

		$this->addHook('beforeSave',$this);

		$this->add('dynamic_model/Controller_AutoCreator');
	}

	function beforeSave(){
		$this['last_updated_on']=date('Y-m-d H:i:s');
	}
}