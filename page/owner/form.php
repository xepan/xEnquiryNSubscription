<?php
class page_xEnquiryNSubscription_page_owner_form extends page_xEnquiryNSubscription_page_owner_main{
	public $menubar;
	function init(){
		parent::init();


		$this->add('H4')->setHTML('Manage Your Custom Forms <small>And submitted data</small>');
		$crud=$this->add('CRUD');
		$crud->setModel('xEnquiryNSubscription/Model_Forms');
		$crud->add('Controller_FormBeautifier');

		if($crud->grid){
			$crud->add_button->setIcon('ui-icon-plusthick');
		}

		$refcrud=$crud->addRef('xEnquiryNSubscription/CustomFields',array('label'=>'Add Fields'));
		$form_values = $crud->addRef('xEnquiryNSubscription/CustomFormEntry',array('label'=>'Submissions','view_options'=>array('allow_add'=>false)));
 
		if($form_values and $form_values->grid){
			$form_values->grid->addClass('panel panel-default');
			$form_values->grid->setStyle('padding','20px');
			$form_values->grid->addPaginator(50);
			$form_values->grid->addQuickSearch(array('message'));
		}

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