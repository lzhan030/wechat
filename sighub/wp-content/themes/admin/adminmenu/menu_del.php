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
include './wp-content/themes/ReeooV3/wechat/common/wechat_dbaccessor.php';	
//加上这个代码，从js传参数过来
@extract($_REQUEST);

$menuid=$_GET["menuId"];

$menus=wechat_public_menu_gets($menuid);
foreach($menus as $menu){
	$menuPid=$menu->parent_id;
}
if($menuPid==-1){

//查询出该menuid对应的所有子id，然后user_content中的也全部删除
$pmenus=wechat_public_menu_gets_bypar($menuid);
	foreach($pmenus as $mid){
		$menid=$mid->menu_id;
		wechat_public_usermenu_del($menid);
	}
$m_delete=wechat_public_menupar_del($menuid);
}
$me_delete=wechat_public_menu_del($menuid);

if($me_delete===false){
	echo "删除失败";
}else{
		echo "删除成功";
}		
	
?>