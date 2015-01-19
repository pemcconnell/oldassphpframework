<?php
class Validate
{
	
	static public function minlen($str, $msg = false, $len = 1)
	{
		if(!is_numeric($len)) $len = 1;
		if(strlen($str)<$len)
		{
			global $CONSOLE;
			$CONSOLE->formerror(($msg) ? $msg : 'Field is too short. Please make this field atleast ' . $len . ' characters long');
		} else {
			return $str;
		}
		return false;
	}
	
	static public function email($str, $msg = false)
	{
		$str = filter_var($str, FILTER_SANITIZE_EMAIL);
		if(filter_var($str, FILTER_VALIDATE_EMAIL))
		{
			return $str;
		} else {
			global $CONSOLE;
			$CONSOLE->formerror(($msg) ? $msg : "'" . $str . "' is not a valid email address");
		}
		return false;
	}
	
	static public function url($str, $msg = false)
	{
		$str = filter_var($str, FILTER_SANITIZE_URL);
		if(filter_var($str, FILTER_VALIDATE_URL))
		{
			return $str;
		} else {
			global $CONSOLE;
			$CONSOLE->formerror(($msg) ? $msg : "'" . $str . "' is not a valid URL");
		}
		return false;
	}
	
	static public function luhn($number, $msg = false)
	{
		$sum = 0;
		$alt = false;
		for($i = strlen($number) - 1; $i >= 0; $i--)
		{
			$n = substr($number, $i, 1);
			if($alt)
			{
				$n *= 2;
				if($n > 9)
				{
					//calculate remainder
					$n = ($n % 10) +1;
				}
			}
			$sum += $n;
			$alt = !$alt;
		}
		$b = ($sum%10)==0;
		if(!$b)
		{
			global $CONSOLE;

			$CONSOLE->formerror(($msg) ? $msg : 'It appears that you have entered an invalid card number');
		}
		return $b;
	}
	
	static public function ip($str, $msg = false)
	{
		if(filter_var($str, FILTER_VALIDATE_IP))
		{
			return $str;
		} else {
			global $CONSOLE;
			$CONSOLE->formerror(($msg) ? $msg : "'" . $str . "' is not a valid IP");
		}
		return false;
	}
	
	static public function password($str, $len = 6, $msg = false)
	{
		if(!is_numeric($len)) $len = 6;
		if(strlen($str)<$len)
		{
			global $CONSOLE;
			$CONSOLE->formerror(($msg) ? $msg : "'" . $str . "' is not a valid password");
			return false;
		} else {
			return HTML::hashpwd($str);
		}
		return false;
	}
}