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
include '../../wesite/common/dbaccessor.php';
include 'menu_permission_check.php';

$allresult='';
$wechaterror=null;
$wechat_group=getWechatGroupInfo_gweid($_SESSION['GWEID']);//这个是虚拟号信息
foreach($wechat_group as $wgroup){
	$WEID=$wgroup->WEID;
	$user_id=$wgroup->user_id;
	$shared_flag=$wgroup->shared_flag;
}

//自己的也要处理一遍
$weidselfinfo=web_admin_usechat_prisvcinfo_group($_SESSION['GWEID']);//所有变成共享依附于虚拟号的号，并且是私人的
foreach($weidselfinfo as $winfo){
	$APPID=$winfo->menu_appId;
	$APPSECRET=$winfo->menu_appSc;
	$WECHAT_NIKENAME=$winfo->wechat_nikename;
	$wid=$winfo->wid;
	$ACC_TOKEN=re_Token($APPID,$APPSECRET);
	$resultself=wechat_menu_delete($ACC_TOKEN);
	if($resultself!='0'){
		if($resultself=='41001'||$resultself=='40001'){
			$allresult=$allresult."'".$WECHAT_NIKENAME."'公众号的AppId或AppSecret有错误，其菜单删除失败  ";
			
		}else{
			if(empty($wechaterror)){
				if(in_array($WECHAT_RESPONSE[$resultself],$WECHAT_RESPONSE)){
					$wechaterror=$WECHAT_RESPONSE[$resultself];
				}else{
					$wechaterror="菜单删除出现错误";
				}
			}
		}
	}	
}
		
if($shared_flag==2){
	$gweids=getWechatGroupInfo_gweid_shared($user_id);//所有变成共享依附于虚拟号的号
	foreach($gweids as $gweidinfo){
		$gweid=$gweidinfo->GWEID;
		$weidinfo=web_admin_usechat_prisvcinfo_group($gweid);//所有变成共享依附于虚拟号的号，并且是私人的
		foreach($weidinfo as $winfo){
			$APPID=$winfo->menu_appId;
			$APPSECRET=$winfo->menu_appSc;
			$WECHAT_NIKENAME=$winfo->wechat_nikename;
			$wid=$winfo->wid;
			$ACC_TOKEN=re_Token($APPID,$APPSECRET);
			$result=wechat_menu_delete($ACC_TOKEN);	
			if($result!='0'){
				if($result=='41001'||$result=='40001'){
					$allresult=$allresult."'".$WECHAT_NIKENAME."'公众号的AppId或AppSecret有错误，其菜单删除失败  ";
					
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
	}
}

$menu_del_all=wechat_menu_del_all($_SESSION['GWEID']);//这里还是删除虚拟号的，个人的那些没共享的不删除
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