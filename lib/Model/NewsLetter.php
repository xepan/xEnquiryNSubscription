<?php

namespace xEnquiryNSubscription;


class Model_NewsLetter extends \Model_Table {
	public $table ='xEnquiryNSubscription_NewsLetter';

	function init(){
		parent::init();
		
		$this->hasOne('Epan','epan_id');
		$this->addCondition('epan_id',$this->api->current_website->id);

		$f=$this->addField('name')->mandatory(true)->group('a1~6~Internal Name');
		$f->icon='fa fa-adn~red';
		// $this->addField('short_description')->display(array('grid'=>'shorttext,wrap'));//->hint('255 Characters Msg for social and tweets');
		$this->addField('email_subject')->mandatory(true)->group('a~12~<i/> NewsLetter');
		$this->addField('matter')->type('text')->display(array('form'=>'RichText'))->defaultValue('<p></p>')->group('a~12~bl')->mandatory(true);

		$this->addHook('beforeSave',$this);

		// $this->add('dynamic_model/Controller_AutoCreator');
	}

	function beforeSave(){
		if($this['matter']=='<p></p>')
			throw $this->exception('Matter is mandatory field','ValidityCheck')->setField('matter');
	}

}