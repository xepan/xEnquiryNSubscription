<?php

namespace xEnquiryNSubscription;

class Model_SubscriptionCategoryAssociation extends \Model_Table {
	var $table= "xEnquiryNSubscription_SubsCatAss";
	
	function init(){
		parent::init();

		$f=$this->hasOne('xEnquiryNSubscription/SubscriptionCategories','category_id')->sortable(true);
		$f->icon= 'fa fa-folder~red';
		$f=$this->hasOne('xEnquiryNSubscription/Subscription','subscriber_id')->display(array('form'=>'autocomplete/Plus'))->sortable(true);
		$f->icon= 'fa fa-folder~red';

		$f=$this->addField('subscribed_on')->type('datetime')->defaultValue(date('Y-m-d H:i:s'))->group('a~4')->sortable(true);
		$f->icon='fa fa-calander~blue';
		$f=$this->addField('last_updated_on')->type('datetime')->defaultValue(date('Y-m-d H:i:s'))->group('a~4')->sortable(true);
		$f->icon='fa fa-calander~blue';
		
		$f=$this->addField('send_news_letters')->type('boolean')->defaultValue(true)->group('a~4')->sortable(true);
		$f->icon='fa fa-exclamation~blue';

		$this->addField('unsubscribed_on')->type('datetime')->defaultValue(null)->sortable(true);

		$this->addExpression('from_app')->set(function($m,$q){
			return $m->refSQL('subscriber_id')->fieldQuery('from_app');
		})->sortable(true);


		$this->addHook('beforeSave',$this);

		// $this->add('dynamic_model/Controller_AutoCreator');
	}

	function beforeSave(){
		$temp=$this->add('xEnquiryNSubscription/Model_SubscriptionCategoryAssociation');
		$temp->addCondition('category_id',$this['category_id']);
		$temp->addCondition('subscriber_id',$this['subscriber_id']);
		$temp->addCondition('id','<>',$this['id']);
		$temp->tryLoadAny();
		if($temp->loaded())
			throw $this->exception("Already Associated","ValidityCheck")->setField('subscriber_id');
		$this['last_updated_on']=date('Y-m-d H:i:s');

		if($this->dirty['send_news_letters'] and !$this['send_news_letters'])
			$this['unsubscribed_on'] = date('Y-m-d H:i:s');

	}

}