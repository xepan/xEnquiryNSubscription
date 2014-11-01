<?php

namespace xEnquiryNSubscription;

class View_Tools_unSubscribe extends \componentBase\View_Component{
	public $html_attributes=array(); // ONLY Available in server side components
	function init(){
		parent::init();

		if(!$_GET['email']){
			$this->add('View_Error')->set('No Email Provided');
			return;
		}
		$sub=$this->add('xEnquiryNSubscription/Model_Subscription');
		$sub->addCondition('email',$_GET['email']);

		if($_GET['xEnquiryNSubscription_categrory_id'])
			$sub->addCondition('category_id',$_GET['xEnquiryNSubscription_categrory_id']);
		
		foreach ($sub as  $junk) {
			$sub['send_news_letters']=false;
			$sub->save();
		}

		$this->add('View_Info')->set('UnSubscribe Email Successfully');
	}

	// defined in parent class
	// Template of this tool is view/namespace-ToolName.html
}