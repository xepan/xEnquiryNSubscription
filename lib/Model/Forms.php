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
		$f=$this->addField('receipent_email_id')->group('b~8');
		$f->icon='fa fa-envelope~blue';


		$this->hasMany('xEnquiryNSubscription/CustomFields','forms_id');
		$this->hasMany('xEnquiryNSubscription/CustomFormEntry','forms_id');
		
		$this->addExpression('un_read_submission')->set(function($m,$q){
			return $m->refSQL('xEnquiryNSubscription/CustomFormEntry')
			->addCondition(
				$m->dsql()->orExpr()
				->where('is_read',false)
				->where('watch',true)
			)->count();
		});
		$this->addHook('beforeSave',$this);
		$this->addHook('beforeDelete',$this);
		// $this->add('dynamic_model/Controller_AutoCreator');
	}

	function beforeDelete(){
		$this->ref('xEnquiryNSubscription/CustomFields')->deleteAll();
		$this->ref('xEnquiryNSubscription/CustomFormEntry')->deleteAll();
	}

	function beforeSave(){
		if($this['receive_mail'] and !$this['receipent_email_id'])
			throw $this->exception('Please specify Email Id','ValidityCheck')->setField('receipent_email_id');

		$name_check =$this->add('xEnquiryNSubscription/Model_Forms');
		$name_check->addCondition('name',$this['name']);
		$name_check->addCondition('id','<>',$this->id);
		$name_check->tryLoadAny();

		if($name_check->loaded())
			throw $this->exception('Name Already Taken','ValidityCheck')->setField('name');

	}
}