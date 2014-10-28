<?php
namespace xEnquiryNSubscription;

class Model_CustomFormEntry extends \Model_Table {
	var $table= "xEnquiryNSubscription_customformentry";
	function init(){
		parent::init();
		$this->hasOne('Epan','epan_id');
		$this->addCondition('epan_id',$this->api->current_website->id);
		$this->hasOne('xEnquiryNSubscription/Forms','forms_id');

		$this->addField('create_at')->type('date')->defaultValue(date('Y-m-d'));
		$this->addField('ip');
		$this->addField('message');

		// $this->add('dynamic_model/Controller_AutoCreator');
	}

	function createNew($epan_id,$forms_id,$create_at,$message,$ip){
		if($this->loaded())
			throw new \Exception('model cannot be loaded');
			
		$this['epan_id']=$epan_id;
		$this['forms_id']=$forms_id;
		$this['create_at']=$create_at;
		$this['ip']=$ip;
		$this['message']=$message;
		$this->save();

	}
}