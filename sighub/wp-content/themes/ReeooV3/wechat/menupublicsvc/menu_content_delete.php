<?php
$tmp_path = explode ( 'ReeooV3', __FILE__ );
$template_path=$tmp_path[0];
require_once $template_path.'ReeooV3/wechat/common/session.php';

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
global  $current_user;
if( !isset($current_user->user_login)|| empty($current_user->user_login)){
		wp_redirect(wp_login_url());
}	
get_header(); 


include '../common/wechat_dbaccessor.php';
$menuId=$_GET["menuId"];
$menuType=$_GET["menuType"];
$menuKey=$_GET["menuKey"];
$menuName=$_GET["menuName"];
$menuPad=$_GET["menuPad"];
$content=$_GET["content"];
if($menuType=="view"){	
	$urldelete=wechat_menu_publicsvc_urldel($menuId,$menuType,$menuKey,$_SESSION['WEID']);
}else if($menuType=="weChat_news"){
	$newsdelete=wechat_menu_publicsvc_newsdel($menuId,$menuType,$menuKey,$_SESSION['WEID']);
}else if($menuType=="weChat_text"){
	$textdelete=wechat_menu_publicsvc_textdel_all_group($menuId,$menuType,$menuKey,$_SESSION['WEID'],$_SESSION['GWEID']);
}
?>