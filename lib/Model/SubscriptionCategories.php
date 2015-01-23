<?php
namespace xEnquiryNSubscription;


class Model_SubscriptionCategories extends \Model_Table {
	var $table= "xEnquiryNSubscription_Subscription_Categories";
	function init(){
		parent::init();

		$this->hasOne('Epan','epan_id')->system(true);
		$f=$this->addField('name')->mandatory(true)->group('a~8')->sortable(true);
		$f->icon='fa fa-folder~red';
		$f=$this->addField('is_active')->type('boolean')->defaultValue(true)->group('a~4')->sortable(true);
		$f->icon='fa fa-exclamation~blue';

		$this->hasMany('xEnquiryNSubscription/HostsTouched','category_id');
		$this->hasMany('xEnquiryNSubscription/Model_SubscriptionCategoryAssociation','category_id');

		$this->addExpression('total_emails')->set(function($m,$q){
			$mq=$m->add('xEnquiryNSubscription/Model_Subscription',array('table_alias'=>'tmq'));
			// $s_j=$mq->join('xEnquiryNSubscription_Subscription','subscriber_id');
			$as_j=$mq->join('xEnquiryNSubscription_SubsCatAss.subscriber_id');
			$as_j->addField('category_id');

			$mq->addCondition('category_id',$q->getField('id'));
			// $mq->addCondition('from_app','xEnquiryNSubscription');
			return $mq->count();
			
			// return $m->refSQL('xEnquiryNSubscription/Model_SubscriptionCategoryAssociation')->count();
		})->type('int')->sortable(true)->caption('Total Assos Emails');
		
		$this->addHook('beforeSave',$this);
		$this->addHook('afterInsert',$this);
		$this->addHook('beforeDelete',$this);

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

	function hasSubscriber($subscriber){
		if(!$this->loaded()) throw $this->exception('Must be called on loaded Subscriber Category Model');
		if($subscriber instanceof Subscription) throw $this->exception('Subscriber Must be instance of Subscription Model');

		if(!$subscriber->loaded()) throw $this->exception('Subscriber Must be LOADED instance of Subscription Model');

		$asso = $this->add('xEnquiryNSubscription/Model_SubscriptionCategoryAssociation');
		$asso->addCondition('category_id',$this->id);
		$asso->addCondition('subscriber_id',$subscriber->id);
		$asso->tryLoadAny();

		if($asso->loaded())
			return $asso;
		else
			return false;

	}

	function addSubscriber($subscriber){
		if(!$this->loaded()) throw $this->exception('Must be called on loaded Subscriber Category Model');
		if($subscriber instanceof Subscription) throw $this->exception('Subscriber Must be instance of Subscription Model');

		if(!$subscriber->loaded()) return false;

		$asso = $this->add('xEnquiryNSubscription/Model_SubscriptionCategoryAssociation');
		$asso->addCondition('category_id',$this->id);
		$asso->addCondition('subscriber_id',$subscriber->id);
		$asso->tryLoadAny();

		$asso['send_news_letters']=true;
		$asso->save();

		return $asso;

	}
	
	function beforeDelete(){
	 	$this->ref('xEnquiryNSubscription/Model_SubscriptionCategoryAssociation')->deleteAll();
	}
}