<?php
class RssController extends FrontendBaseController
{
	private		$feedtitle,
				$feeddesc,
				$feedlink,
				$feedimg;

	public function __construct()
	{
		parent::__construct();
		
		$this->feedtitle = 'RSS Feed';
		$this->feeddesc = 'All the latest news from ' . BASE_HREF . '.';
		$this->feedlink = BASE_HREF . 'news/';
		$this->feedimg = BASE_HREF . 'imgs/rsslogo.png';
	}
	
	public function index()
	{
		// SET DEFAULT
		$this->news();
	}
	
	public function news()
	{
		$feed = false;
		$dbLayout = array(
			'tbl' => 'news',
			'id' => 'id',
			'online' => 'online'
		);
		$data = $this->MODEL->getTblData($dbLayout, 0, 0, 1);
		if($data)
		{
			// ARRANGE DATA TO SUIT RSS CLASS
			foreach($data as $k => $row)
			{
				$link = BASE_HREF . '/news/view/' . $row['id'] . '/' . HTML::createCleanURL($row['name']);
				$feedrow = array(
					'title' => RSS::cleanValue($row['name']),
					'link' => RSS::cleanValue($link),
					'permalink' => RSS::cleanValue($link),
					'category' => RSS::cleanValue('News'),
					'pubDate' => RSS::cleanValue($row['date']),
					'content' => RSS::cleanValue($row['content'])
				);
				$data[$k] = $feedrow;
			}
			$feed = RSS::header($this->feedtitle, $this->feedlink, $this->feedimg);
			$feed .= RSS::quickCreateFeed($data);
			$feed .= RSS::footer($this->feeddesc);
		}
		$this->display($feed);
		exit;
	}
	
	private function display($xml)
	{
		if($xml)
		{
			RSS::phpheaders();
			echo $xml;
		} else {
			$this->console->error('Attempted to access a non existant RSS feed');
			// 404?
		}
		exit;
	}
	
	public function __destruct()
	{
		parent::__destruct();
	}
}