<?php
class page_xEnquiryNSubscription_page_owner_form extends page_xEnquiryNSubscription_page_owner_main{
	public $menubar;
	

	function init(){
		parent::init();

		$message_vp = $this->add('VirtualPage');
		$message_vp->set(function($p){
			$m=$p->add('xEnquiryNSubscription/Model_CustomFormEntry')->tryLoad($_GET['form_submission_id']);
			$p->add('View')->setHTML($m['message'])->addCLass('well');
		});

		$this->app->layout->template->trySetHTML('page_title','<i class="fa fa-bullhorn"></i> '.$this->component_name. '<small> Manage Your Custom Forms And submitted data</small>');

		$total_custom_form=$this->setModel('xEnquiryNSubscription/Model_Forms')->count()->getOne();
		$total_submission_entry=$this->add('xEnquiryNSubscription/Model_CustomFormEntry')->count()->getOne();
		$unread_entry=$this->add('xEnquiryNSubscription/Model_CustomFormEntry')->addCondition($this->api->db->dsql()->orExpr()->where('is_read',0)->where('watch',1))->count()->getOne();

		$bg=$this->app->layout->add('View_BadgeGroup');
		$v=$bg->add('View_Badge')->set('Total Forms')->setCount($total_custom_form)->setCountSwatch('ink');
		$v=$bg->add('View_Badge')->set('Total Forms Submissions')->setCount($total_submission_entry)->setCountSwatch('green');
		
		if($unread_entry) $v=$bg->add('View_Badge')->set('Unread / Watch')->setCount($unread_entry)->setCountSwatch('red');

		$crud=$this->app->layout->add('CRUD');
		$crud->setModel('xEnquiryNSubscription/Model_Forms',array('name','receive_mail','receipent_email_id','un_read_submission'));
		// $crud->add('Controller_FormBeautifier');

		// if(!$crud->isEditing()){
		// 	$crud->add_button->setIcon('ui-icon-plusthick');
		// }

		$refcrud=$crud->addRef('xEnquiryNSubscription/CustomFields',array('label'=>'Add Fields'));
		$form_values = $crud->addRef('xEnquiryNSubscription/CustomFormEntry',array('label'=>'Submissions','view_options'=>array('allow_add'=>false),'grid_fields'=>array('forms','created_at','ip','message','is_read','watch')));
		
		if($form_values and (!$form_values->isEditing())){
			$g=$form_values->grid;
			$btn = $g->addButton('Mark All Read');
			
			if($btn->isClicked()){
				$temp = $form_values->getModel();
				$temp->_dsql()->set('is_read',1)->update();
				$g->js()->reload()->execute();
			}

			$form_values->grid->addClass('panel panel-default');
			$form_values->grid->addPaginator(50);
			$form_values->grid->addQuickSearch(array('message'));
			$form_values->grid->addColumn('Button','Keep_Watch');

			$g->addMethod('format_message',function($g,$f)use($message_vp){
				$g->current_row_html[$f]='<a href="javascript:void(0)" onclick="'.$g->js()->univ()->frameURL('Message',$g->api->url($message_vp->getURL(),array('form_submission_id'=>$g->model->id))).'">'.$g->current_row[$f].'</a>';
			});

			$g->addFormatter('message','message');

		}
		if($_GET['Keep_Watch']){
			$custom_entry=$this->add('xEnquiryNSubscription/Model_CustomFormEntry')->load($_GET['Keep_Watch']);
	
			if($custom_entry['watch']==false)
				$custom_entry['watch']=true;
			else
				$custom_entry['watch']=false;
			
			$custom_entry->save();
			$form_values->grid->js(null,array($bg->js()->reload(),$this->js()->univ()->successMessage('Watch Changes')))->reload()->execute();
		
		}

		if($refcrud and (!$refcrud->isEditing())){
			$refcrud->add_button->setIcon('ui-icon-plusthick');
		}

		if($refcrud and $refcrud->isEditing()){

			// $set_value_field=$refcrud->form->getElement('set_value');
			// $expandable_field=$refcrud->form->getElement('type');
			// 	$expandable_field->js(true)->univ()
			// 	->bindConditionalShow(array(""=>array(),
			// 					"dropdown"=>array('set_value'),
			// 					"radio"=>array('set_value')
			// 					),'div .atk-form-row');
			
		}

		
		if($refcrud){
			// $refcrud->add('Controller_FormBeautifier');
		}

	}
}