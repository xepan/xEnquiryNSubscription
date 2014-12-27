<?php

namespace xEnquiryNSubscription;


class Model_EmailQueue extends \Model_Table {
	var $table='xEnquiryNSubscription_EmailQueue';
	public $mailer_object=null;
	function init(){
		parent::init();

		$this->hasOne('xEnquiryNSubscription/EmailJobs','emailjobs_id')->sortable(true);
		$this->hasOne('xEnquiryNSubscription/Subscription','subscriber_id')->display(array('form'=>'autocomplete/Basic'))->sortable(true);
		$this->addField('email')->sortable(true);
		$this->addField('sent_at')->defaultValue(date('Y-m-d H:i:s'))->type('datetime')->sortable(true);
		$this->addField('is_sent')->type('boolean')->defaultValue(false)->sortable(true);
		$this->addField('is_received')->type('boolean')->defaultValue(false);
		$this->addField('is_read')->type('boolean')->defaultValue(false);
		$this->addField('is_clicked')->type('boolean')->defaultValue(false);

		// $this->add('dynamic_model/Controller_AutoCreator');
		
	}

	function processSingle(){
	
		$news_letter = $this->ref('emailjobs_id')->ref('newsletter_id');
		
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

		$email = $this['subscriber_id']?$this->ref('subscriber_id')->get('email'):$this['email'];
		if(!$mailer->send($email,null,$news_letter->get('email_subject'),$news_letter->get('matter'),"")){
			return false;
		}
		$this['is_sent']=true;

		$this->ref('emailjobs_id')->set('processed',true)->save();

		$this->saveAndUnload();

		return true;
		// $news_letter->destroy();
		// if(method_exists($mailer, 'destroy'))
			// $mailer->destroy();
	}

}