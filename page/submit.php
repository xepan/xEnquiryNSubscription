<?php

class page_xEnquiryNSubscription_page_submit extends Page {

	public $required_login=false;

	function init(){
		parent::init();
		$x='Enquiry-Submitted';

		$epan_model=$this->add('Model_Epan');

		$l=$this->api->locate('addons','xEnquiryNSubscription', 'location');
		$this->api->pathfinder->addLocation(
			$this->api->locate('addons','xEnquiryNSubscription'),
			array(
		  		'template'=>'templates',
		  		'css'=>'templates/css'
				)
			)->setParent($l);

		$email_to = $_REQUEST['email_to'];

		if($_REQUEST['email_to'] == '')
			$email_to = $this->api->current_website['email_id'];

		if($email_to == '')
			$this->js()->univ()->errorMessage('Opps! Email not set')->execute();


		$enquiry_entries="";
		
		foreach (json_decode($_REQUEST['form_entries']) as $entry) {
			$enquiry_entries .= "<b>".$entry->fieldTitle."</b> : " . $entry->fieldValue . " <br/>";
		}
		
		$tm=$this->add( 'TMail_Transport_PHPMailer' );
		$msg=$this->add( 'GiTemplate' );
		$msg->loadTemplate( 'mail/enquiryform' );

		$msg->trySet('epan',$this->api->current_website['name']);
		$msg->trySetHTML('form_entries',$enquiry_entries);

		$email_body=$msg->render();

		$subject ="Your Epan Got An Enquiry !!!";

		try{
			$tm->send( $email_to, $epan_model['email_id'], $subject, $email_body ,false,null);
		}catch( phpmailerException $e ) {
			// throw $e;
			$this->api->js(null,'$("#form-'.$_REQUEST['form_id'].'")[0].reset()')->univ()->errorMessage( $e->errorMessage() . " " . $this->api->current_website['email_id']  )->execute();
		}catch( Exception $e ) {
			throw $e;
		}

		$goal_uuid = array(array('uuid'=>$form_model['name']));
		$this->api->exec_plugins('goal','enquiry');

		$this->js(null,'$("#form-'.$_REQUEST['form_id'].'")[0].reset()')->univ()->successMessage('Enquiry Sent to ' . $_REQUEST['email_to'])->execute();
	}
}