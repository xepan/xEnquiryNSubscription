<?php

namespace xEnquiryNSubscription;

class View_Tools_CustomeForm extends \componentBase\View_Component{

	function init(){
		parent::init();
			
			if(!$this->data_options){
				$this->add('View_Error')->set('Please Select Form Category');
				return;
				$this->data_options = $form['data_options'];
			}
			$form_model=$this->add('xEnquiryNSubscription/Model_Forms');
			
			$form=$this->add('Form',array('name'=>$this->api->normalizeName($form_model['name'])));
			$form_data_options_field = $form->addField('hidden','data_options');

			$form_data_options_field->set($this->data_options);
			
			$form_model->addCondition('epan_id',$this->api->current_website->id);

			if(!$this->data_options){
				$form_model->tryLoadAny();
				if(!$form_model->loaded()){
					$this->add('View_Error')->set('No Form Settings loaded..');
					return;
				}
			}
			else{
				$form_model->load($this->data_options);
			}

			$this->rename($this->api->normalizeName($form_model['name']));


			$custome_field=$form_model->ref('xEnquiryNSubscription/CustomFields');
			

			if($custome_field->count()->getOne() == '0'){
				$this->add('View_Error')->set('Please Add Field in your form');
				$form->setStyle('display','none');	
			}

			foreach ($custome_field as $junk) {		
				if($junk['type']=='captcha'){
					// throw new \Exception($custome_field['type']=='captcha');
				$captcha_field=$form->addField('line','captcha');
				$captcha_field->belowField()->add('H5')->set('Please enter the code shown above');
				$captcha_field->add('x_captcha/Controller_Captcha');
				}elseif($junk['mandatory']){
					$field=$form->addField($custome_field['type'],$this->api->normalizeName($custome_field['name']),$custome_field['name'])->validateNotNull(true);
				}
				else{
					$field=$form->addField($custome_field['type'],$this->api->normalizeName($custome_field['name']),$custome_field['name']);
				}

				if($junk['type']=='dropdown'){
					$field->setEmptyText('Please Select');
				}

				
				if(in_array($custome_field['type'],array('radio','dropdown'))){		
					$new_arr =explode(',', $custome_field['set_value']);
					$to_put=array();
					foreach ($new_arr as $value) {
						$to_put[$value] = $value;
					}
					$field->setValueList($to_put);
				}
			}
			if($form_model['button_name'])
				$form->addSubmit($form_model['button_name']);
			else{
				$form->addSubmit('Submit');
			}

			if($form->isSubmitted()){

				// throw new \Exception(print_r($form->getAllFields(),true));
				if(!$form_model['receipent_email_id'])
					$this->js()->univ()->errorMessage('Please Insert Receipent Email id')->execute();

				$epan=$this->api->current_website;

				$form_entry_model=$this->add('xEnquiryNSubscription/Model_CustomFormEntry');
				$tm=$this->add( 'TMail_Transport_PHPMailer' );
			
				$msg=$this->add( 'SMLite' );
				$msg->loadTemplate( 'mail/xEnquiryNSubscripition_customeform' );

				$form_values="";
				foreach ($custome_field as $junk) {
					$form_values .= "<b>".$custome_field['name']."</b> : " . $form[$this->api->normalizeName($custome_field['name'])] . '<br/>';
				}

				if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			    	$ip = $_SERVER['HTTP_CLIENT_IP'];
			    }elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			    		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
				} else {
			    		$ip = $_SERVER['REMOTE_ADDR'];
					}
				// saving form enquiry 
				$form_entry_model->createNew($this->api->current_website->id,$form_model['id'],date('Y-m-d'),$form_values,$ip);

				$msg->trySet('epan',$this->api->current_website['name']);
				$msg->setHTML('custome_form',$form_values);

				$email_body=$msg->render();

				$subject ="You Got An  Enquiry !!!";

				if($form_model['receive_mail']){
					try{
						$tm->send( $form_model['receipent_email_id'], $epan['email_id'], $subject, $email_body ,false,null);
						// throw new \Exception($form->getAllFields());
					}catch(\Exception $e ) {
						// $this->js()->univ()->errorMessage('Please')->execute();
						// throw $e;
						$alert_model=$this->add('Model_Alerts');	
						$alert_model->createNew($this->api->current_website->id,"your email setting is not configure properly","danger","Custom Enquiry Form");	
						return;
						// throw $e;
					}
				}

				$message_model=$this->add('Model_Messages');	
				$message_model->createNew($this->api->current_website->id,"Custom Form Entry","Submitted : ". $form_model['name'],"Custom Enquiry Form");	

				$goal_uuid = array(array('uuid'=>$form_model['name']));
				$this->api->exec_plugins('goal',$goal_uuid);
					
				$form->js(null,$this->js()->univ()->successMessage('Thank You for Enquiry'))->reload()->execute();
			}
	}
	// defined in parent class
	// Template of this tool is view/namespace-ToolName.html
}
