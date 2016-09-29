<?php 

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
get_header(); 
?>

<?php
	include '../common/dbaccessor.php';
	include '../common/upload.php';
	global $wpdb;
//$picUrl=$_GET["picUrl"];
$Id=$_GET["Id"];//slider的ID
	$bac_insert=web_admin_delete_site_bacimg3($Id);
if($bac_insert===false){
	echo "error";
}else{
	echo "success";
}
?>