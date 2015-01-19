<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" /> 
	<meta http-equiv="content-language" content="en" />
	<meta name="copyright" content="Copyright <?php echo date('Y');?>" /> 
	<meta name="author" content="Peter McConnell" /> 
	<title>Forgot your password</title>
	<base href="<?php echo BASE_HREF;?>admin/" />
	<?php foreach($GBL_stylesheets as $stylesheet) echo '<link href="' . $stylesheet . '" rel="stylesheet" type="text/css" media="screen" />' . "\n"; ?>
</head>
<body>
<div id="wrapper">
    <div id="header">
        <a href="./">Home</a>
    </div>
    <ul id="body">
        <li id="content">
        	<?php
        	echo $GBL_formreport;
