<?php

class page_xEnquiryNSubscription_page_owner_update extends page_componentBase_page_update {
	
	public $git_path = 'https://github.com/xepan/xEnquiryNSubscription';

	function init(){
		parent::init();

		// 
		// Code To run before installing
		
		$this->update(false);

		$model_array=array(
			'Model_SubscriptionCategoryAssociation',
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
			'Model_HostsTouched',
			);

		foreach ($model_array as $md) {
			$model = $this->add('xEnquiryNSubscription/'.$md);
			$model->add('dynamic_model/Controller_AutoCreator');
			$model->tryLoadAny();
		}

		
		$this->add('View_Info')->set('Component Is SuccessFully Updated');
		// Code to run after installation
	}
}