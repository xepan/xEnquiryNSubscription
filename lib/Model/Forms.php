<?php

namespace xEnquiryNSubscription;

class Model_Forms extends \Model_Table{
	public $table='xEnquiryNSubscription_customForm_forms';

	function init(){
		parent::init();
		$this->hasOne('Epan','epan_id');
		$this->addCondition('epan_id',$this->api->current_website->id);		
		$this->addField('name')->caption('Form Name');
		// $this->addField('value')->hint('Comma Separated Values i.e. Red, Green, Blue');
		$this->addField('receipent_email_id')->mandatory(true);
		$this->addField('receive_mail')->type('boolean');
		$this->addField('button_name');

		$this->hasMany('xEnquiryNSubscription/CustomFields','forms_id');

		// $this->add('dynamic_model/Controller_AutoCreator');
	}
}