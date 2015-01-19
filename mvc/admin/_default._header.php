<!DOCTYPE html>
<!--[if IEMobile 7 ]> <html dir="ltr" lang="en-US"class="no-js iem7"> <![endif]-->
<!--[if lt IE 7 ]> <html dir="ltr" lang="en-US" class="no-js ie6 oldie"> <![endif]-->
<!--[if IE 7 ]>    <html dir="ltr" lang="en-US" class="no-js ie7 oldie"> <![endif]-->
<!--[if IE 8 ]>    <html dir="ltr" lang="en-US" class="no-js ie8 oldie"> <![endif]-->
<!--[if (gte IE 9)|(gt IEMobile 7)|!(IEMobile)|!(IE)]><!--><html dir="ltr" lang="en-US" class="no-js"><!--<![endif]-->
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta http-equiv="content-language" content="en" />
	<meta name="copyright" content="Copyright <?php echo date('Y');?>" />
	<meta name="author" content="Peter McConnell" /> 
	<title>Content Management System</title>
	<base href="<?php echo BASE_HREF;?>admin/" />
	<?php foreach($GBL_stylesheets as $stylesheet) echo '<link href="' . $stylesheet . '" rel="stylesheet" type="text/css" media="screen" />' . "\n"; ?>
</head>
<body>
<div id="wrapper">
	<div id="jsStatusMessage"><?php echo $GBL_formreport; ?></div>
    <div id="header">
    	<div class="left">
        	<a href="<?php echo BASE_HREF; ?>" class="home">View My Website</a>
        </div>
        <div class="right">
        	<span>Signed in as <strong><?php echo $session['admin']['name'];?></strong> |&nbsp;</span><a href="./logout" class="logout">Log Out</a>
        </div>
        <div class="clear"></div>
    </div>
    <ul id="body">
        <li id="nav">
            <ul>
                <li><a href="./cmsusers">CMS Users</a></li>
                <?php /*<li><a href="./forms">Forms</a></li>*/ ?>
                <?php /*<li><a href="./galleries">Galleries</a></li>*/ ?>
                <li><a href="./jobs">Jobs</a></li>
                <li><a href="./news">News</a></li>
                <li><a href="./pages">Pages</a></li>
                <li><a href="./staff">Staff</a></li>
            </ul>
        </li>
        <li id="content">
			<div id="content_wrapper">
