<?php

class page_xEnquiryNSubscription_page_install extends page_componentBase_page_install {
	function init(){
		parent::init();

		// Code To run before installing
		$model = $this->add('xEnquiryNSubscription/Model_SubscriptionCategories');
		$model->add('dynamic_model/Controller_AutoCreator');
		$model->tryLoadAny();

		$model = $this->add('xEnquiryNSubscription/Model_Subscription');
		$model->add('dynamic_model/Controller_AutoCreator');
		$model->tryLoadAny();

		$model = $this->add('xEnquiryNSubscription/Model_SubscriptionConfig');
		$model->add('dynamic_model/Controller_AutoCreator');
		$model->tryLoadAny();

		$model = $this->add('xEnquiryNSubscription/Model_NewsLetter');
		$model->add('dynamic_model/Controller_AutoCreator');
		$model->tryLoadAny();
		
		$model = $this->add('xEnquiryNSubscription/Model_Forms');
		$model->add('dynamic_model/Controller_AutoCreator');
		$model->tryLoadAny();

		$model = $this->add('xEnquiryNSubscription/Model_EmailJobs');
		$model->add('dynamic_model/Controller_AutoCreator');
		$model->tryLoadAny();

		$model = $this->add('xEnquiryNSubscription/Model_CustomFormEntry');
		$model->add('dynamic_model/Controller_AutoCreator');
		$model->tryLoadAny();

		$model = $this->add('xEnquiryNSubscription/Model_CustomFields');
		$model->add('dynamic_model/Controller_AutoCreator');
		$model->tryLoadAny();



		
		$this->install();
		
		// Code to run after installation
	}
}