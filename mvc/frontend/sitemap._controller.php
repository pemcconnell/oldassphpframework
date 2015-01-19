<?php
class SitemapController extends FrontendBaseController
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		$aData = $this->getSitemapData();
		
		$aCols = array();
		
		foreach($aData as $urlset)
		{
			$aCols[$urlset['title']] = array();
			foreach($urlset['data'] as $row)
			{
				$aCols[$urlset['title']][] = $row;
			}
		}
		$this->templatevars['aCols'] = $aCols;
	}
	
	public function xml()
	{
		$aData = $this->getSitemapData();
		$this->displayxml($aData);
		exit;
	}
	
	private function getSitemapData()
	{
		// PAGES
		$dbLayout = array(
			'tbl' => 'pages',
			'id' => 'id',
			'sortOrder' => 'sortOrder',
			'online' => 'online',
			'parent' => 'parent'
		);
		$pages = $this->MODEL->getTblData($dbLayout, false, 0, 1);
		$aPageData = array();
		foreach($pages as $page)
		{                        
                            $url = $this->createPageLink($page);
                            if(strpos($url, HREF)!==false)
                            {
			$aPageData[] = array(
				'url' => $url,
				'title' => $page['menuName'],
				'desc' => HTML::createSummary($page['content']),
				'lastmod' => $page['lastUpdated']
			);
                            }
		}
		$aPageData = array('title' => 'Pages', 'data' => $aPageData);
		
		// NEWS
		$dbLayout = array(
			'tbl' => 'news',
			'id' => 'id',
			'sortOrder' => 'displaydate DESC',
			'online' => 'online',
			'parent' => 'parent'
		);
		$news = $this->MODEL->getTblData($dbLayout, false, 0, 1);
		$aNewsData = array();
		foreach($news as $row)
		{
			$aNewsData[] = array(
				'url' => BASE_HREF . 'news/view/' . $row['id'] . '/' . HTML::createCleanURL($row['name']),
				'title' => $row['name'],
				'desc' => HTML::createSummary($row['content']),
				'lastmod' => $row['lastUpdated']
			);
		}
		$aNewsData = array('title' => 'News', 'data' => $aNewsData);
		
		$aData = array(
			$aPageData,
			$aNewsData
		);
		
		return $aData;
	}
	
	private function displayxml(array $aData)
	{
		header("Content-type:text/xml");
		echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd\">\n";
		// LOOP URLS
		foreach($aData as $urlset)
		{
			foreach($urlset['data'] as $row)
			{
				echo "<url>\n";
					echo "<loc>" . $row['url'] . "</loc>\n";
					echo "<lastmod>" . $row['lastmod'] . "</lastmod>\n";
				echo "</url>\n";
			}
		}
		echo "</urlset>\n";
		exit;
	}
	
	public function __destruct()
	{
		parent::__destruct();
	}
}