<?php

class RSS
{
    static private $sWebMaster = 'rss@domain.com';
	static public function phpheaders()
	{
		header("Content-Type:application/rss+xml; charset=ISO-8859-1");
	}
	
	static public function header($title, $link, $img)
	{
		$xml = '<?xml version="1.0"?>' . "\n" . '<rss version="2.0">' . "\n";
			$xml .= '<channel>' . "\n";
				$xml .= '<generator>PM CMS</generator>' . "\n";
				$xml .= '<title>' . $title . '</title>' . "\n";
				$xml .= '<link>' . $link . '</link>' . "\n";
				$xml .= '<language>en</language>' . "\n";
				$xml .= '<webMaster>' . self::$sWebMaster . '</webMaster>' . "\n";
				$xml .= '<copyright>&copy;' . date('Y') . '</copyright>' . "\n";
				$xml .= '<pubDate>Tue, 17 Apr 2012 17:32:17 GMT</pubDate>' . "\n";
				$xml .= '<lastBuildDate>Tue, 17 Apr 2012 17:32:17 GMT</lastBuildDate>' . "\n";
				$xml .= '<image><title>' . $title . '</title><url>' . $img . '</url><link>' . $link . '</link></image>' . "\n";
		return $xml;
	}
	
	static public function quickCreateFeed($aData)
	{
		$xml = '';
		foreach($aData as $row)
		{
			$xml .= '<item>' . "\n";
				$xml .= '<title>' . $row['title'] . '</title>' . "\n";
				$xml .= '<link>' . $row['link'] . '</link>' . "\n";
				$xml .= '<guid isPermaLink="false"></guid>' . "\n"; // CHECK!
				$xml .= '<category>' . $row['category'] . '</category>' . "\n";
				$xml .= '<pubDate>' . $row['pubDate'] . '</pubDate>' . "\n";
				$xml .= '<description>' . $row['content'] . '</description>' . "\n";
			$xml .= '</item>' . "\n";
		}
		return $xml;		
	}
	
	static public function footer($desc)
	{
		$xml = '<description>' . $desc . '</description>' . "\n";
		$xml = '</channel>' . "\n";
		$xml .= '</rss>' . "\n";
		return $xml;
	}
	
	static public function cleanValue($val)
	{
		$val = trim($val);
		$val = str_replace(array("\n", "\r", "\t"), ' ', $val);
		return $val;
	}
}