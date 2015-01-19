<?php
/**
 * router.php
 * 
 * Use this file to re-route the MVC. For example, if you wish to make ./sitemap.xml target the SitemapController with the 'xml' method called then 'sitemap.xml' => 'sitemap/xml'
 */
 # STRAIGHT FORWARD ROUTING
$ROUTER = array(
	'robots.txt' => 'robots/txt',
	'sitemap.xml' => 'sitemap/xml'
);

/**
 * routerMethod
 * 
 * Advanced routing - allows for regex replacement etc.
 * 
 * @param string $url Expected input URL
 * 
 * @return string Url
 */
function routerMethod($url)
{
	$uri = false;
	return $uri;
}