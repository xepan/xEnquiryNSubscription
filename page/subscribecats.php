<?php

class page_xEnquiryNSubscription_page_subscribecats extends Page{
	
	function init(){
		parent::init();
		$cats= $this->add('xEnquiryNSubscription/Model_SubscriptionCategories');
		$cats->addCondition('is_active',true);
		$options= "";
		foreach ($cats as $junk) {
			$options .= '<option value="'.$cats['name'].'">'.$cats['name'].'</option>';
		}

		echo $options;
		exit;
	}
}