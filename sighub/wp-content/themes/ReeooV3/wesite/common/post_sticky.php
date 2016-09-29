<?php 

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
get_header(); 
?>

<?php
	include '../common/dbaccessor.php';
	$postid=$_GET["postid"];
	if (!is_sticky($postid))
		stick_post($postid);
	else
		unstick_post($postid);
	
?>