<?php
namespace xEnquiryNSubscription;

class Model_CustomFields extends \Model_Table{
	public $table="xEnquiryNSubscription_custome_customFields";
	
	function init(){
		parent::init();


		// $this->hasOne('Epan','epan_id');	
		$this->hasOne('xEnquiryNSubscription/Forms','forms_id');
		$this->addField('name')->caption('Field Name')->mandatory(true);

		$type=$this->addField('type')->setValueList(array(
													'Number'=>'INTEGER',
													'line'=>'LINE',
													'text'=>'TEXT',
													'password'=>'PASSWORD',
													'radio'=>'radio', 
													'checkbox'=>'checkbox',
													'dropdown'=>'DROPDOWN',
													'DatePicker'=>'DATE',
													'Upload'=>'UPLOAD',
													'captcha'=>'Captcha'))
									->mandatory(true);

		$this->addField('is_expandable')->type('boolean')->defaultValue(false);
		$this->addField('set_value')->hint('Comma Separated Values i.e. Male, Female, Other');
		$this->addField('mandatory')->type('boolean')->Caption('Requird Field');
		// $this->addCondition('epan_id',$this->api->current_website->id);
		// $this->add('dynamic_model/Controller_AutoCreator');

	
		
 	}
}