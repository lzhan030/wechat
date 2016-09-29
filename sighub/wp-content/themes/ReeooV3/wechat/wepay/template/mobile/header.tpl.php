<?php defined('IN_IA') or exit('Access Denied');?>

<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
	<meta charset="UTF-8" />
	<!--<title><?php //if (is_home () ) { bloginfo('name'); } elseif ( is_category() ) { single_cat_title();
	//echo " - "; bloginfo('name'); } elseif (is_single() || is_page() ) { single_post_title(); echo " - "; bloginfo('name'); }
	//elseif (is_search() ) { bloginfo('name'); echo "search results:"; echo
	//wp_specialchars($s); } else { wp_title('',true); } ?></title>-->
	<title><?php bloginfo('name'); ?></title>
	<!--<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes" />
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">-->
	<!--新添加为适应手机的 -->
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
	
	<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" />
	<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="http://feeds.feedburner.com/reeoo" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite_mobile.css">
	<!--<link rel="stylesheet" href="<?php //bloginfo('template_directory'); ?>/style.css">-->

	<!--[if IE]>
    	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]--> 	
	<?php 
		if ( is_singular() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );
		wp_head();
	?>
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js"></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.form.js" type="text/javascript" ></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/bootstrap-modal.js"></script>
	

		
</head>