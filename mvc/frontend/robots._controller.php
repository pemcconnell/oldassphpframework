<?php

class RobotsController extends FrontendBaseController
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function txt()
	{
		header("Content-type:text");
		echo "Sitemap: " . BASE_HREF . "sitemap.xml\n";
		exit;
	}
	
	public function __destruct()
	{
		parent::__destruct();
	}
}