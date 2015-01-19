<?php
class LogoutController extends AdminBaseController
{
	public function __construct()
	{
		parent::__construct(false);
		$this->processCMSLogout();
	}
}