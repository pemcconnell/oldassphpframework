<?php

/**
 * Should contain all search engine related snippets.
 */
class SEO
{
	static public $ping_google_url = 'http://www.google.com/webmasters/tools/ping?sitemap=';
	static public $ping_yahoo_url = 'http://search.yahooapis.com/SiteExplorerService/V1/ping?sitemap=';
	static public $ping_ask_url = 'http://submissions.ask.com/ping?sitemap=';
	static public $ping_live_url = 'http://webmaster.live.com/ping.aspx?siteMap=';
	
	static public function tellAllSearchEnginesThereIsANewPage($sitemapurl = '')
	{
		self::tellSearchEngineThereIsANewPage('google', $sitemapurl);
		self::tellSearchEngineThereIsANewPage('yahoo', $sitemapurl);
		self::tellSearchEngineThereIsANewPage('ask', $sitemapurl);
		self::tellSearchEngineThereIsANewPage('live', $sitemapurl);
	}
	
	static public function tellSearchEngineThereIsANewPage($engine, $sitemapurl = '')
	{
		if($sitemapurl == '') $sitemapurl = BASE_HREF . 'sitemap.xml';
		$urlvarname = 'ping_' . $engine . '_url';
		if(isset(self::$$urlvarname))
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, self::$$urlvarname . urlencode($sitemapurl));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$output = curl_exec($ch);
			curl_close($ch);
		} else {
			global $CONSOLE;
			$CONSOLE->error('Attempted to ping unknown search engine "' . $engine . '"');
		}
	}
}