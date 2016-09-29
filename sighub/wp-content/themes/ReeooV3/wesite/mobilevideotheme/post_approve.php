<?php 

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');

include '../common/dbaccessor.php';
$postid=$_GET["postid"];
global $wpdb;
$pos_approve=$wpdb->update($wpdb->prefix.'posts',array('post_status'=>'publish'),array('ID' => $postid));
/* if($pos_delete==false){
	echo "删除失败!";
}else{
	echo "删除成功!";
} */

if($pos_approve===false)
	echo "操作失败，找不到视频!";
else
	echo "操作成功!";
?>