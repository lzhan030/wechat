<?php 

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
global  $current_user;
if( !isset($current_user->user_login)|| empty($current_user->user_login)){
		wp_redirect(wp_login_url());
}	

?>


<?php
include '../common/wechat_dbaccessor.php';	
//加上这个代码，从js传参数过来
@extract($_REQUEST);
$vid=$_GET["vmId"];
$vm_delete=web_admin_delete_vmember($vid);
$vm_delete_group=web_admin_delete_vmember_group($vid);
if(($vm_delete===false)&&($vm_delete_group===false)){
	echo "删除失败";
}else{
	echo "删除成功";
}

?>