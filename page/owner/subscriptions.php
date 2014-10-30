<?php

class page_xEnquiryNSubscription_page_owner_subscriptions extends page_xEnquiryNSubscription_page_owner_main {

	function init(){
		parent::init();
		$this->rename('xEnSpos');
		
	}

	function page_index(){
		$tabs= $this->add('Tabs');

		$subscription_cat_tab = $tabs->addTab('Categories');
		$subscriptions_tab = $tabs->addTab('Subscriptions');
		
		$subscriptions_cat_curd = $subscription_cat_tab->add('CRUD');
		$subscriptions_cat_curd->setModel('xEnquiryNSubscription/SubscriptionCategories');

		if($g=$subscriptions_cat_curd->grid){
			$g->addColumn('Expander','config');
		}

		$subscriptions_curd = $subscriptions_tab->add('CRUD');
		$subscriptions_curd->setModel('xEnquiryNSubscription/Subscription')->setOrder('subscribed_on','desc');
		if($g = $subscriptions_curd->grid){
			$g->sno=1;
			$g->addMethod('format_sno',function($grid,$field){
				$skip=0;
				foreach ($_GET as $key => $value) {
					if(strpos($key, '_paginator_skip') !== false) $skip = $_GET[$key];
				}
				$grid->current_row[$field] = $grid->sno + $skip;
				$grid->sno++;
			});

			$g->addColumn('sno','sno');
			$g->addOrder()->move('sno','first')->now();

			$subscriptions_curd->grid->addPaginator(100);
			$subscriptions_curd->grid->addButton('Upload Data')->js('click')->univ()->frameURL('Data Upload',$this->api->url('./upload'));
		}

		$news_letter_tab = $tabs->addTabURL($this->api->url('./newsletter'),'News Letters');
		// $email_config = $tabs->addTabURL($this->api->url('./emailconfig'),'Email Configuration');
		
	}

	function page_config(){

		$this->api->stickyGET('xEnquiryNSubscription_Subscription_Categories_id');

		$config_form = $this->add('Form');
		$config_model=$this->add('xEnquiryNSubscription/Model_SubscriptionConfig');
		$config_model->addCondition('category_id',$_GET['xEnquiryNSubscription_Subscription_Categories_id']);
		$config_model->tryLoadAny();

		$config_form->setModel($config_model);
		$config_form->addSubmit('Update');

		if($config_form->isSubmitted()){
			$config_form->update();
			$config_form->js()->reload()->execute();
		}

		// $config_form->getElement('email_body')->js(true)->_load('tinymce/xepan.tinymce')->univ()->xtinymce();
		// $config_form->getElement('email_body')->js(true)->tinymce(array('script_url'=>'templates/js/tinymce/tinymce.min.js',
		// 	'toolbar1'=>'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
		// 	'toolbar2' => 'print preview media | forecolor backcolor emoticons',
		// 	'image_advtab' =>true, 
		// 	'plugins'=> 'advlist autolink lists link image charmap print preview hr anchor pagebreak searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking save table contextmenu directionality emoticons template paste textcolor',
		// 	'file_browser_callback'=> $this->js()->alert('HI')->_enclose()
		// 	));

	}

	function page_newsletter(){
		$newsletter_model = $this->add('xEnquiryNSubscription/Model_NewsLetter');
		$newsletter_model->addExpression('unsend_emails')->set(function($m,$q){
			$q= $m->add('xEnquiryNSubscription/Model_EmailQueue');
			$q->join('xEnquiryNSubscription_EmailJobs','emailjobs_id')->addField('newsletter_id');
			return $q->addCondition('newsletter_id',$q->getField('id'))->addCondition('is_sent',false)->count();
		});


		$newsletter_crud = $this->add('CRUD',array('allow_edit'=>true));
		$newsletter_crud->setModel($newsletter_model);
		
		if($g=$newsletter_crud->grid){
			$g->addColumn('Expander','send');
			// $g->addColumn('Expander','update','Edit');
			// $btn=$g->addButton('Start Processing News Letters');
			// if($btn->isClicked()){
			// 	// ===== Actual Email Sending
			// 	$this->js()->univ()->frameURL('Sending Emails',$this->api->url('xEnquiryNSubscription_page_emailexec'))->execute();
				
			// }
		}

	}

	// function page_emailconfig(){
	// 	$mass_email=$this->add('xEnquiryNSubscription/Model_MassEmailConfiguration');
	// 	$mass_email->tryLoadAny();

	// 	$form=$this->add('Form');
	// 	$form->addSubmit('Update');
	// 	$form->setModel($mass_email);

	// 	if($form->isSubmitted()){
	// 		$form->update();
	// 		$form->js(null,$form->js()->univ()->successMessage('Updated'))->reload()->execute();
	// 	}
	// }

	// function page_newsletter_update(){
	// 	$this->api->stickyGET('xEnquiryNSubscription_NewsLetter_id');

	// 	$newsletter_model = $this->add('xEnquiryNSubscription/Model_NewsLetter');
	// 	$newsletter_model->load($_GET['xEnquiryNSubscription_NewsLetter_id']);

	// 	$form = $this->add('Form');
	// 	$form->setModel($newsletter_model);
	// 	$form->addSubmit('Update');

	// 	if($form->isSubmitted()){
	// 		$form->update();
	// 		$form->js(null,$form->js()->univ()->successMessage('Updated'))->reload()->execute();
	// 	}

	// }

	function page_newsletter_send(){
		$this->api->stickyGET('xEnquiryNSubscription_NewsLetter_id');

		$tabs = $this->add('Tabs');
		$mass_email_tab = $tabs->addTab('Mass Emails');
		$mass_email_tab->add('View_Error')->set("This will add Emails to Queue to be processed by xMarketingCampain Application");

		$form = $mass_email_tab->add('Form');
		$crud= $mass_email_tab->add('CRUD');

		$subscription_field = $form->addField('DropDown','subscriptions');
		$subscription_field->setModel('xEnquiryNSubscription/SubscriptionCategories');
		$subscription_field->setEmptyText('Please select a category')->validateNotNull();
		$form->addField('CheckBox','include_unsubscribed_members_too');
		$form->addSubmit('Add To job');
		
		if($form->isSubmitted()){
			$subscribers = $this->add('xEnquiryNSubscription/Model_Subscription');
			$subscribers->addCondition('category_id',$form['subscriptions']);
			if(!$form['include_unsubscribed_members_too'])
				$subscribers->addCondition('send_news_letters',true);
			
			$new_job = $this->add('xEnquiryNSubscription/Model_EmailJobs');
			$new_job['newsletter_id'] = $_GET['xEnquiryNSubscription_NewsLetter_id'];
			$new_job->save();

			$q= $this->add('xEnquiryNSubscription/Model_EmailQueue');
			foreach ($subscribers as $junk) {
				$q['emailjobs_id'] = $new_job->id;
				$q['subscriber_id'] = $subscribers->id;
				$q->saveAndUnload();
			}
			if($crud->grid) $crud->grid->js()->reload()->execute();
		}

		$existing_jobs = $this->add('xEnquiryNSubscription/Model_EmailQueue');
		$job_j = $existing_jobs->join('xEnquiryNSubscription_EmailJobs','emailjobs_id');
		$job_j->addField('newsletter_id');
		$existing_jobs->addCondition('newsletter_id',$_GET['xEnquiryNSubscription_NewsLetter_id']);
		$existing_jobs->setOrder('id','desc');

		$subscriber_join = $existing_jobs->leftJoin('xEnquiryNSubscription_Subscription','subscriber_id');
		// $subscriber_join->addField('subscriber','name');

		$category_join = $subscriber_join->leftJoin('xEnquiryNSubscription_Subscription_Categories','category_id');
		$category_join->addField('under_category','name');

		$crud->setModel($existing_jobs);
		if($crud->grid) $crud->grid->addPaginator(50);

		// ================ SINGLE EMAIL

		$single_email_tab = $tabs->addTab('Send To Single');
		$single_email_tab->add('View_Info')->set('This Email will be send immidiate and will not be pending in Queue');
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
				$subs['category_id'] = $single_form['add_to_category'];
				$subs['email'] = $single_form['email_id'];
				$subs->save();
			}

			$new_job = $this->add('xEnquiryNSubscription/Model_EmailJobs');
			$new_job['newsletter_id'] = $_GET['xEnquiryNSubscription_NewsLetter_id'];
			$new_job->save();

			$q= $this->add('xEnquiryNSubscription/Model_EmailQueue');
			$q['emailjobs_id'] = $new_job->id;
			$q['email'] = $single_form['email_id'];
			$q->save();
			$q->processSingle();
			$single_form->js(null,$single_form->js()->univ()->successMessage('Done'))->reload()->execute();
		}

	}

	function page_upload(){
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

						$new_category->destroy();
					}

					$new_subscription = $this->add('xEnquiryNSubscription/Model_Subscription');
					$new_subscription->addCondition('category_id', array_search($d['Category'], $stored_categories));
					$new_subscription->addCondition('email', $d['Email']);
					$new_subscription->tryLoadAny();
					$new_subscription['send_news_letters'] = $d['Send News Letters'];
					$new_subscription['subscribed_on'] = date('Y-m-d',strtotime($d['Subscribed On']));
					$new_subscription->saveAndUnload();

				}

				$this->add('View_Info')->set(count($data).' Recored Imported');

			}
		}
	}
}
