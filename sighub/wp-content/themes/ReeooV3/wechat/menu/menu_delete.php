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

include '../common/wechat_dbaccessor.php';
include 'menu_permission_check.php';

//拿到私有的服务号或已认证的订阅号的信息，公有的不行
$wids=wechat_info_get_group_pri($_SESSION['GWEID']);
$allresult='';
$wechaterror=null;
foreach($wids as $wc){
    $wid=$wc->wid;
	$weinfo=wechat_wechats_get(intval($wid));
	foreach($weinfo as $win){
		$APPID=$win->menu_appId;
		$APPSECRET=$win->menu_appSc;
	}	
	$ACC_TOKEN=re_Token($APPID,$APPSECRET);
	$result=wechat_menu_delete($ACC_TOKEN);
	if($result!='0'){
		if($result=='41001'||$result=='40001'){
			$allresult=$allresult."AppId或AppSecret有错误，菜单删除失败 ";
			
		}else{
			if(empty($wechaterror)){
				if(in_array($WECHAT_RESPONSE[$result],$WECHAT_RESPONSE)){
					$wechaterror=$WECHAT_RESPONSE[$result];
				}else{
					$wechaterror="菜单删除出现错误,请重试";
				}
			}
		}
	}
}

if(($allresult=='')&&(empty($wechaterror))){//没有错误
	$menu_del_all=wechat_menu_del_all($_SESSION['GWEID']);//delete wechat_menu by the gweid
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