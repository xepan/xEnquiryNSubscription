<?php

class page_xEnquiryNSubscription_page_owner_dashboard extends page_xEnquiryNSubscription_page_owner_main{

	function init(){
		parent::init();

		$total_emails =$this->add('xEnquiryNSubscription/Model_Subscription');
		$dv = $this->app->layout->add('View_BackEndView',array('cols_widths'=>array(12)));
		$mail=$total_emails->addCondition('from_app','xEnquiryNSubscription')->count()->getOne();		
		$dv->addToTopBar('View')->setHTML('Web Subscription -'.$mail)->addClass('label label-info');
		
		$total_other_subscription=$this->add('xEnquiryNSubscription/Model_Subscription');
		$other_subscription=$total_other_subscription->addCondition('from_app','<>','xEnquiryNSubscription')->count()->getOne();
		$dv->addToTopBar('View')->setHTML('Other Subscription - '.$other_subscription)->addClass('label label-danger');

		$subscriptions=$this->add('xEnquiryNSubscription/Model_Subscription')->count()->getOne();
		$dv->addToTopBar('View')->setHTML('Total Subscription - '.$subscriptions)->addClass('label label-default');

		$newsLetter=$this->add('xEnquiryNSubscription/Model_NewsLetter');
		$xenquiry_newsletter=$newsLetter->addCondition('created_by','xEnquiryNSubscription')->count()->getOne();
		$dv->addToTopBar('View')->setHTML('xEnquiryNSubscription NewsLetters -'.$xenquiry_newsletter)->addClass('label label-primary');

		$total_other_newsletter=$this->add('xEnquiryNSubscription/Model_NewsLetter');
		$other_newsletter=$total_other_newsletter->addCondition('created_by','<>','xEnquiryNSubscription')->count()->getOne();
		$dv->addToTopBar('View')->setHTML('Other NewsLetters - '.$other_newsletter)->addClass('label label-danger');

		$custom_form =$this->add('xEnquiryNSubscription/Model_Forms')->count()->getOne();		
		$dv->addToTopBar('View')->setHTML('Custom Form -'.$custom_form)->addClass('label label-success');

		$total_submission_entry =$this->add('xEnquiryNSubscription/Model_CustomFormEntry')->count()->getOne();		
		$dv->addToTopBar('View')->setHTML('Total Custom Form Submissions -'.$total_submission_entry)->addClass('label label-success');
		
		$is_read_watch=$this->add('xEnquiryNSubscription/Model_CustomFormEntry');
		$unread=$is_read_watch->addCondition($is_read_watch->dsql()->orExpr()
			->where('is_read',0)
			->where('watch',1)
			)->count()->getOne();
		$dv->addToTopBar('View')->setHTML('Un-read / Watch - '.$unread)->addClass('label label-danger');
			
		$dv_op = $dv->addOptionButton();
		$crud = $dv->addToColumn(0,'View');



		
		$chart=$this->app->layout->add('chart/Chart');

		$m=$this->add('xEnquiryNSubscription/Model_Subscription');
		// $m->addCondition('from_app','Website');
		$m->_dsql()->del('fields')
								->field('from_app')
								->field('count(*) subs')
								->field('date(created_at) dt')
								// ->where('created_at','>',date("Y_m-d",strtotime("- 1 month")))
								->group('from_app,date(created_at)');
		$x=$m->_dsql()->getAll();	
		// echo "<pre>";
		// print_r($x);							
		// echo "</pre>";
		// exit;							
		foreach($x as $junk) {
			// $m->addCondition('from_app','Website');
			$y=$chart->addLineData($junk['from_app'],$junk['dt'],(int)$junk['subs']);
		}
		echo $y;

		// $chart->addLineData('Website','2',15);
		$chart
		->setXAxisTitle('Dates')
		// ->setXAxis($xaxis)
		->setYAxisTitle('Total Subscription')
		->setTitle('Subscription Status',null,'sub Tittle')
		->setChartType('line')
		;
	}

}