<?php

class page_xEnquiryNSubscription_page_install extends page_componentBase_page_install {
	function init(){
		parent::init();

		// Code To run before installing
		$model_array=array(
			'Model_SubscriptionCategories',
			'Model_Subscription',
			'Model_SubscriptionConfig',
			'Model_NewsLetter',
			'Model_Forms',
			'Model_EmailJobs',
			'Model_EmailQueue',
			'Model_CustomFormEntry',
			'Model_CustomFields',
			'Model_MassEmailConfiguration',
			'Model_HostsTouched'
			);

		foreach ($model_array as $md) {
			try{
				$model = $this->add('xEnquiryNSubscription/'.$md);
				$model->add('dynamic_model/Controller_AutoCreator');
				$model->tryLoadAny();
			}catch(Exception_DB $e){
				echo $e->getMessage();
			}
		}


		
		$this->install();
		
		// Code to run after installation
	}
}