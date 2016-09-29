<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head profile="http://gmpg.org/xfn/11">

<title><?php bloginfo('name'); ?><?php wp_title(); ?></title>

<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />	
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=3.0, user-scalable=yes">  
<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" /> <!-- leave this for stats please -->

<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="alternate" type="text/xml" title="RSS .92" href="<?php bloginfo('rss_url'); ?>" />
<link rel="alternate" type="application/atom+xml" title="Atom 0.3" href="<?php bloginfo('atom_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

	<?php wp_get_archives('type=monthly&format=link'); ?>
	<?php //comments_popup_script(); // off by default ?>
	<?php wp_head(); ?>

</head>


<?php
global $wpdb, $table_prefix;
$tableName = $table_prefix.'orangesitemeta';
//$siteId = ($_GET['site'] == ''||$_GET['site']==null)? '1': $_GET['site'];
$siteId = $_SESSION['orangeSite'];//$siteId  = $_COOKIE['orangeSite'];
$keyName = 'mobilethemeTitle';
$sql = "SELECT `site_value` FROM `".$tableName."` WHERE `site_id`='".$siteId."' and `site_key`='".$keyName."'";
$sitemeta = $wpdb->get_row($sql);
$title = $sitemeta->site_value;
if(empty($title ))
{
    //$title  = '请创建标题';
    //$wpdb->query("INSERT INTO `".$tableName."`( `site_id`, `site_key`,`site_value`) VALUE('".$siteId."', '".$keyName."','".$title."')");
}
?>

<body>

<div id="wrapper">

<div id="logo">
<!--<a href="<?php bloginfo('rss2_url'); ?>" class="mobile_rss"><img src="<?php bloginfo('template_directory'); ?>/images/mobile_rss.gif" border="0" alt="Subscribe" /></a>-->
<a href="<?php bloginfo('rss2_url'); ?>" class="mobile_rss"></a>
<h1><a href="<?php ;//echo get_option('home'); ?>/"><?php echo $title;//bloginfo('name'); ?></a></h1>
</div>