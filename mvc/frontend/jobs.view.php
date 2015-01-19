<?php
echo $breadcrumb;
?>
<h1><?php echo $row['name']; ?></h1>
<?php echo $row['content']; ?>
<a href="./<?php echo $this->mvc['PRE-ROUTER']['CONTROLLER']; ?>">Back</a>