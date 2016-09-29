<?php 

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
get_header(); 
?>

<?php
$menid=$_GET["menid"];
$menu_delete=$wpdb -> delete($wpdb -> prefix.'site_nav', array(
	'id' => $menid
));
if($menu_delete===false){
	echo "error";
}else{
	echo "success";
}
?>