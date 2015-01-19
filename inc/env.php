<?php
/**
 * env.php
 * 
 * Any server settings that are defined through PHP should be set in this file
 * @author Peter McConnell <pemcconnell@googlemail.com>
 */
if (function_exists('sys_getloadavg')) {
	$load = sys_getloadavg();
	if ($load[0] > 80) {
		header('HTTP/1.1 503 Too busy, try again later');
		die('Server too busy. Please try again later.');
	}
}

// Remove Magic Quotes if the server supports it
if (get_magic_quotes_gpc()) {
    $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
    while (list($key, $val) = each($process)) {
        foreach ($val as $k => $v) {
            unset($process[$key][$k]);
            if (is_array($v)) {
                $process[$key][stripslashes($k)] = $v;
                $process[] = &$process[$key][stripslashes($k)];
            } else {
                $process[$key][stripslashes($k)] = stripslashes($v);
            }
        }
    }
    unset($process);
}

date_default_timezone_set('Europe/Belfast');

set_time_limit(0);
ini_set('session.gc_maxlifetime', '3600');
header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"'); // PRIVACY POLICY HEADERS
header("Pragma: no-cache");
header("Cache-Control: no-cache");