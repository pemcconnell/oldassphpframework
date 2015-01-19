<?php

class Twitterlogin
{
	public static 		$twitterObj,
						$authenticateUrl;
					
	protected static	$consumer_key,
						$consumer_secret;
	
	public static function init($consumer_key, $consumer_secret)
	{
		include(BASE_PATH . 'lib' . DS . 'twitterlogin' . DS . 'EpiCurl.php');
		include(BASE_PATH . 'lib' . DS . 'twitterlogin' . DS . 'EpiOAuth.php');
		include(BASE_PATH . 'lib' . DS . 'twitterlogin' . DS . 'EpiTwitter.php');
		
		self::$consumer_key = $consumer_key;
		self::$consumer_secret = $consumer_secret;
		
		self::$twitterObj = new EpiTwitter(self::$consumer_key, self::$consumer_secret);
	}
					
	public static function login()
	{
		// AUTHENTICATE
		self::$authenticateUrl = @self::$twitterObj->getAuthenticateUrl();

		header('Location:' . self::$authenticateUrl);
		exit;		
	}
	
	public static function check($token, $verifier)
	{
		self::$twitterObj->setToken($token);
		$token = self::$twitterObj->getAccessToken();
		self::$twitterObj->setToken($token->oauth_token, $token->oauth_token_secret);
		setcookie('oauth_token', $token->oauth_token); 
		setcookie('oauth_token_secret', $token->oauth_token_secret);
	}
}