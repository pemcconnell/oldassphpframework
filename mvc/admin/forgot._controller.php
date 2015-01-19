<?php

class ForgotController extends AdminBaseController
{
	public function __construct()
	{
		parent::__construct(false);
		
		if($this->session->is_set('admin'))
		{ // SESSION LOGIN
			header('Location:' . BASE_HREF . 'admin/pages');
			exit;
		} elseif(isset($_COOKIE[$_COOKIE[BASE_HREF . '-mg']])) { // COOKIE LOGIN
			$this->authLoginCookie();
		}
		
		$this->templatevars['GBL_stylesheets'][] = BASE_HREF . 'admin/css/login.css';
	}
	
	public function index()
	{
		if(isset($_POST['sub_btn']))
		{
		    global $SETTINGS;
			// SEND EMAIL
			mail($SETTINGS['info']['developer'], 'A client has forgotten their password', 'The client ' . BASE_HREF . ' has forgotten their password.');
			
			$this->console->formsuccess('Your details have been sent to the web developer and we shall get in touch with your details as soon as possible.');
		}
	}
	
	public function __destruct()
	{
		parent::__destruct();
	}
}