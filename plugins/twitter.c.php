<?php

class Twitter
{	
	static public function getFeedByUsername($sUserName, $limit = 10)
	{
		$aRet = array();
		$url = 'http://twitter.com/statuses/user_timeline.rss?screen_name='.$sUserName.'&count='.$limit;
		$xml = simplexml_load_file($url);
		if(is_object($xml))
		{
			foreach($xml->channel->item as $twit)
			{
				$description = stripslashes(htmlentities($twit->description,ENT_QUOTES,'UTF-8'));
				if(strtolower(substr($description,0,strlen($sUserName))) == strtolower($sUserName))
				{
			        $description = substr($description,strlen($sUserName)+1);
			    }
			    // ADD HYPERLINKS
			    $description = preg_replace('@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@i', '<a href="\1">\1</a>', $description);
			    // ADD TWITTER LINKS
			    $description = preg_replace('/@([a-z0-9]+)/i', '<a href="http://www.twitter.com/#!/\1">@\1</a>', $description);
			    $aRet[] = array(
			    	'title' => stripslashes(htmlentities($twit->title,ENT_QUOTES,'UTF-8')),
			    	'description' => $description,
				    'pubDate' => strtotime($twit->pubDate),
				    'guid' => stripslashes(htmlentities($twit->guid,ENT_QUOTES,'UTF-8')),
			    	'link' => stripslashes(htmlentities($twit->link,ENT_QUOTES,'UTF-8')),
			    );
			}
		}
		return $aRet;
	}
}