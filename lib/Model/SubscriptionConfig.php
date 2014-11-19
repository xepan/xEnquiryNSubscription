<?php
namespace xEnquiryNSubscription;


class Model_SubscriptionConfig extends \Model_Table {
	var $table= "xEnquiryNSubscription_Subscription_Config";
	function init(){
		parent::init();

		$this->hasOne('xEnquiryNSubscription/SubscriptionCategories','category_id')->mandatory(true);
		
		$f=$this->addField('email_caption')->caption('Email ID Caption')->defaultValue('Email ID')->hint('Leave Empty to Hide')->group('a~4~<i class="fa fa-eye"/> Enquiry Form Visual');
		$f->icon='fa fa-adn~blue';

		$f=$this->addField('subscribe_caption')->defaultValue('Subscribe')->hint('Leave Empty to hide button')->group('a~4');
		$f->icon='fa fa-adn~blue';
		
		$f=$this->addField('placeholder_text')->defaultValue('Enter your Email Id')->group('a~4');
		$f->icon='fa fa-adn~blue';

		$f=$this->addField('thank_you_msg')->defaultValue('Thank You for Subscription')->group('b~6~<i class=""/> After Subscription');
		$f->icon='fa fa-thumbs-o-up~blue';
		$f=$this->addField('flip_the_html')->type('text')->defaultValue('<h2 class="alert alert-info"> Thank You :) <script>alert("Thank You");</script></h2>')->hint('"<h2 class="alert alert-info"> Thank You :) <script>alert("Thank You");</script></h2>" :: or leave empty to bypass the feature')->group('b~6');
		$f->icon='fa fa-angellist~blue';

		$f=$this->addField('allow_non_email_entries')->type('boolean')->defaultValue(false)->hint('To take any other single values like Phone no etc.')->group('c~6~<i class="fa fa-cog"/> Subscription Settings');
		$f->icon='fa fa-exclamation~blue';
		$f=$this->addField('allow_re_subscribe')->type('boolean')->defaultValue(true)->hint('Will not create another email entry though')->group('c~6');
		$f->icon='fa fa-exclamation~blue';
		
		$f=$this->addField('send_response_email')->type('boolean')->defaultValue(false)->group('d~2~<i class="fa fa-envelope"/> Auto respond Email Settings');
		$f->icon='fa fa-exclamation~blue';
		
		$f=$this->addField('email_subject')->group('d~10');
		$f->icon='fa fa-quote-left~blue';

		$f=$this->addField('email_body')->type('text')->display(array('form'=>'RichText'))->group('d~10~bl');
		$f->icon='fa fa-quote-left~blue';

		// $this->add('dynamic_model/Controller_AutoCreator');
	}
}