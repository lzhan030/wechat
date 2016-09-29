<?php defined('IN_IA') or exit('Access Denied');?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
	<meta charset="UTF-8" />
	<title><?php bloginfo('name'); ?><?php wp_title(); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" />
	<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="http://feeds.feedburner.com/reeoo" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/wechat/weshopping/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/wechat/weshopping/css/font-awesome.min.css">
	<link href="<?php bloginfo('template_directory'); ?>/css/animate.css" rel="stylesheet">
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/wechat/weshopping/css/common.css">
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/wechat/weshopping/css/bootstrap-theme.min">
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css">
	<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/we7/style/font-awesome.css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/we7/style/font-awesome-ie7.min.css">
	<!--[if IE]>
    	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]--> 	
	<?php 
		if ( is_singular() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );
		wp_head();
	?>
	<!--<script src="<?php bloginfo('template_directory'); ?>/js/jquery-1.10.2.min.js"></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.form.js" type="text/javascript" ></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/bootstrap.min.js"></script>-->
	
	<script src="<?php bloginfo('template_directory'); ?>/wechat/weshopping/js/require.js"></script>
	<script src="<?php bloginfo('template_directory'); ?>/wechat/weshopping/js/app/config.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/wechat/weshopping/js/lib/jquery-1.11.1.min.js"></script>
	<script src="<?php bloginfo('template_directory'); ?>/wechat/weshopping/js/jquery.gcjs.js"></script>
	<script src="<?php bloginfo('template_directory'); ?>/wechat/weshopping/js/jquery.form.js"></script>
	<script src="<?php bloginfo('template_directory'); ?>/wechat/weshopping/js/lib/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/wechat/weshopping/js/tooltipbox.js"></script>
	<!--<script src="<?php bloginfo('template_directory'); ?>/wechat/weshopping/js/lib/jquery-ui-1.10.3.min.js"></script>-->
	
</head>