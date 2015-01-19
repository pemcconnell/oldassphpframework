<!DOCTYPE html>
<!--[if IEMobile 7 ]> <html dir="ltr" lang="en-US"class="no-js iem7"> <![endif]-->
<!--[if lt IE 7 ]> <html dir="ltr" lang="en-US" class="no-js ie6 oldie"> <![endif]-->
<!--[if IE 7 ]>    <html dir="ltr" lang="en-US" class="no-js ie7 oldie"> <![endif]-->
<!--[if IE 8 ]>    <html dir="ltr" lang="en-US" class="no-js ie8 oldie"> <![endif]-->
<!--[if (gte IE 9)|(gt IEMobile 7)|!(IEMobile)|!(IE)]><!--><html dir="ltr" lang="en-US" class="no-js"><!--<![endif]-->
<head>
    <title><?php echo $metaTitle; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="description" content="<?php echo $metaDescription; ?>" />
    <base href="<?php echo BASE_HREF; ?>" />
    <?php foreach ($GBL_stylesheets as $stylesheet)
	echo '<link href="' . $stylesheet . '" rel="stylesheet" type="text/css" />' . "\n"; ?>
    <link rel="stylesheet" href="<?php echo BASE_HREF; ?>css/print.css" type="text/css" media="print" />
    <?php /*
    <link rel="apple-touch-icon" href="<?php echo BASE_HREF; ?>imgs/iphone-icon.png"/>
    <link rel="shortcut icon" href="<?php echo BASE_HREF; ?>imgs/favicon.ico" />*/ ?>
    <!--[if IE]>
    <meta http-equiv="Page-Enter" content="blendTrans(duration=0)" />
    <meta http-equiv="Page-Exit" content="blendTrans(duration=0)" />
    <![endif]-->
</head>
<body id="template" class="pageview_<?php echo $GBL_controllerName; ?>">
    <div id="fb-root">&nbsp;</div>
    <div id="outterwrapper">
	<div id="wrapper">
	    <div id="bodyheaderwrapper">
		<div id="body">
		    <div id="fullcontent">
			<?php
			$class = 'fullcol';
			if ($related || (is_array($additionalRelated)))
			    $class = 'maincol';
			?>
			<div class="<?php echo $class; ?>">
			<?php echo $GBL_formreport;