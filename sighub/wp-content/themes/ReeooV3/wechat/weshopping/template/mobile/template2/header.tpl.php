<?php defined('IN_IA') or exit('Access Denied');?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html <?php language_attributes(); ?>>
<head>
	<meta charset="utf-8">
	<title><?php bloginfo('name'); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=3.0, user-scalable=no">
	<meta name="format-detection" content="telephone=no, address=no">
	<meta name="apple-mobile-web-app-capable" content="yes" /> <!-- apple devices fullscreen -->
	<meta name="apple-touch-fullscreen" content="yes"/>
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
	<meta name="keywords" content="<?php  if(empty($_W['page']['keywords'])) { ?><?php  if(IMS_FAMILY != 'x') { ?>微擎,微信,微信公众平台,we7.cc<?php  } ?><?php  } else { ?><?php  echo $_W['page']['keywords'];?><?php  } ?>" />
	<meta name="description" content="<?php  if(empty($_W['page']['description'])) { ?><?php  if(IMS_FAMILY != 'x') { ?>公众平台自助引擎（www.we7.cc），简称微擎，微擎是一款免费开源的微信公众平台管理系统，是国内最完善移动网站及移动互联网技术解决方案。<?php  } ?><?php  } else { ?><?php  echo $_W['page']['description'];?><?php  } ?>" />
	<link href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css" rel="stylesheet">
	<link href="<?php bloginfo('template_directory'); ?>/css/font-awesome-shopping.min.css" rel="stylesheet">
	<link href="<?php bloginfo('template_directory'); ?>/css/animate.css" rel="stylesheet">
	<link href="<?php bloginfo('template_directory'); ?>/css/common.css" rel="stylesheet">
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/lib/jquery-1.11.1.min.js"></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.form.js" type="text/javascript" ></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/bootstrap-modal.js"></script>
	<script type="text/javascript">
	if(navigator.appName == 'Microsoft Internet Explorer'){
		if(navigator.userAgent.indexOf("MSIE 5.0")>0 || navigator.userAgent.indexOf("MSIE 6.0")>0 || navigator.userAgent.indexOf("MSIE 7.0")>0) {
			alert('您使用的 IE 浏览器版本过低, 推荐使用 Chrome 浏览器或 IE8 及以上版本浏览器.');
		}
	}
	
	window.sysinfo = {
	<?php  if(!empty($_W['uniacid'])) { ?>
			'uniacid': '<?php  echo $_W['uniacid'];?>',
	<?php  } ?>
	<?php  if(!empty($_W['acid'])) { ?>
			'acid': '<?php  echo $_W['acid'];?>',
	<?php  } ?>
	<?php  if(!empty($_W['openid'])) { ?>
			'openid': '<?php  echo $_W['openid'];?>',
	<?php  } ?>
	<?php  if(!empty($_W['uid'])) { ?>
			'uid': '<?php  echo $_W['uid'];?>',
	<?php  } ?>
			'siteroot': '<?php  echo $_W['siteroot'];?>',
			//'siteurl': '<?php  echo $_W['siteurl'];?>',
			'attachurl': '<?php  echo $_W['attachurl'];?>',
	<?php  if(defined('MODULE_URL')) { ?>
			'MODULE_URL': '<?php echo MODULE_URL;?>',
	<?php  } ?>
		'cookie' : {'pre': '<?php  echo $_W['config']['cookie']['pre'];?>'}
	};
	</script>
</head>
<body>
<div class="container container-fill">