<?php defined('IN_IA') or exit('Access Denied');?><!DOCTYPE html>
<html>
<head>
	<title><?php bloginfo('name'); ?></title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=3.0, user-scalable=yes"> 
	<!-- Mobile Devices Support @begin -->
	<meta content="application/xhtml+xml;charset=UTF-8" http-equiv="Content-Type">
	<meta content="no-cache,must-revalidate" http-equiv="Cache-Control">
	<meta content="no-cache" http-equiv="pragma">
	<meta content="0" http-equiv="expires">
	<meta content="telephone=no, address=no" name="format-detection">
	<meta content="width=device-width, initial-scale=1.0" name="viewport">
	<meta name="apple-mobile-web-app-capable" content="yes" /> <!-- apple devices fullscreen -->
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
	<!-- Mobile Devices Support @end -->
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	<!--<link type="text/css" rel="stylesheet" href="<?php echo home_url()?>/wp-content/themes/ReeooV3/css/bootstrap.min.css">-->
	<link type="text/css" rel="stylesheet" href="<?php echo home_url()?>/wp-content/themes/ReeooV3/wechat/scratchcard/template/css/bootstrap.css">
	<link type="text/css" rel="stylesheet" href="<?php echo home_url()?>/wp-content/themes/ReeooV3/we7/style/font-awesome.min.css" />
	<link type="text/css" rel="stylesheet" href="<?php echo home_url()?>/wp-content/themes/ReeooV3/we7/style/common.mobile.css">
	<link type="text/css" rel="stylesheet" href="<?php echo home_url()?>/wp-content/themes/ReeooV3/we7/style/messenger.css">
	<link type="text/css" rel="stylesheet" href="<?php echo home_url()?>/wp-content/themes/ReeooV3/we7/style/messenger-theme-future.css">
	<script type="text/javascript" src="<?php echo home_url()?>/wp-content/themes/ReeooV3/js/jquery-1.10.2.min.js"></script>
	<!-- <script type="text/javascript" src="<?php echo home_url()?>/wp-content/themes/ReeooV3/js/bootstrap.min.js"></script>-->
	<script type="text/javascript" src="<?php echo home_url()?>/wp-content/themes/ReeooV3/wechat/scratchcard/template/js/bootstrap.js"></script>
	<script type="text/javascript" src="<?php echo home_url()?>/wp-content/themes/ReeooV3/we7/script/cascade.js"></script>
	<script type="text/javascript" src="<?php echo home_url()?>/wp-content/themes/ReeooV3/we7/script/jquery.touchwipe.js"></script>
	<script type="text/javascript" src="<?php echo home_url()?>/wp-content/themes/ReeooV3/we7/script/swipe.js"></script>
	<script type="text/javascript" src="<?php echo home_url()?>/wp-content/themes/ReeooV3/js/jquery.form.js"></script>
	<script type="text/javascript" src="<?php echo home_url()?>/wp-content/themes/ReeooV3/we7/script/messenger.min.js"></script>
	<script charset="utf-8" src="<?php bloginfo('template_directory'); ?>/wechat/scratchcard/template/js/wScratchPad.js"></script>
	<link type="text/css" rel="stylesheet" href="<?php echo home_url()?>/wp-content/themes/ReeooV3/css/wsite.css" />
	<link type="text/css" rel="stylesheet" href="<?php echo home_url()?>/wp-content/themes/ReeooV3/wechat/scratchcard/template/css/scratchcard.css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/wechat/scratchcard/template/css/lottery.css">
	<style>
	<?php if($name == 'home') { ?>
	body {background-image:url(<?php echo $_W['styles']['homebgimg'];?>); background-color:<?php echo $_W['styles']['homebgcolor'];?>; <?php echo $_W['styles']['homebgextra'];?>; }
	<?php } ?>
	</style>
</head>
<body>
