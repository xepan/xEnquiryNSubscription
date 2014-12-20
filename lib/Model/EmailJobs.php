<?php

namespace xEnquiryNSubscription;


class Model_EmailJobs extends \Model_Table {
	public $table ='xEnquiryNSubscription_EmailJobs';
	public $mailer_object=null;

	function init(){
		parent::init();
		
		$this->hasOne('xEnquiryNSubscription/NewsLetter','newsletter_id');

		$this->addField('job_posted_at')->type('datetime')->defaultValue(date('Y-m-d H:i:s'));
		$this->addField('processed_on')->type('datetime')->defaultValue(null);

		$this->addExpression('name')->set(function($m,$q){
			return $m->refSQL('newsletter_id')->fieldQuery('name');
		});

		$this->addField('process_via')->system(true)->defaultValue('xEnquiryNSubscription');

		$this->addExpression('processed_in_hour')->set('DATE_FORMAT(processed_on,"%Y-%m-%d %H:00:00")');

		$this->hasMany('xEnquiryNSubscription/EmailQueue','emailjobs_id');
		
		$this->addHook('beforeSave',$this);
		$this->addHook('beforeDelete',$this);

		// $this->add('dynamic_model/Controller_AutoCreator');
	
	}

	function beforeSave(){
		if($this->dirty['processed'] and $this['processed']==true){
			$this['processed_on'] = date('Y-m-d H:i:s');
		}
	}

	function beforeDelete(){
		$this->ref('xEnquiryNSubscription/EmailQueue')->deleteAll();
	}


}