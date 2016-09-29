<?php

@header("Content-type: text/html; charset=utf-8"); 
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
global  $current_user;
if( !isset($current_user->user_login)|| empty($current_user->user_login)){
		wp_redirect(wp_login_url());
}

include './wp-content/themes/ReeooV3/wechat/common/wechat_dbaccessor.php';
include './wp-content/themes/ReeooV3/wechat/common/jostudio.wechatmenu.php';

$M_id=$_GET["M_id"];
$wechats_info=wechat_select_public_wid($M_id);
$allresult='';
$wechaterror=null;
foreach($wechats_info as $winfo){
	$wid=$winfo->wid;
	$weinfo=wechat_wechats_get(intval($wid));
	foreach($weinfo as $win){
		$APPID=$win->menu_appId;
		$APPSECRET=$win->menu_appSc;
		$WECHAT_NIKENAME=$win->wechat_nikename;
	}	
	$ACC_TOKEN=re_Token($APPID,$APPSECRET);
	$result=wechat_menu_delete($ACC_TOKEN);
	if($result!='0'){
		if($result=='41001'||$result=='40001'){
			$allresult=$allresult."'".$WECHAT_NIKENAME."'公众号的AppId或AppSecret有错误，菜单删除失败  ";
			
		}else{
			if(empty($wechaterror)){
				if(in_array($WECHAT_RESPONSE[$result],$WECHAT_RESPONSE)){
					$wechaterror=$WECHAT_RESPONSE[$result];
				}else{
					$wechaterror="微信菜单删除出现错误,请重试";
				}
			}
		}
	}
}
$menus=wechat_public_menu_countsm_del($M_id);//wechat_content_menu+user_menu
if(($allresult=='')&&(empty($wechaterror))){//没有错误
	$hint = array("status"=>"success","message"=>"菜单删除成功");
	echo json_encode($hint);
	exit;	
}else if(!empty($wechaterror)){//appid错误
	$hint = array("status"=>"success","message"=>$wechaterror);
	echo json_encode($hint);
	exit;
}else{//appid之外的错误
	$hint = array("status"=>"error","message"=>$allresult);
	echo json_encode($hint);
	exit;
}
?>
