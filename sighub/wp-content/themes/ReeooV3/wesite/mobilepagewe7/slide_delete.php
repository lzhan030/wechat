<?php 

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
get_header(); 
?>

<?php
include '../common/dbaccessor.php';
$slideid=$_GET["slideid"];
$slide_delete=$wpdb -> delete($wpdb -> prefix.'site_nav', array(
	'id' => $slideid
));
if($slide_delete===false){
	echo "error";
}else{
	echo "success";
}
?>