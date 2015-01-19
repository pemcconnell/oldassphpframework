<?php

echo $breadcrumb;

if($showName)
{
	echo '<h1>' . $pageName . '</h1>';
}
echo $content;

foreach($jobs as $row)
{
    echo '<div class="joblisting">';
	echo '<div class="h3">' . $row['name'] . '</div>';
	echo '<div class="summary">' . $row['summary'] . '</div>';
	echo '<a href="' . $row['url'] . '">Read More</a>';
    echo '</div>';
}