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
include 'menu_permission_check.php';
$backValue=$_POST['trans_data'];
$menuid=$_POST['menuid'];
if($backValue!=null){
	$text=wechat_text_get($backValue);
	foreach($text as $t){
		$content=$t->text_content;
	}
	echo $content;
}
if($menuid!=null){
	$menus=wechat_menu_get($menuid);
	foreach($menus as $menu){
		$menuurl=$menu->menu_key;
		$menutype=$menu->menu_type;
		if($menutype=="view"){
			/*如果没有http，证明为内链，加上home_url显示，插入时，判断有再截取入库*/
			$tmp = stristr($menuurl,"http");
			if(($tmp===false)&&(!empty($menuurl))){
				$menuurllink=home_url().$menuurl;
			}else{				
				$menuurllink=$menuurl;
			}		
		}else{
			$menuurllink=$menu->menu_key;
		}
	}
	echo $menuurllink;
}
?>