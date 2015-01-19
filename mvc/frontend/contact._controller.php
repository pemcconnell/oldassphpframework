<?php

class ContactController extends FrontendBaseController
{
    private     $sFormTo = 'pemcconnell@googlemail.com';
    
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		if(isset($_POST['sub_btn']))
		{
			$name = Validate::minlen($this->getVal('c_name_txt'), 'Please insert your name');
			$email = Validate::email($this->getVal('c_email_txt'), 'Please insert your email address');
			$tel = Validate::minlen($this->getVal('c_tel_txt'), 'Please insert your telephone number');
			$company = $this->getVal('c_company_txt');
			$msg = Validate::minlen($this->getVal('c_msg_txt'), 'Insert a message');
				
			if($this->console->iFormErrCount == 0)
			{
				$message = "Someone has got in touch via the website:\n\nName: " . $name . "\nEmail: " . $email . "\nTel: " . $tel . "\nCompany: " . $company . "\nMessage:\n" . $msg;
				if(@mail($this->sFormTo, 'Website Contact Form', $message))
				{
					$this->console->formsuccess('Thank you for getting in touch. We shall respond as promptly as possible.');
				} else {
					$this->console->formerror('Sorry but it appears there was an error in sending your message. Please get in touch via our contact details.');
					$this->console->exception('There was a problem sending the email via the contact form');
				}
			}
		}
	}
	
	public function __destruct()
	{
		parent::__destruct();
	}
}
