<?php
if($pageName != '')
{
	echo '<h1>' . $pageName . '</h1><div class="hr_big"> --- </div>';
}
echo $content; ?>
<div class="clear">&nbsp;</div>
<?php foreach($aCols as $title => $rows) : ?>
<ul class="sitemapcol">
	<li class="title"><div class="h2"><?php echo $title; ?></div></li>
	<?php foreach($rows as $row) : ?>
		<li class="sitemaplink">
		<a href="<?php echo $row['url']; ?>"><?php echo $row['title']; ?></a>
		<?php echo $row['desc']; ?>
		</li>
	<?php endforeach; ?>
</ul>
<?php endforeach; ?>