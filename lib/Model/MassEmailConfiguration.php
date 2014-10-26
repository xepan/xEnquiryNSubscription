<?php
namespace xEnquiryNSubscription;
class Model_MassEmailConfiguration extends \Model_Table{
	var $table="xEnquiryNSubscription_massemailconfiguration";
	function init(){
		parent::init();

		$this->hasOne('Epan','epan_id');
		$this->addCondition('epan_id',$this->api->current_website->id);
		$this->addField('use_mandril')->type('boolean')->defaultValue(false)->hint('Enabling This Setting Will Override Your Epan Ganrel Email Setting');
		$this->addField('mandril_api_key');
		$this->addField('send_via_bcc')->type('boolean')->defaultValue(true);
		// $this->add('dynamic_model/Controller_AutoCreator');
	}
}