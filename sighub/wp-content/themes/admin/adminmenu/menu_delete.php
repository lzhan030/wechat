<?php
$tmp_path = explode ( 'ReeooV3', __FILE__ );
$template_path=$tmp_path[0];
require_once $template_path.'ReeooV3/wechat/common/session.php';

@header("Content-type: text/html; charset=utf-8"); 
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

$M_id=$_GET["M_id"];
$w_id=wechat_select_public_wid($M_id);
foreach($w_id as $w){
	$wid=$w->wid;
	$weinfo=wechat_wechats_get($wid);
	foreach($weinfo as $win){
		$APPID=$win->menu_appId;
		$APPSECRET=$win->menu_appSc;
	}	
	$ACC_TOKEN=re_Token($APPID,$APPSECRET);
	//删除菜单事件触发
	$result=wechat_menu_delete($ACC_TOKEN);
}  

if($result == "0"){
	echo "menu delete success";
}else{
	echo "menu delete error";
}


?>