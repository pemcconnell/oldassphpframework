<?php

class _defaultController extends AdminIndexController
{
	public function __construct()
	{
		parent::__construct();
		
		header('Location:' . BASE_HREF . 'admin/login');
		exit;
	}
	
	public function __destruct()
	{
		parent::__destruct();
	}
}