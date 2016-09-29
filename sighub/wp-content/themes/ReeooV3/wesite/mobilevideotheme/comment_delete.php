<?php 

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
get_header(); 
?>

<?php
	include '../common/dbaccessor.php';
	$commentid=$_GET["commentid"];
	$pos_delete=web_admin_delete_coment($commentid);
	if($pos_delete==false){
		echo "删除失败!";
	}else{
		echo "删除成功!";
	}
?>