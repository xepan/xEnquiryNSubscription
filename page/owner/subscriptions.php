<?php

class page_xEnquiryNSubscription_page_owner_subscriptions extends page_xEnquiryNSubscription_page_owner_main {

	function init(){
		parent::init();
		$this->rename('xEnSubs');
		
	}

	
	function page_categories(){

		// Add Top Bar
		$bv = $this->add('View_BackEndView',array('cols_widths'=>array(12)));
		$bv->addToTopBar('H3')->set('Subscription Categories');
		$op = $bv->addOptionButton();
		$crud = $bv->addToColumn(0,'View');

		$sub_cat_model= $this->add('xEnquiryNSubscription/Model_SubscriptionCategories');
		$sub_cat_model->addExpression('last_communicated')->sortable(true)->set(function($m,$q){

			$mq=$m->add('xEnquiryNSubscription/Model_EmailQueue',array('table_alias'=>'tmq'));
			$s_j=$mq->join('xEnquiryNSubscription_Subscription','subscriber_id');
			$as_j=$s_j->join('xEnquiryNSubscription_SubsCatAss.subscriber_id');
			$as_j->addField('category_id');

			$mq->addCondition('category_id',$q->getField('id'));
			$mq->addCondition('is_sent',true);
			$mq->setOrder('sent_at','desc');
			$mq->setLimit(1);
			return $mq->fieldQuery('sent_at');
		});

		$subscriptions_cat_curd = $crud->add('CRUD');
		$subscriptions_cat_curd->setModel($sub_cat_model);
		$subscriptions_cat_curd->add('Controller_FormBeautifier');

		if($g=$subscriptions_cat_curd->grid){
			$subscriptions_cat_curd->add_button->setIcon('ui-icon-plusthick')->set("New");
			$g->addPaginator(20);
			
			$qs = $g->addQuickSearch(array('name'));

			$qs->search_field->setAttr('placeholder','Search Categories by Name');

			$g->addColumn('Expander','config');
			$g->addTotals(array('total_emails'));
			$g->removeColumn('epan');
			$g->add_sno();
			$g->order->move('is_active','after','s_no');

		}



		// Subscribers of this Category section
		$cat_ref_subs_crud = $subscriptions_cat_curd->addRef('xEnquiryNSubscription/Model_SubscriptionCategoryAssociation',array('label'=>'Subscribers','grid_fields'=>array('subscriber','send_news_letters','subscribed_on')));

		if($cat_ref_subs_crud){
			$cat_ref_subs_crud->add('Controller_FormBeautifier');
		}

		if($cat_ref_subs_crud and $g=$cat_ref_subs_crud->grid){
			$cat_ref_subs_crud->grid->addClass('panel panel-default');
			$cat_ref_subs_crud->grid->addStyle('padding','20px');
			$cat_ref_subs_crud->grid->addPaginator(100);
			$cat_ref_subs_crud->grid->addQuickSearch(array('subscriber'));
			$cat_ref_subs_crud->add_button->setIcon('ui-icon-plusthick');
			$cat_ref_subs_crud->add_button->set('New');
			$g->add_sno();
		}

	}

	function page_total_subscriptions(){
		$bv = $this->add('View_BackEndView',array('cols_widths'=>array(12)));
		$bv->addToTopBar('H3')->set('Total Subscription');
		$total_subscription=$this->setModel('xEnquiryNSubscription/Model_Subscription')->count()->getOne();
		$bv->addToTopBar('View')->set('Total Subscriptions -'.$total_subscription);		
		$op = $bv->addOptionButton();
		$crud = $bv->addToColumn(0,'View');

		$subscriptions_curd = $this->add('CRUD');
		$subscriptions_curd->setModel('xEnquiryNSubscription/Model_Subscription',null,array('email','is_ok','created_at'));
		if($g = $subscriptions_curd->grid){
			$subscriptions_curd->add_button->seticon('ui-icon-plusthick');
			// $g->sno=1;
			// $g->addMethod('format_sno',function($grid,$field){
			// 	$skip=0;
			// 	foreach ($_GET as $key => $value) {
			// 		if(strpos($key, '_paginator_skip') !== false) $skip = $_GET[$key];
			// 	}
			// 	$grid->current_row[$field] = $grid->sno + $skip;
			// 	$grid->sno++;
			// });

			// $g->addColumn('sno','sno');
			// $g->addOrder()->move('sno','first')->now();
			$g->add_sno();
			
			$subscriptions_curd->grid->addPaginator(100);
			$subscriptions_curd->grid->addQuickSearch(array('email'));
			$upl_btn=$subscriptions_curd->grid->addButton('Upload Data');
			$upl_btn->setIcon('ui-icon-arrowthick-1-n');
			$upl_btn->js('click')->univ()->frameURL('Data Upload',$this->api->url('./upload'));
		}
		$subscriptions_curd->add('Controller_FormBeautifier');

		$cat_ref_subs_crud = $subscriptions_curd->addRef('xEnquiryNSubscription/Model_SubscriptionCategoryAssociation',array('label'=>'Categories'));

		if($cat_ref_subs_crud){
			$cat_ref_subs_crud->add('Controller_FormBeautifier');
		}

		if($cat_ref_subs_crud and $cat_ref_subs_crud->grid){
			$cat_ref_subs_crud->add_button->setIcon('ui-icon-plusthick');
			$cat_ref_subs_crud->grid->addClass('panel panel-default')->addStyle('padding','10px');
			$cat_ref_subs_crud->grid->addPaginator(100);
			$cat_ref_subs_crud->grid->addQuickSearch(array('category'));
		}

	}

	function page_categories_config(){

		$this->api->stickyGET('xEnquiryNSubscription_Subscription_Categories_id');

		$v=$this->add('View');
		$v->addClass('panel panel-danger');
		$v->addStyle('padding','20px');

		$config_form = $v->add('Form');
		$config_model=$this->add('xEnquiryNSubscription/Model_SubscriptionConfig');
		$config_model->addCondition('category_id',$_GET['xEnquiryNSubscription_Subscription_Categories_id']);
		$config_model->tryLoadAny();

		$config_form->setModel($config_model);
		$config_form->addSubmit('Update');

		if($config_form->isSubmitted()){
			$config_form->update();
			$config_form->js()->reload()->execute();
		}

		$config_form->add('Controller_FormBeautifier',array('modifier'=>'default'));

	}

	function page_newsletter(){
		$config_model=$this->add('xEnquiryNSubscription/Model_Config')->tryLoadAny();

		// Add Top Bar
		$bv = $this->add('View_BackEndView',array('cols_widths'=>array(12)));
		$bv->addToTopBar('H3')->set('News Letters');
		$total_newsletter=$this->add('xEnquiryNSubscription/Model_NewsLetter');
		$total=$total_newsletter->addCondition('created_by','xEnquiryNSubscription')->count()->getOne();
		$bv->addToTopBar('View')->set('Total xEnquiryNSubscription NewsLetter - '.$total);
		$op = $bv->addOptionButton($this->api->url('./config'));
		$crud = $bv->addToColumn(0,'View');

		$newsletter_model = $this->add('xEnquiryNSubscription/Model_NewsLetter');
		$newsletter_model->addExpression('unsend_emails')->set(function($m,$q){
			$mq= $m->add('xEnquiryNSubscription/Model_EmailQueue');
			$mq->join('xEnquiryNSubscription_EmailJobs','emailjobs_id')->addField('newsletter_id');
			return $mq->addCondition('newsletter_id',$q->getField('id'))->addCondition('is_sent',false)->count();
		})->sortable(true);

		if(!$config_model['show_all_newsletters']){
			$newsletter_model->addCondition('created_by','xEnquiryNSubscription');
		}
		
		$newsletter_crud = $this->add('CRUD');
		$newsletter_crud->setModel($newsletter_model,null,array('name','email_subject','unsend_emails','created_by'));
		$newsletter_crud->add('Controller_FormBeautifier');

		if($g=$newsletter_crud->grid){
			$g->addClass('newsletter_grid');
			$g->js('reload')->reload();

			if(!$config_model['show_all_newsletters']){
				$g->removeColumn('created_by');
			}


			$g->addColumn('Expander','send');
			$newsletter_crud->add_button->setIcon('ui-icon-plusthick');
			
			$btn=$g->addButton("");
			
			if($btn->isClicked()){
				$this->js()->univ()->frameURL('Executing Email Sending Process',$this->api->url('xEnquiryNSubscription_page_emailexec'))->execute();
			}

			$email_to_process = $this->add('xEnquiryNSubscription/Model_EmailQueue');
			$email_to_process->addCondition('is_sent',false);
			$email_to_process->setOrder('id','asc');
			$email_to_process->setOrder('emailjobs_id','asc');

			$job_j = $email_to_process->join('xEnquiryNSubscription_EmailJobs','emailjobs_id');
			$job_j->addField('process_via');
			$email_to_process->addCondition('process_via','xEnquiryNSubscription');
			$pending_count = $email_to_process->count()->getOne();

			$btn->setIcon('ui-icon-seek-end');
			$btn->set("Start Processing Sending, Now ($pending_count)");
			$btn->addClass('processing_btn');
			$btn->js('reload')->reload();
		}

	}

	function page_newsletter_config(){
		$config_model = $this->add('xEnquiryNSubscription/Model_Config');
		$config_model->tryLoadAny();

		$form = $this->add('Form');
		$form->setModel($config_model);
		$form->addSubmit('Update');

		$form->add('Controller_FormBeautifier');

		if($form->isSubmitted()){
			$form->update();
			$form->js(null,$form->js()->_selector('.newsletter_grid')->trigger('reload'))->univ()->closeDialog()->execute();
		}

	}


	function page_newsletter_send(){
		$this->api->stickyGET('xEnquiryNSubscription_NewsLetter_id');

		$v= $this->add('View');
		$v->addClass('panel panel-default');
		$v->addStyle('padding','20px');

		$tabs = $v->add('Tabs');
		$mass_email_tab = $tabs->addTab('Mass Emails');
		// $mass_email_tab->add('View_Error')->set("This will add Emails to Queue to be processed by xMarketingCampain Application");

		$form = $mass_email_tab->add('Form');
		
		$mass_email_tab->add('H4')->set('Existing Queue');

		$crud= $mass_email_tab->add('CRUD',array('allow_edit'=>false));
		$crud->addClass('panel panel-default');
		$crud->addStyle('margin-top','10px');

		$subscription_field = $form->addField('DropDown','subscriptions');
		$subscription_field->setModel('xEnquiryNSubscription/SubscriptionCategories');
		$subscription_field->setEmptyText('Please select a category')->validateNotNull();
		$form->addField('CheckBox','include_unsubscribed_members_too');
		$form->addSubmit('Add To job');

		$form->add('Controller_FormBeautifier');
		
		if($form->isSubmitted()){
			$subscribers = $this->add('xEnquiryNSubscription/Model_Subscription');
			$asso_j = $subscribers->join('xEnquiryNSubscription_SubsCatAss.subscriber_id');
			// $asso_j->addField('category_id');
			$asso_j->addField('send_news_letters');

			$subscribers->addCondition('category_id',$form['subscriptions']);
			if(!$form['include_unsubscribed_members_too'])
				$subscribers->addCondition('send_news_letters',true);
			
			$new_job = $this->add('xEnquiryNSubscription/Model_EmailJobs');
			$new_job['newsletter_id'] = $_GET['xEnquiryNSubscription_NewsLetter_id'];
			$new_job['process_via']='xEnquiryNSubscription';
			$new_job->save();

			$q= $this->add('xEnquiryNSubscription/Model_EmailQueue');
			foreach ($subscribers as $junk) {
				$q['emailjobs_id'] = $new_job->id;
				$q['subscriber_id'] = $subscribers->id;
				$q->saveAndUnload();
			}
			if($crud->grid) {
				$crud->grid->js(null,$this->js()->_selector('.processing_btn')->trigger('reload'))->reload()->execute();
			}
		}

		$existing_jobs = $this->add('xEnquiryNSubscription/Model_EmailQueue');
		$job_j = $existing_jobs->leftJoin('xEnquiryNSubscription_EmailJobs','emailjobs_id');
		$job_j->addField('newsletter_id');
		$existing_jobs->addCondition('newsletter_id',$_GET['xEnquiryNSubscription_NewsLetter_id']);
		$existing_jobs->setOrder('id','desc');

		$subscriber_join = $existing_jobs->leftJoin('xEnquiryNSubscription_Subscription','subscriber_id');
		// $subscriber_join->addField('subscriber','name');

		$subscriber_asso = $subscriber_join->leftJoin('xEnquiryNSubscription_SubsCatAss.subscriber_id');
		$category_join = $subscriber_asso->leftJoin('xEnquiryNSubscription_Subscription_Categories','category_id');
		$category_join->addField('under_category','name');

		$crud->setModel($existing_jobs);

		if($crud->grid){
			// $form=$crud->grid->add('Form',null,'grid_buttons',array('form_horizontal'));
			// $form->addField('DropDown','top_1');
			$crud->add_button->setIcon('ui-icon-plusthick');
			$crud->grid->addPaginator(50);
			$crud->grid->addQuickSearch(array('emailjobs','subscriber','email'));
		}

		// ================ SINGLE EMAIL

		$single_email_tab = $tabs->addTab('Send To Single');
		$single_form = $single_email_tab->add('Form');
		$single_form->addField('line','email_id')->validateNotNull();
		$single_form->addField('CheckBox','also_add_to_category');
		$single_form->addField('DropDown','add_to_category')->setModel('xEnquiryNSubscription/SubscriptionCategories');
		$single_form->addSubmit('Send');

		if($single_form->isSubmitted()){
			
			if($single_form['also_add_to_category']){
				if(!$single_form['add_to_category'])
					$single_form->displayError('add_to_category','Select Category');

				$subs = $this->add('xEnquiryNSubscription/Model_Subscription');
				$subs->addCondition('email',$single_form['email_id']);
				$subs->tryLoadAny();

				if(!$subs->loaded()){
					// $subs['category_id'] = $single_form['add_to_category'];
					$subs['email'] = $single_form['email_id'];
					$subs->save();
				}

				$cat = $this->add('xEnquiryNSubscription/Model_SubscriptionCategories');
				$cat->load($single_form['add_to_category']);
				try{
					$cat->addSubscriber($subs);
				}catch(\Exception $e){
					// Might be already associated
				}

			}

			$new_job = $this->add('xEnquiryNSubscription/Model_EmailJobs');
			$new_job['newsletter_id'] = $_GET['xEnquiryNSubscription_NewsLetter_id'];
			$new_job->save();

			$q= $this->add('xEnquiryNSubscription/Model_EmailQueue');
			$q['emailjobs_id'] = $new_job->id;
			$q['email'] = $single_form['email_id'];
			$q->save();
			if($q->processSingle())
				$single_form->js(null,$single_form->js()->univ()->successMessage('Done'))->reload()->execute();
			else
				$single_form->js(null,$single_form->js()->univ()->errorMessage('Error'))->reload()->execute();
		}

	}
 
	function page_total_subscriptions_upload(){
		$this->add('View')->setElement('iframe')->setAttr('src','index.php?page=xEnquiryNSubscription_page_owner_subscriptions_upload_execute&cut_page=1')->setAttr('width','100%');
	}


	function page_upload_execute(){
		$form= $this->add('Form');
		$form->template->loadTemplateFromString("<form method='POST' action='index.php?page=xEnquiryNSubscription_page_owner_subscriptions_upload_execute&cut_page=1' enctype='multipart/form-data'>
			<input type='file' name='subscribers_file'/>
			<input type='submit' value='Upload'/>
			</form>
			<br/>
			<small><a href='epan-components/xEnquiryNSubscription/templates/subscribe.csv'>click here to download sample file</a></small>

			");
		if($_FILES['subscribers_file']){
			if ( $_FILES["subscribers_file"]["error"] > 0 ) {
				$this->add( 'View_Error' )->set( "Error: " . $_FILES["subscribers_file"]["error"] );
			}else{
				if($_FILES['subscribers_file']['type'] != 'text/csv'){
					$this->add('View_Error')->set('Only CSV Files allowed');
					return;
				}

				$importer = new CSVImporter($_FILES['subscribers_file']['tmp_name'],true,',');
				$data = $importer->get(); 

				$existing_categories = $this->add('xEnquiryNSubscription/Model_SubscriptionCategories');
				$existing_categories_array = $existing_categories->getRows();

				$stored_categories=array();
				foreach ($existing_categories_array as $esc) {
					$stored_categories[$esc['id']] = $esc['name'];
				}

				// echo "<pre>";
				// print_r($data);
				// echo "</pre>";


				foreach ($data as $d) {
					if(!in_array($d['Category'], $stored_categories)){
						$new_category = $this->add('xEnquiryNSubscription/Model_SubscriptionCategories');
						$new_category['name'] = $d['Category'];
						$new_category->save();
						$stored_categories[$new_category->id] = $new_category['name'];
					}else{
						$new_category->load(array_search($d['Category'], $stored_categories));
					}

					$new_subscription = $this->add('xEnquiryNSubscription/Model_Subscription');
					$new_subscription->addCondition('email', $d['Email']);
					$new_subscription->tryLoadAny();
					$new_subscription['send_news_letters'] = $d['Send News Letters'];
					$new_subscription['subscribed_on'] = date('Y-m-d',strtotime($d['Subscribed On']));
					$new_subscription->save();

					$new_category->addSubscriber($new_subscription);
					
					$new_category->destroy();
					$new_subscription->destroy();
				}

				$this->add('View_Info')->set(count($data).' Recored Imported');

			}
		}
	}
}
