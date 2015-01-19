<?php
echo $breadcrumb;

if ($showName) {
    echo '<h1>' . $pageName . '</h1>';
}
echo $content;
?>
<ul class="news">
<?php
foreach ($articles as $article) {
    ?>
        <li>
            <div class="thumb">
                <?php if ($article['image'] != '') { ?>
                    <a href="<?php echo $article['url']; ?>">
                        <img src="<?php echo $article['image'] ?>" alt="<?php echo $article['name']; ?>" class="news-img" />
                    </a>
                <?php } ?>
            </div>
            <article>
                <header>
                    <h2><a href="<?php echo $article['url']; ?>"><?php echo $article['name']; ?></a></h2>
                    <time datetime="<?php echo $article['displaydate']; ?>T00:00:00">
                        <?php echo date("jS M Y", strtotime($article['displaydate'] . ' 00:00:00')); ?>
                    </time>
                </header>
                <p><?php echo $article['summary'] ?></p>
                <a class="read-more-btn" href="<?php echo $article['url']; ?>">Read More</a>
            </article>
            <div class="clear noheight">&nbsp;</div>
        </li>
    <?php }
?>
</ul>
<div class="clear">&nbsp;</div>
<?php echo $pagination; ?>
<div class="clear">&nbsp;</div>