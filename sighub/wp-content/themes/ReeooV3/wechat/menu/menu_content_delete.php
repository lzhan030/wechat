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
include 'menu_permission_check.php';
$menuId=$_GET["menuId"];
$menuType=$_GET["menuType"];
$menuKey=$_GET["menuKey"];
$menuName=$_GET["menuName"];
$menuPad=$_GET["menuPad"];
$content=$_GET["content"];
if($menuType=="view"){//删除链接
	$urldelete=wechat_menu_prisvc_urldel_group($menuId,$menuType,$menuKey,$_SESSION['GWEID']);
}else if($menuType=="weChat_news"){//删除图文
	$newsdelete=wechat_menu_prisvc_newsdel_group($menuId,$menuType,$menuKey,$_SESSION['GWEID']);
}else if($menuType=="weChat_text"){//删除文本
	$textdelete=wechat_menu_prisvc_textdel_group($menuId,$menuType,$menuKey,$_SESSION['GWEID']);
}
?>