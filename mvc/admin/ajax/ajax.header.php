<?php

// INITS FOR MERLIN CLASS
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
if(!defined('BASE_PATH')) define('BASE_PATH', strchr($_SERVER['SCRIPT_FILENAME'], 'mvc' . DS . 'admin', true));
if(!defined('LOCALMODE')) define('LOCALMODE', (strpos($_SERVER['HTTP_HOST'], 'localhost')!==false));
if(!defined('BASE_HREF'))
{
	$aP = explode("/admin/ajax/", strtolower($_SERVER['REQUEST_URI']));
	define('BASE_HREF', 'http://' . $_SERVER['HTTP_HOST'] . $aP[0] . '/');
}

require_once (BASE_PATH . 'settings.php');
require_once (BASE_PATH . 'engine' . DS . 'console.php');
require_once (BASE_PATH . 'engine' . DS . 'db.php');
require_once (BASE_PATH . 'engine' . DS . 'session.php');

if(!isset($CONSOLE) || !is_object($CONSOLE)) $CONSOLE = new Console;
if(!isset($DB) || !is_object($DB)) $DB = new DB;
if(!isset($SESSION) || !is_object($SESSION)) $SESSION = new Session;
$SESSION->start();