<?php
/**
 * settings.php
 *
 * Basic site configuration such as database connection and developer email address
 */
// START SITE CONFIG
$db = '__framework';
$dbuname = 'mydbuser';
$dbpwd = 'mydbpwd';
$dbhost = 'localhost';
$devemail = 'me@domain.com';

// SERVER OVERRIDES
if(!LOCALMODE) $dbpwd = 'LIVEPASSWD';
if(LOCALMODE) $devemail = 'me@domain.com'; 

// ENABLE FIREBUG FOR ALL BROWSERS
$bEnableFirebug = false;
if(LOCALMODE) $bEnableFirebug = false;
define('ENABLE_FIREBUG', $bEnableFirebug);

// SETTINGS DEFAULTS
$SETTINGS = array(
	'db' => array(
		'host' => $dbhost,
		'database' => $db,
		'username' => $dbuname,
		'password' => $dbpwd
	),
	'mvc' => array(
		'zones' => array( # Zones refer to base folder AND base controller name
			'frontend',
			'admin'
		),
		'defaults' => array(
			'zone' => 'frontend',
			'controller' => '_default', // LOADED AS 'TEMPLATE' PAGE
			'homecontroller' => 'home', // LOADED AS HOMEPAGE
			'view' => 'index',
			'pagetarget' => 'pages'
		)
	),
	'info' => array(
		'developer' => $devemail,
		'author' => 'Peter McConnell',
		'version' => '0.0.9',
		'lastupdated' => '01 May 2012'
	),
	'auth' => array(
		'salt' => '*4dAL%D_(',
		'pepper' => 'mIs@D(($ds9'
	),
	'uploadpath' => BASE_PATH . 'tmp' . DS . 'uploads' . DS,
	'errlog' => 'err.log',
	'bLogEmails' => true,
	'bUseCustomErrorHandling' => true
);
