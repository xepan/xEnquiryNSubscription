<?php
class page_xEnquiryNSubscription_page_owner_form extends page_xEnquiryNSubscription_page_owner_main{
	public $menubar;
	function init(){
		parent::init();

		$bv = $this->add('View_BackEndView',array('cols_widths'=>array(12)));
		$bv->addToTopBar('H3')->set('Custom Form');
		$total_custom_form=$this->setModel('xEnquiryNSubscription/Model_Forms')->count()->getOne();
		$bv->addToTopBar('View')->set('Custom Form -'.$total_custom_form);

		$total_submission_entry=$this->add('xEnquiryNSubscription/Model_CustomFormEntry')->count()->getOne();
		$bv->addToTopBar('View')->set('Total Custom Form Submissions  -'.$total_submission_entry);

		$op = $bv->addOptionButton();
		$crud = $bv->addToColumn(0,'View');


		$this->add('H4')->setHTML('Manage Your Custom Forms <small>And submitted data</small>');
		$crud=$this->add('CRUD');
		$crud->setModel('xEnquiryNSubscription/Model_Forms');
		$crud->add('Controller_FormBeautifier');

		if($crud->grid){
			$crud->add_button->setIcon('ui-icon-plusthick');

		}

		$refcrud=$crud->addRef('xEnquiryNSubscription/CustomFields',array('label'=>'Add Fields'));
		$form_values = $crud->addRef('xEnquiryNSubscription/CustomFormEntry',array('label'=>'Submissions','view_options'=>array('allow_add'=>false)));
		

		if($form_values and $g=$form_values->grid){
			$btn = $g->addButton('Mark All Read');
			
			if($btn->isClicked()){
				$temp = $form_values->getModel();
				$temp->_dsql()->set('is_read',1)->update();
				$g->js()->reload()->execute();
			}

			$form_values->grid->addClass('panel panel-default');
			$form_values->grid->setStyle('padding','20px');
			$form_values->grid->addPaginator(50);
			$form_values->grid->addQuickSearch(array('message'));
			$form_values->grid->addColumn('Button','Keep_Watch');
		}
			if($_GET['Keep_Watch']){			
			$custom_entry=$this->add('xEnquiryNSubscription/Model_CustomFormEntry')->load($_GET['Keep_Watch']);
			if($custom_entry['watch']==false)
				$custom_entry['watch']=true;
			else
				$custom_entry['watch']=false;
			$custom_entry->save();
			$form_values->grid->js(null,$this->js()->univ()->successMessage('Watch Changes'))->reload()->execute();
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