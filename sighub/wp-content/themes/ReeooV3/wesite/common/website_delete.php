<?php 
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
get_header(); 
?>

<?php
include 'dbaccessor.php';
$siteid=$_GET["siteId"];
$po_delete=web_admin_delete_site($siteid);
if($po_delete===false){
	echo "error";
}else{
	echo "success";
}

?>