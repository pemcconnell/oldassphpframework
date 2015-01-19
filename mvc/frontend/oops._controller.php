<?php

class OopsController extends FrontendBaseController
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function code($code)
	{
		global $CONSOLE;
		$CONSOLE->warning('Encountered ' . $code);
		if(HttpError::isError($code))
		{
			header(HttpError::httpHeaderFor($code));
		}
		if(!HttpError::canHaveBody($code)) exit;
	}
	
	public function __destruct()
	{
		parent::__destruct();
	}
}