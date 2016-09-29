<?php 

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
get_header(); 
?>

<?php

include '../common/dbaccessor.php';
$galleryid=$_GET["galleryid"];
$gal_delete=web_admin_delete_gallery($galleryid);
if($gal_delete===false){
	echo "error";
}else{
	echo "success";
}

?>