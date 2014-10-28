<?php
class page_xEnquiryNSubscription_page_owner_form extends page_xEnquiryNSubscription_page_owner_main{
	public $menubar;
	function init(){
		parent::init();


		$this->add('H1')->setHTML('Form Setting <small>Update your Form settings</small>');
		$crud=$this->add('CRUD');
		$crud->setModel('xEnquiryNSubscription/Model_Forms');
		$refcrud=$crud->addRef('xEnquiryNSubscription/CustomFields',array('label'=>'Add Fields'));
		if($refcrud and $refcrud->form){

			$set_value_field=$refcrud->form->getElement('set_value');
			$expandable_field=$refcrud->form->getElement('is_expandable');
				$expandable_field->js(true)->univ()
				->bindConditionalShow(array(""=>array(),
								"*"=>array($set_value_field)
								),'div .atk-row');
			
		}

		
		
	}
}