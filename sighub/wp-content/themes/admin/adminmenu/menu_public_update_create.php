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


$w_id=wechat_select_public_wid($M_id);
if(!empty($w_id)){
	$allresult='';
	$wechaterror=null;
	foreach($w_id as $w){
		$wid=$w->wid;
		
		$weinfo=wechat_wechats_get(intval($wid));
		foreach($weinfo as $win){
			$APPID=$win->menu_appId;
			$APPSECRET=$win->menu_appSc;
			$WECHAT_NIKENAME=$win->wechat_nikename;
		}

		//获取token
		$ACC_TOKEN=re_Token($APPID,$APPSECRET);

		$demomenu=$_POST["demomenu"];  //获取菜单编号M_id
		if($demomenu==null){
			$demomenu=$M_id;
		}
		$MENUGWEID=$demomenu;
		include './wp-content/themes/ReeooV3/wechat/common/menu_public_manage.php';

		if($menu->str!= '{ "button": [  ] }'){
			$result=wechat_menu_create($ACC_TOKEN,$menu->str);
		}else{
			$result=wechat_menu_delete($ACC_TOKEN);
		}
		if($result!='0'){
			if($result=='41001'||$result=='40001'){
				$allresult=$allresult."'".$WECHAT_NIKENAME."'公众号的AppId或AppSecret有错误，菜单同步失败  ";
				
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
