<?php

namespace xEnquiryNSubscription;


class Model_EmailJobs extends \Model_Table {
	public $table ='xEnquiryNSubscription_EmailJobs';
	public $mailer_object=null;

	function init(){
		parent::init();
		
		$this->hasOne('xEnquiryNSubscription/NewsLetter','newsletter_id');
		// $this->hasOne('xEnquiryNSubscription/Subscription','subscriber_id');
		// $this->addField('email');
		$this->addField('job_posted_at')->type('datetime')->defaultValue(date('Y-m-d H:i:s'));
		$this->addField('processed')->type('boolean')->defaultValue(false);
		$this->addField('processed_on')->type('datetime')->defaultValue(date('Y-m-d H:i:s'));

		$this->addField('post_socials_and_blogs')->type('boolean')->defaultValue(false);

		$this->addExpression('processed_in_hour')->set('DATE_FORMAT(processed_on,"%Y-%m-%d %H:00:00")');

		$this->hasMany('xEnquiryNSubscription/EmailQueue','emailjobs_id');
		
		// $this->add('dynamic_model/Controller_AutoCreator');
	
	}

}