<?php

namespace xEnquiryNSubscription;

class View_Tools_SubscriptionModule extends \componentBase\View_Component{
	
	function init(){
		parent::init();
		
		$category = $this->add('xEnquiryNSubscription/Model_SubscriptionCategories');
		$category->tryLoadBy('name',$this->data_options);


		$config_model=$this->add('xEnquiryNSubscription/Model_SubscriptionConfig');
		$config_model->addCondition('category_id',$category->id);
		$config_model->tryLoadAny();

		if(!$category->loaded() or !$config_model->loaded()){
			$this->add('View_Error')->set('Config not loaded/set, double click and select any Category ' . $this->data_options .' -');
			return;
		}

		$form = $this->add('Form',array('name'=>$this->html_attributes['id']),null,array('form'));
		$form->addClass('stacked');
		$form->setModel('xEnquiryNSubscription/Subscription',array('email'));
		$email_field = $form->getElement('email');
		$email_field->setCaption($config_model['email_caption']);
		if($config_model['placeholder_text'])
			$email_field->setAttr('placeholder',$config_model['placeholder_text']);
		if($config_model['subscribe_caption'])
			$form->addSubmit($config_model['subscribe_caption'])->addClass('btn btn-default');
		else{
			$email_field->setAttr('placeholder','Enter Your Email ID and Press Enter');
			$form->template->trydel('button_row');
		}

		if($form->isSubmitted()){
			
			if(!$config_model['allow_non_email_entries']){
				$valid=true;
				$address = $form['email'];
				if ((defined('PCRE_VERSION')) && (version_compare(PCRE_VERSION, '8.0') >= 0)) {
				  $valid =  preg_match('/^(?!(?>(?1)"?(?>\\\[ -~]|[^"])"?(?1)){255,})(?!(?>(?1)"?(?>\\\[ -~]|[^"])"?(?1)){65,}@)((?>(?>(?>((?>(?>(?>\x0D\x0A)?[	 ])+|(?>[	 ]*\x0D\x0A)?[	 ]+)?)(\((?>(?2)(?>[\x01-\x08\x0B\x0C\x0E-\'*-\[\]-\x7F]|\\\[\x00-\x7F]|(?3)))*(?2)\)))+(?2))|(?2))?)([!#-\'*+\/-9=?^-~-]+|"(?>(?2)(?>[\x01-\x08\x0B\x0C\x0E-!#-\[\]-\x7F]|\\\[\x00-\x7F]))*(?2)")(?>(?1)\.(?1)(?4))*(?1)@(?!(?1)[a-z0-9-]{64,})(?1)(?>([a-z0-9](?>[a-z0-9-]*[a-z0-9])?)(?>(?1)\.(?!(?1)[a-z0-9-]{64,})(?1)(?5)){0,126}|\[(?:(?>IPv6:(?>([a-f0-9]{1,4})(?>:(?6)){7}|(?!(?:.*[a-f0-9][:\]]){7,})((?6)(?>:(?6)){0,5})?::(?7)?))|(?>(?>IPv6:(?>(?6)(?>:(?6)){5}:|(?!(?:.*[a-f0-9]:){5,})(?8)?::(?>((?6)(?>:(?6)){0,3}):)?))?(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])(?>\.(?9)){3}))\])(?1)$/isD', $address);
				} elseif (function_exists('filter_var')) { //Introduced in PHP 5.2
			        if(filter_var($address, FILTER_VALIDATE_EMAIL) === FALSE) {
			          $valid = false;
			        } else {
			          $valid =  true;
			        }
			    } else {
			        $valid =  preg_match('/^(?:[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+\.)*[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+@(?:(?:(?:[a-zA-Z0-9_](?:[a-zA-Z0-9_\-](?!\.)){0,61}[a-zA-Z0-9_-]?\.)+[a-zA-Z0-9_](?:[a-zA-Z0-9_\-](?!$)){0,61}[a-zA-Z0-9_]?)|(?:\[(?:(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\.){3}(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\]))$/', $address);
				}
				if(!$valid)
					$form->displayError('email','Please specify Proper email');
			}

			$check_existing = $this->add('xEnquiryNSubscription/Model_Subscription');
			$check_existing->addCondition('email',$form['email']);
			$check_existing->tryLoadAny();
			
			if(!$check_existing->loaded()){
				// No email found like this in records ... saving now
				$check_existing['from_app']='Website';
				$check_existing->save();
			}else{
				// Already in database
				if($check_existing['is_bounced']){
					$form->displayError('email','This Address is supposed to bounce');
				}

				if($ex=$category->hasSubscriber($check_existing)){
					if(!$config_model['allow_re_subscribe']){
						$form->displayError('email','Already Subscribed');
					}
				}
			}

			$category->addSubscriber($check_existing);
		

			$js=array();

			if($config_model['send_response_email']){
				try{
					$mailer = $this->add('TMail_Transport_PHPMailer');
					$mailer->send($form['email'],null,$config_model['email_subject'],$config_model['email_body'],"");
				}catch(Exception $e){
					$js[] = $form->js()->univ()->errorMessage($e->getMessage());
				}
			}

			if($config_model['thank_you_msg'])
				$js[] = $form->js()->univ()->successMessage($config_model['thank_you_msg']);
			if($config_model['flip_the_html'])
				$js[] = $form->js()->html($config_model['flip_the_html']);
			else
				$js[] = $form->js()->reload();

			$goal_uuid = array(array('uuid'=>$config_model['name']));
			$this->api->exec_plugins('goal',$goal_uuid);

			$form->js(null,$js)->execute();

		}


	}

	// defined in parent class
	// Template of this tool is view/namespace-ToolName.html
}
