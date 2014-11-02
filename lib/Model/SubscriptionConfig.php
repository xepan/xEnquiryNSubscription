<?php
namespace xEnquiryNSubscription;


class Model_SubscriptionConfig extends \Model_Table {
	var $table= "xEnquiryNSubscription_Subscription_Config";
	function init(){
		parent::init();

		$this->hasOne('xEnquiryNSubscription/SubscriptionCategories','category_id')->mandatory(true);
		
		$this->addField('email_caption')->caption('Email ID Caption')->defaultValue('Email ID')->hint('Leave Empty to Hide');
		$this->addField('subscribe_caption')->defaultValue('Subscribe')->hint('Leave Empty to hide button');
		$this->addField('placeholder_text')->defaultValue('Enter your Email Id');
		$this->addField('thank_you_msg')->defaultValue('Thank You for Subscription');
		$this->addField('flip_the_html')->type('text')->defaultValue('<h2 class="alert alert-info"> Thank You :) <script>alert("Thank You");</script></h2>')->hint('"<h2 class="alert alert-info"> Thank You :) <script>alert("Thank You");</script></h2>" :: or leave empty to bypass the feature');
		$this->addField('allow_non_email_entries')->type('boolean')->defaultValue(false)->hint('To take any other single values like Phone no etc.');
		$this->addField('allow_re_subscribe')->type('boolean')->defaultValue(true)->hint('Will not create another email entry though');
		$this->addField('send_response_email')->type('boolean')->defaultValue(false);
		$this->addField('email_subject');
		$this->addField('email_body')->type('text')->display(array('form'=>'RichText'));

		// $this->add('dynamic_model/Controller_AutoCreator');
	}
}