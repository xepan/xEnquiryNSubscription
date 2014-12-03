<?php

namespace xEnquiryNSubscription;


class Model_Config extends \Model_Table{
	public $table='xEnquiryNSubscription_Config';

	function init(){
		parent::init();
		$this->hasOne('Epan','epan_id');
		$this->addCondition('epan_id',$this->api->current_website->id);

		$this->addField('show_all_newsletters')->type('boolean')->defaultValue(false)->caption('Show News Letters Created By All Applications');
		

		$this->add('dynamic_model/Controller_AutoCreator');
	}

}