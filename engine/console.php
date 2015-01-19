<?php
if($SETTINGS['bUseCustomErrorHandling'])
{
	set_error_handler("customError");
}
/**
 * This class is in charge of ALL site error handling (developer and public)
 *  
 * @author Peter McConnell <pemcconnell@googlemail.com>
 *
 */
class Console
{
	public	$iFormErrCount = 0,
			$aMsgs,
			$aEmailMsg;
	
	public function __construct()
	{
		error_reporting(E_ALL);
		ini_set('display_errors', 1);
		
		$this->aMsgs = array(
			'user'	=> array('success' => array(), 'error' => array()),
			'sys'	=> array('warning' => array())
		);
	}
	
    /**
     * formerror
     * 
     * @param string $msg Text to display in the error message
     * 
     * @return null
     */
	public function formerror($msg)
	{
		$this->aMsgs['user']['error'][] = $msg;
		++$this->iFormErrCount;
	}
	
    /**
     * formsuccess
     * 
     * @param string $msg Text to display in the success message
     * 
     * @return null
     */
	public function formsuccess($msg)
	{
		$this->aMsgs['user']['success'][] = $msg;
	}
	
	/**
	 * exception()
	 * 
	 * Strict error catching. If the app is in LOCALMODE it will throw a 
     * standard exception. If not in LOCALMODE it will pass the message through 
     * to the aEmailMsgs stack
	 * 
	 * @param string $msg  Message to display as the exception
	 * @param string $type The $type that gets sent to addEmailMsg()
     * 
     * @return null
	 */
	public function exception($msg, $type = 'Exception')
	{
		if(LOCALMODE)
		{
			echo '<pre>';
			throw new Exception($msg);
			echo '</pre>';
		} else {
			$this->addEmailMsg($msg, $type);
		}
	}
	
	/**
	 * warning()
	 * 
	 * This method should only be used when wanting to track data that could 
     * POSSIBLY be erroneous. It has no default output so to view the warnings 
     * an output source should be typed manually 
     * (print_r(console->aMsgs['sys']['warning'], true);)
	 * 
	 * @param string $msg
     * 
     * @return null
	 */
	public function warning($msg)
	{
		$this->aMsgs['sys']['warning'][] = $msg;
	}
	
	/**
	 * error()
	 * 
	 * Pass the console a code / logic error. To be called for on logic fails 
     * and by the database abstraction layer. Transfers message to customError - 
     * the apps custom error handling function
	 * 
	 * @param string $msg Error      Message
	 * @param mixed $aPHPErrorParams PHP error information (array or false).
     * 
     * @return null
	 */
	public function error($msg, $aPHPErrorParams = false)
	{
		if (!is_array($aPHPErrorParams)) {
		    $aPHPErrorParams = array(
		      'errno' => '-1', 'errstr' => '', 'errfile' => 'not specified', 
		      'errline' => 'not specified'
          );
        } elseif (!isset($aPHPErrorParams['errno'])) {
            $aPHPErrorParams['errno'] = '';
        } elseif(!isset($aPHPErrorParams['errstr'])) {
            $aPHPErrorParams['errstr'] = '';
        } elseif(!isset($aPHPErrorParams['errfile'])) {
            $aPHPErrorParams['errfile'] = '';
        } elseif(!isset($aPHPErrorParams['errline'])) {
            $aPHPErrorParams['errline'] = '';
        }
        
		customError (
            $aPHPErrorParams['errno'], $msg, $aPHPErrorParams['errfile'], 
            $aPHPErrorParams['errline']
        );
	}
	
	/**
	 * addEmailMsg()
	 * 
	 * Adds an error message to the aEmailMsg array (email sends on __destruct())
	 * 
	 * @param string $msg  Message as it is to appear in the email
	 * @param string $type What type of error it is
     * 
     * @return null
	 */
	public function addEmailMsg($msg, $type = 'PHP Error')
	{
		// {$type . $msg} used as key to stop repetitive messages going into email
		$this->aEmailMsg[$type . $msg] = array( 'type' => $type,
												'msg' => $msg,
												'post' => $_POST,
												'get' => $_GET,
												'session' => (isset($_SESSION) ? $_SESSION : array()),
												'backtrace' => debug_backtrace());
	}
	
	/**
     * getUsersIP
     * 
	 * @return string IP address of active user
	 */
	private function getUsersIP()
	{
		if (isset($_SERVER['HTTP_X_FORWARD_FOR']))
		{
			$ip = $_SERVER['HTTP_X_FORWARD_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
	
	/**
	 * logEmail()
	 * 
	 * Takes email message, attempts to re-format for file save and appends 
     * that message to the errlogfile as defined in the $SETTINGS
	 * 
	 * @param string $html
     * 
     * @return null
	 */
	private function logEmail($html)
	{
		global $SETTINGS;
		if($SETTINGS['bLogEmails'])
		{
			if(strpos($html, '<body')!==false)
			{
				$html = strchr($html, '<body');
			}
			$ihtml = str_replace(array('&nbsp;', '<h1>', '</h1>', '<h2>', '</h2>', '<strong>', '</strong>', '<br />'), array(' ', '||||', '||||', '|||', '|||', '||', '||', "\n"), $html);
			$html = strip_tags($ihtml);
			$fh = fopen($SETTINGS['uploadpath'] . $SETTINGS['errlog'], 'a+');
			fwrite($fh, $html . "\n------------------------------------------------------------------\n");
			fclose($fh);
		}
	}
	
	/**
	 * compileAndSendEmail()
	 * 
	 * Accumulates all the aEmailMsg's and sends them in an email to the 
     * developer as defined in $SETTINGS
     * 
     * @return null
	 */
	private function compileAndSendEmail()
	{
		$count = count($this->aEmailMsg);
		if($count!=0)
		{
			global $SETTINGS;
			$to = $SETTINGS['info']['developer'];
			if(strpos($to, '@')!==false)
			{
				// SEND EMAIL
				$subj = 'Error Console: ' . BASE_HREF;
				$msg = '<html><head><style type="text/css">';
				$msg .= 'html { background:#CCC; } 
			            body { background:#CCC; margin:0; padding:15px; font-size:11px; } 
			            #wrapper { background:#FFF; padding:15px; } 
			            h2, strong { color:navy; } 
			            h1 { pading:0; margin-top:5px; }
			            </style></head><body><div id="wrapper">';
				$msg .= '<strong>' . $count . ' errors reported</strong><br />';
				$msg .= '<strong>URL:</strong> <a href="' . BASE_HREF . $_GET['uri'] . '">' . BASE_HREF . $_GET['uri'] . '</a><br />';
				$msg .= '<strong>Users IP:</strong> ' . $this->getUsersIP() . '<br />';
				$msg .= '<strong>Date/Time:</strong> ' . date('d/m/Y H:i:s') . '<br />';
				$msg .= '<strong>Base Href:</strong> ' . BASE_HREF . '<br />';
				$msg .= '<strong>Base Path:</strong> ' . BASE_PATH . '<br />';
				foreach ($this->aEmailMsg as $row)
				{
					$msg .= '<div style="padding:5px 10px; border:2px dashed orange;">';
					$typetitle = '<span style="color:orange;">' . $row['type'] . '</span>';
					if ($row['type'] != 'PHP Error') {
					    $typetitle = '<span style="color:#F00;">' . $row['type'] . '</span>';
				    }
					$msg .= "<h1>" . $typetitle . "</h1>";
					$msg .= "<h2>Message:</h2>\n" . $row['msg'] . "\n<br />";
					if (count($row['post'])!=0) $msg .= "<h2>POST DATA:</h2>\n<pre>" . print_r($row['post'], true) . "</pre>\n<br />";
					if (count($row['get'])!=0) $msg .= "<h2>GET DATA:</h2>\n<pre>" . print_r($row['get'], true) . "</pre>\n<br />";
					if (count($row['session'])!=0) $msg .= "<h2>SESSION DATA:</h2>\n<pre>" . print_r($row['session'], true) . "</pre>\n<br />";
					if ($row['type'] == 'Exception') {
						$msg .= "<h2>BACKTRACE:</h2>\n<pre>";
						array_shift($row['backtrace']);
						foreach ($row['backtrace'] as $row)
						{
							$msg .= 'Line ' . $row['line'] . ' : ' . $row['file'] . '<br />';
							if(isset($row['class'])) $msg .= $row['class'] . ' | ';
							if(isset($row['function'])) $msg .= $row['function'] . '()';
							$msg .= "<hr />";
						}
						$msg .= "</pre>\n<br />";
					}
					$msg .= "</div><br />&nbsp;";
				}
				$msg .= '</div></body></html>';
				$headers  = "MIME-Version: 1.0\r\nContent-type: text/html; charset=iso-8859-1\r\n";
				//$headers .= "From:no-reply@domain.com";
				
				$this->logEmail($msg);
				
				mail($to, $subj, $msg, $headers);
			}
		}
	}
	
    /**
     * __destruct
     * 
     * Sends debug email
     */
	public function __destruct()
	{
		$this->compileAndSendEmail();
	}
}

$CONSOLE = new Console;

/**
 * customError()
 * 
 * The applications custom error handling method
 * 
 * @param mixed $errno
 * @param string $errstr
 * @param string $errfile
 * @param int $errline
 * 
 * @return null
 */
function customError($errno, $errstr, $errfile, $errline)
{
	if (($errno == 8) && (strpos($errstr, 'ps_files_cleanup_dir')!==false)) {
	    return false; // SERVER GENERATED ERROR
    }
	$html = '<div class="phpError">';
	$html .= '<strong>Error [' . $errno . ']:</strong> ' . $errstr . ' on <span>line ' . $errline . '</span> in <span>' . $errfile . '</span><hr />';
	$html .= '<strong>Backtrace Preview:</strong><br />';
	$bt = debug_backtrace();
    array_shift($bt);
	foreach ($bt as $row)
	{
		if (isset($row['file'])) {
			$html .= $row['file'] . ' (Line ' . $row['line'] . ')';
			$html .= '<br />';
		}
	}
	$html .= '</div>';
	if (LOCALMODE) {
	    echo $html;
    } else {
		global $CONSOLE;
		$CONSOLE->addEmailMsg($html);
	}
	return;
}