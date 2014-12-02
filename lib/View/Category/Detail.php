<?php

namespace xEnquiryNSubscription;

class View_Category_Detail extends \View{
	
	function init(){
		parent::init();

		$this->add('View_Info')->set($_GET['category_id']);

	}
}