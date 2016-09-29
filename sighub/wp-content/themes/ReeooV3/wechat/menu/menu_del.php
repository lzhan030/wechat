<?php 
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
global  $current_user;
if( !isset($current_user->user_login)|| empty($current_user->user_login)){
		wp_redirect(wp_login_url());
}	

include '../common/wechat_dbaccessor.php';
include 'menu_permission_check.php';	
//加上这个代码，从js传参数过来
@extract($_REQUEST);

$menuid=$_GET["menuId"];
$menus=wechat_menu_get($menuid);
foreach($menus as $menu){
	$menuPid=$menu->parent_id;
}
if($menuPid==-1){
	$m_delete=wechat_menupar_del($menuid);
}
	$me_delete=wechat_menu_del($menuid);

if($me_delete===false){
	$hint = array("message"=>"删除失败,请重试");
}else{
	$hint = array("message"=>"删除成功");
}		
echo json_encode($hint);
exit;
?>