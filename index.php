<?php
/**
* Framework
*
* PHP version 5.2
*
* @author  Peter McConnell <pemcconnell@googlemail.com>
* @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
* @link    http://www.twitter.com/DesignThenCode
*/

// ENVIRONMENTAL CONSTANTS
$protocol = 'http://';
if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) {
    $protocol = 'https://';
}

define(
    'HREF', 
    $_SERVER['HTTP_HOST'] . str_replace('index.php', '', $_SERVER['PHP_SELF'])
);
define('BASE_HREF', 'http://' . HREF);
define('BASE_SSLIFON_HREF', $protocol . HREF);
define('BASE_PATH', str_replace('index.php', '', $_SERVER['SCRIPT_FILENAME']));
define('DS', DIRECTORY_SEPARATOR);
define('LOCALMODE', (strpos($_SERVER['HTTP_HOST'], 'localhost')!==false));
define(
    'LIVEMODE', 
    !LOCALMODE
);

// BOOTSTRAP
require BASE_PATH . 'bootstrap.php';

// START FRAMEWORK
$Framework = new Framework;
echo $Framework->display();