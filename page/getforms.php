<?php

class page_xEnquiryNSubscription_page_getforms extends Page {

	function init(){
		parent::init();
		
		$str="<option value='0'>Please Select</option>";

		$form=$this->add('xEnquiryNSubscription/Model_Forms');

		foreach ($form as  $junk) {
			$str.="<option value='".$junk['id']."'>".$junk['name']."</option>";
		}
		echo $str;
		exit;


	}
}	