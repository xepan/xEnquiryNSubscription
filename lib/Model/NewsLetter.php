<?php

namespace xEnquiryNSubscription;


class Model_NewsLetter extends \Model_Table {
	public $table ='xEnquiryNSubscription_NewsLetter';

	function init(){
		parent::init();
		
		$this->hasOne('Epan','epan_id');
		$this->addCondition('epan_id',$this->api->current_website->id);

		$this->addField('name');
		$this->addField('short_description')->display(array('grid'=>'shorttext,wrap'));//->hint('255 Characters Msg for social and tweets');
		$this->addField('email_subject');
		$this->addField('matter')->type('text')->display(array('form'=>'RichText'))->defaultValue('<p></p>');
		// $this->add('dynamic_model/Controller_AutoCreator');
	}
}