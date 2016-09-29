<?php defined('IN_IA') or exit('Access Denied');?><!DOCTYPE html>
<html>
<head>
    <title><?php bloginfo('name'); ?></title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="format-detection" content="telephone=no">
	<!--<title><?php  if($title) { ?><?php  echo $title;?><?php  } else { ?><?php  if(!empty($_W['account']['name'])) { ?><?php  echo $_W['account']['name'];?><?php  } ?><?php  } ?></title>-->
	<link rel="stylesheet"  href="<?php bloginfo('template_directory'); ?>/css/jquery.mobile-1.3.1.css">
	<link rel="stylesheet"  href="<?php bloginfo('template_directory'); ?>/css/common.mobile1.css?v=<?php echo TIMESTAMP;?>">
	<link type="text/css" rel="stylesheet" href="<?php echo home_url();?>/wp-content/themes/ReeooV3/wechat/egg/template/images/new.min.css" />
	<link type="text/css" rel="stylesheet" href="<?php echo home_url();?>/wp-content/themes/ReeooV3/wechat/egg/template/images/common.css" />
	<link type="text/css" rel="stylesheet" href="<?php echo home_url()?>/wp-content/themes/ReeooV3/wechat/redenvelope/template/css/bootstrap.min.css">
	<link href="<?php bloginfo('template_directory'); ?>/css/font-awesome-shopping.min.css" rel="stylesheet">
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery-1.10.2.min.js"></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.mobile-1.3.1.min.js"></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/common.mobile.js?v=<?php echo TIMESTAMP;?>"></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/bootstrap-modal.js"></script>
</head>
<body>