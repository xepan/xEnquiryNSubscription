<?php

class page_xEnquiryNSubscription_page_emailexec extends Page {
	public $mailer_object = null;


	function init(){
		parent::init();

		$jobs_processed_in_this_hour = $this->add('xEnquiryNSubscription/Model_EmailQueue');
		$jobs_processed_in_this_hour->addCondition('sent_at','>=',date('Y-m-d H:00:00'));
		$jobs_processed_in_this_hour->addCondition('is_sent',true);

		$job_j = $jobs_processed_in_this_hour->join('xEnquiryNSubscription_EmailJobs','emailjobs_id');
		$job_j->addField('process_via');
		$jobs_processed_in_this_hour->addCondition('process_via','xEnquiryNSubscription');


		$count = $jobs_processed_in_this_hour->count()->getOne();

		$remainings = $this->api->current_website['email_threshold'] -  $count;

		if(!$remainings){
			$this->js()->univ()->errorMessage('Threshold Reached, No Mails sent, Try in Next Hour')->execute();
			return; // double stop for cron
		}

		$email_to_process = $this->add('xEnquiryNSubscription/Model_EmailQueue');
		$email_to_process->addCondition('is_sent',false);
		$email_to_process->setOrder('id','asc');
		$email_to_process->setOrder('emailjobs_id','asc');

		$job_j = $email_to_process->join('xEnquiryNSubscription_EmailJobs','emailjobs_id');
		$job_j->addField('process_via');
		$email_to_process->addCondition('process_via','xEnquiryNSubscription');


		$email_to_process->setLimit($remainings);

		$mailer = $this->add('TMail_Transport_PHPMailer');

		$i=0;
		foreach($email_to_process as $email){
			$news_letter = $this->add('xEnquiryNSubscription/Model_NewsLetter');
			$news_letter->load($this->add('xEnquiryNSubscription/Model_EmailJobs')->load($email_to_process['emailjobs_id'])->get('newsletter_id'));
			if(!$mailer->send($email['subscriber'],null,$news_letter->get('email_subject'),$news_letter->get('matter'),"")){
				$email_to_process->ref('subscriber_id')->set('is_bounced',true)->saveAndUnload();
			}
			$email_to_process['is_sent']=true;
			$email_to_process->saveAndUnload();
			$i++;
		}

		$this->js(true)->univ()->successMessage( $i.' Emails Sent');

	}

	
}