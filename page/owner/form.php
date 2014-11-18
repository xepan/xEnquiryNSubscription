<?php
class page_xEnquiryNSubscription_page_owner_form extends page_xEnquiryNSubscription_page_owner_main{
	public $menubar;
	function init(){
		parent::init();


		$this->add('H4')->setHTML('Manage Your Custom Forms <small>and their filled data</small>');
		$crud=$this->add('CRUD');
		$crud->setModel('xEnquiryNSubscription/Model_Forms');
		$crud->add('Controller_FormBeautifier');

		if($crud->grid){
			$crud->add_button->setIcon('ui-icon-plusthick');
		}

		$refcrud=$crud->addRef('xEnquiryNSubscription/CustomFields',array('label'=>'Add Fields'));


		if($refcrud and $refcrud->grid){
			$refcrud->add_button->setIcon('ui-icon-plusthick');
		}

		if($refcrud and $refcrud->form){

			$set_value_field=$refcrud->form->getElement('set_value');
			$expandable_field=$refcrud->form->getElement('type');
				$expandable_field->js(true)->univ()
				->bindConditionalShow(array(""=>array(),
								"dropdown"=>array($set_value_field),
								"radio"=>array($set_value_field)
								),'div .atk-row');
			
		}

		
		if($refcrud){
			$refcrud->add('Controller_FormBeautifier');
		}

	}
}