<?php

namespace xEnquiryNSubscription;

class Model_Forms extends \Model_Table{
	public $table='xEnquiryNSubscription_customForm_forms';

	function init(){
		parent::init();
		$this->hasOne('Epan','epan_id');
		$this->addCondition('epan_id',$this->api->current_website->id);		
		$f=$this->addField('name')->caption('Form Name')->mandatory(true)->group('a~6~<i class="fa fa-file-text-o"/> Basic Details');
		$f->icon='fa fa-file-text-o~red';
		$f=$this->addField('button_name')->group('a~6')->defaultValue('Submit');
		$f->icon= 'fa fa-adn~blue';
		// $this->addField('value')->hint('Comma Separated Values i.e. Red, Green, Blue');
		$f=$this->addField('receive_mail')->type('boolean')->group('b~4~<i class="fa fa-envelope"/> Send Email also!');
		$f->icon='fa fa-exclamation~blue';
		$f=$this->addField('receipent_email_id')->mandatory(true)->group('b~8');
		$f->icon='fa fa-envelope~blue';

		$this->hasMany('xEnquiryNSubscription/CustomFields','forms_id');
		$this->hasMany('xEnquiryNSubscription/CustomFormEntry','forms_id');
		$this->addHook('beforeDelete',$this);
		// $this->add('dynamic_model/Controller_AutoCreator');
	}

	function beforeDelete(){
		$this->ref('xEnquiryNSubscription/CustomFields')->deleteAll();
		$this->ref('xEnquiryNSubscription/CustomFormEntry')->deleteAll();
	}
}