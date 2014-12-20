<?php
namespace xEnquiryNSubscription;

class Model_CustomFields extends \Model_Table{
	public $table="xEnquiryNSubscription_custome_customFields";
	
	function init(){
		parent::init();


		// $this->hasOne('Epan','epan_id');	
		$this->hasOne('xEnquiryNSubscription/Forms','forms_id');
		$f=$this->addField('name')->caption('Field Name')->mandatory(true)->group('a~6~Basic Details');
		$f->icon='fa fa-adn~red';

		$f=$type=$this->addField('type')->setValueList(array(
													'Number'=>'Number',
													'email'=>'Email',
													'line'=>'Line',
													'text'=>'Text',
													'password'=>'Password',
													'radio'=>'Radio', 
													'checkbox'=>'Checkbox',
													'dropdown'=>'Dropdown',
													'DatePicker'=>'Date',
													// 'Upload'=>'Upload',
													'captcha'=>'Captcha'))
									->mandatory(true)->group('a~4');
		$f->icon ='fa fa-question~red';
		$f=$this->addField('mandatory')->type('boolean')->Caption('Requird Field')->group('a~2');
		$f->icon='fa fa-exclamation~blue';
		// $f=$this->addField('is_expandable')->type('boolean')->defaultValue(false)->group('b~4~Extended Information');
		$f=$this->addField('set_value')->hint('Comma Separated Values i.e. Male, Female, Other [For Radio and DropDown Fields]')->group('b~12~Extended Information');
		$f->icon='';
		// $this->addCondition('epan_id',$this->api->current_website->id);
		// $this->add('dynamic_model/Controller_AutoCreator');

	
		
 	}
}