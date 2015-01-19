<?php

echo $breadcrumb;

if($showName)
{
	echo '<h1>' . $pageName . '</h1>';
}
echo $content;

if($article['image'] != '') {
	#echo '<img src="'.$article['image'].'" alt="'.$pageName.'" class="img-news-story" />';
}

echo $article['content']; ?>
<div class="clear">&nbsp;</div>
<a href="./news" class="back-btn">Back</a>