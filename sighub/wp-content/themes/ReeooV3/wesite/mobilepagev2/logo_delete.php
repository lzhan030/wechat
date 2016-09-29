<?php 

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
get_header(); 
?>

<?php
include '../common/dbaccessor.php';
$siteid=$_GET["siteid"];
$logo_delete=web_admin_update_site_logo($siteid,"");
if($logo_delete===false){
	echo "error";
}else{
	echo "success";
}
?>