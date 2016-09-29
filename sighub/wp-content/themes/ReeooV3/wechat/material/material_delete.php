<?php 

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
global  $current_user;
if( !isset($current_user->user_login)|| empty($current_user->user_login)){
		wp_redirect(wp_login_url());
}	

get_header(); 


?>


<?php
include '../common/wechat_dbaccessor.php';
include '../../wesite/common/dbaccessor.php';
//判断是否是分组管理员的公众号,分组管理员不需要进行此功能的check
$groupadmincount = is_superadmin($_SESSION['GWEID']);
if($groupadmincount == 0) 
	include 'material_permission_check.php';	
//加上这个代码，从js传参数过来
@extract($_REQUEST);

echo $newsId;
$newsItemId=$_GET["netId"];

echo $newsItemId;
//用于删除storage中的图片
$delete_storage=material_news_url_delete($newsItemId,$newsId);
//如果newsItemId为空，删除的是某个多图文的某条图文
//如果不为空，删除整条多图文
$matr_delete=material_news_delete($newsItemId,$newsId);
if($matr_delete===false){
	echo "删除失败";
}else{
		echo "删除成功";
}		
	
?>