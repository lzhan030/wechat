<?php
/*	Author: Janeen
/*	Function:某个私有服务号或已认证订阅号的微信自定义菜单微信上传生成
/**/
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
include '../../wesite/common/dbaccessor.php';
include '../common/jostudio.wechatmenu.php';

$MENUGWEID=$_SESSION['GWEID'];
include '../common/menu_manage.php';

//拿到私有的服务号或已认证的订阅号的信息，公有的不行
$wids=wechat_info_get_group_pri($_SESSION['GWEID']);
if(!empty($wids)){
	$allresult='';
	$wechaterror=null;
	foreach($wids as $wc){
		$wid=$wc->wid;
		$weinfo=wechat_wechats_get(intval($wid));//get wids info
		foreach($weinfo as $win){
			$APPID=$win->menu_appId;
			$APPSECRET=$win->menu_appSc;
		}	
		$ACC_TOKEN=re_Token($APPID,$APPSECRET);
		if($menu->str!= '{ "button": [  ] }'){
			$result=wechat_menu_create($ACC_TOKEN,$menu->str);	
		}else{
			$result=wechat_menu_delete($ACC_TOKEN);
		}
		//微信菜单返回值判断
		if($result!='0'){
			if($result=='41001'||$result=='40001'){
				$allresult=$allresult."AppId或AppSecret有错误，菜单同步失败 ";
			}else{
				if(empty($wechaterror)){
					if(in_array($WECHAT_RESPONSE[$result],$WECHAT_RESPONSE)){
						$wechaterror=$WECHAT_RESPONSE[$result];
					}else{
						$wechaterror="菜单上传出现错误,请重试";
					}
				}
			}
		}
	}
	if(($allresult=='')&&(empty($wechaterror))){//没有错误
		$hint = array("status"=>"success","message"=>"菜单上传成功");
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
}else{
	$hint = array("status"=>"error","message"=>"目前没有公众号使用该菜单模板");
	echo json_encode($hint);
	exit;
}
?>