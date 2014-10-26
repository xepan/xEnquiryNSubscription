<?php

class page_xEnquiryNSubscription_page_emailexec extends Page {
	public $mailer_object = null;


	function init(){
		parent::init();

		$jobs_processed_in_this_hour = $this->add('xEnquiryNSubscription/Model_EmailQueue');
		$jobs_processed_in_this_hour->addCondition('sent_at','>=',date('Y-m-d H:00:00'));
		$jobs_processed_in_this_hour->addCondition('is_sent',true);
		$count = $jobs_processed_in_this_hour->count()->getOne();

		$remainings = $this->api->current_website['email_threshold'] -  $count;

		if(!$remainings)
			$this->js()->univ()->errorMessage('Threshold Reached, No Mails sent, Try in Next Hour')->execute();

		$email_to_process = $this->add('xEnquiryNSubscription/Model_EmailQueue');
		$email_to_process->addCondition('is_sent',false);
		$email_to_process->setOrder('id','asc');
		$email_to_process->setOrder('emailjobs_id','asc');
		$email_to_process->setLimit($remainings);

		$mass_email = $this->add('xEnquiryNSubscription/Model_MassEmailConfiguration')->tryLoadAny();

		$i=0;
		$emails = array();
		foreach ($email_to_process as $junk) {
			$emails[$junk['emailjobs_id']][] = $junk['subscriber'];
			$email_to_process['is_sent']=true;
			$email_to_process->saveAndUnload();
			$i++;
		}
		// echo "<pre>";
		// print_r($emails);
		// echo "</pre>";
		// exit;

		if(!$this->mailer_object){
			$mass_email = $this->add('xEnquiryNSubscription/Model_MassEmailConfiguration')->tryLoadAny();
			if($mass_email->loaded() and $mass_email['use_mandril'] and $mass_email['mandril_api_key']){
				$mailer  = new Mandrill($mass_email['mandril_api_key'],$this->api);
			}else{
				$mailer = $this->add('TMail_Transport_PHPMailer');
			}
		}else{
			$mailer = $this->mailer_object;
		}

		if($mass_email['send_via_bcc']){
			$email = $this->api->current_website['email_from'];
			foreach ($emails as $job_id => $emails) {
				$news_letter = $this->add('xEnquiryNSubscription/Model_NewsLetter');
				$news_letter->load($this->add('xEnquiryNSubscription/Model_EmailJobs')->load($job_id)->get('newsletter_id'));
				$mailer = $this->add('TMail_Transport_PHPMailer');
				$mailer->send($email,null,$news_letter->get('email_subject'),$news_letter->get('matter'),"",array(),$emails);
			}
		}	else{
			foreach ($emails as $job_id => $emails) {
				foreach($emails as $email){
					$news_letter = $this->add('xEnquiryNSubscription/Model_NewsLetter');
					$news_letter->load($this->add('xEnquiryNSubscription/Model_EmailJobs')->load($job_id)->get('newsletter_id'));
					$mailer = $this->add('TMail_Transport_PHPMailer');
					$mailer->send($email,null,$news_letter->get('email_subject'),$news_letter->get('matter'),"");
				}
			}
		}

		$this->js(true)->univ()->successMessage( $i.' Emails Sent');

	}

	
}