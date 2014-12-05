<?php

namespace xEnquiryNSubscription;


class Model_NewsLetter extends \Model_Table {
	public $table ='xEnquiryNSubscription_NewsLetter';

	function init(){
		parent::init();
		
		$this->hasOne('Epan','epan_id');
		$this->addCondition('epan_id',$this->api->current_website->id);

		$f=$this->addField('name')->mandatory(true)->group('a1~6~Internal Name')->sortable(true);
		$f->icon='fa fa-adn~red';
		// $this->addField('short_description')->display(array('grid'=>'shorttext,wrap'));//->hint('255 Characters Msg for social and tweets');
		$this->addField('email_subject')->mandatory(true)->group('a~12~<i/> NewsLetter')->sortable(true);
		$this->addField('matter')->type('text')->display(array('form'=>'RichText'))->defaultValue('<p></p>')->group('a~12~bl')->mandatory(true);
		$this->hasMany('xEnquiryNSubscription/EmailJobs','newsletter_id');
		$this->addHook('beforeSave',$this);
		$this->addHook('beforeDelete',$this);

		$this->addField('created_at')->type('datetime')->defaultValue(date('Y-m-d H:i:s'));
		$this->addField('created_by')->system(true)->defaultValue('xEnquiryNSubscription')->sortable(true);

		// $this->add('dynamic_model/Controller_AutoCreator');
	}

	function beforeSave(){
		if($this['matter']=='<p></p>')
			throw $this->exception('Matter is mandatory field','ValidityCheck')->setField('matter');
	}
	function beforeDelete(){
		$jobs=$this->ref('xEnquiryNSubscription/EmailJobs');
		foreach($jobs as $junk){
			$jobs->delete();
		}

		$this->api->event('xenq_n_subs_newletter_before_delete',$this);

	}

}