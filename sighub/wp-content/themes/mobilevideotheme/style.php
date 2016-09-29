<?php
require_once( dirname(__FILE__) . '../../../../wp-config.php');
require_once( dirname(__FILE__) . '/functions.php');
header("Content-type: text/css");
global $options;
foreach ($options as $value) {
if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); } }

global $wpdb, $table_prefix;
$tableName = $table_prefix.'orangesitemeta';
session_start();
$siteId = $_SESSION['orangeSite'];//$siteId = $_COOKIE['orangeSite'];
$keyName = 'mobilethemeColor';
$sql = "SELECT `site_value` FROM `".$tableName."` WHERE `site_id`='".$siteId."' and `site_key`='".$keyName."'";
$sitemeta = $wpdb->get_row($sql);
$color = $sitemeta->site_value;
if(empty($color))
{
   // $color = 'C00000';
   // $wpdb->query("INSERT INTO `".$tableName."`( `site_id`, `site_key`,`site_value`) VALUE('".$siteId."', '".$keyName."','".$color."')");
}
$keyName = 'mobilethemeWidth';
$sql = "SELECT `site_value` FROM `".$tableName."` WHERE `site_id`='".$siteId."' and `site_key`='".$keyName."'";
$sitemeta = $wpdb->get_row($sql);
$width = $sitemeta->site_value;
if(empty($width ))
{
   // $width  = '360px';
   // $wpdb->query("INSERT INTO `".$tableName."`( `site_id`, `site_key`,`site_value`) VALUE('".$siteId."', '".$keyName."','".$width ."')");
}
?>
a:link, a:visited {color:<?php echo $color; ?>;}
#logo {background-color:<?php echo $color; ?>;}
#wrapper {width:<?php echo $width; ?>;}