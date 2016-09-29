<?php 

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
//get_header(); 
?>

<?php
	include '../common/dbaccessor.php';
	$postid=$_GET["postid"];
	$pos_delete=web_admin_delete_post($postid);
	/* if($pos_delete==false){
		echo "删除失败!";
	}else{
		echo "删除成功!";
	} */

	if($pos_delete==false){
		echo "删除失败!";
	}elseif(strpos($pos_delete,"同步状态失败")>0){  //如果同步状态在删除该文章的时候是失败，则不用提示同步删除的状态
		echo "删除成功!";
	}elseif(strpos($pos_delete,"同步删除成功")>0){
		echo "删除成功,同步删除成功!";
	}elseif(strpos($pos_delete,"同步删除失败")>0){
		echo "删除成功,同步删除失败!";
	}else{
		echo "删除成功!";
	}
	 
?>