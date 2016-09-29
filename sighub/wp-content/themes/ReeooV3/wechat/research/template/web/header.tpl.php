<?php defined('IN_IA') or exit('Access Denied');?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=8" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>微预约</title>
<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/we7/style/font-awesome.css" />
<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/we7/style/bootstrap.css" />

<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/we7/style/common.css?v=<?php echo TIMESTAMP;?>" />
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/we7/script/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/we7/script/jquery.form.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/we7/script/bootstrap.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/we7/script/common.js?v=<?php echo TIMESTAMP;?>"></script>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/we7/script/emotions.js"></script>
<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css" />
<script type="text/javascript">
cookie.prefix = '<?php echo $_W['config']['cookie']['pre'];?>';
</script>
<!--[if IE 7]>
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/we7/style/font-awesome-ie7.min.css">
<![endif]-->
<!--[if lte IE 6]>
<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory'); ?>/we7/style/bootstrap-ie6.min.css">
<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory'); ?>/we7/style/ie.css">
<![endif]-->
</head>
<body <?php if($action == 'frame') { ?>style="height:100%; overflow:hidden;" scroll="no"<?php } ?>>
