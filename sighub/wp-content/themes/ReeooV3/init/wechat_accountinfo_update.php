<?php 
$tmp_path = explode ( 'ReeooV3', __FILE__ );
$template_path=$tmp_path[0];
require_once $template_path.'ReeooV3/wechat/common/session.php';

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
require_once '../wesite/common/dbaccessor.php';
require_once '../wechat/common/wechat_dbaccessor.php';
require_once '../wechat/common/jostudio.wechatmenu.php';
global $wpdb;
global  $current_user;


$gweid = $_POST["id"];
$status = $_POST["status"];
$active = $_POST["active"];
$setActive = $_POST["setActive"];

if(empty($active)&&(empty($setActive))){//共享不共享的设置
	$update=getWechatGroup_update($status,$gweid);
	
	$weidinfo=web_admin_usechat_prisvcinfo_group($gweid);//个人服务号或者个人认证订阅号
	$pubweidinfo=web_admin_usechat_pubsvcinfo_group($gweid);//共用服务号或者共用认证订阅号，对现有菜单内容清空
	if(!empty($weidinfo)){//私人服务号或者认证订阅号,对微信菜单进行更新
		$info=getWechatGroupInfo_gweid($gweid);//get all info by gweid
		foreach($info as $winfos){
			$shared_flag=$winfos->shared_flag;
			$user_id=$winfos->user_id;
		}			
		if($shared_flag==1){
			$weinfo=getWechatGroupActiveInfo($user_id,2);//get info by userid and flg=2
			foreach($weinfo as $gweids){
				$GWEID=$gweids->GWEID;//虚拟号的GWEID
			}
		}else{	
				$GWEID=$gweid;//自己的GWEID
		}
		include 'wechat_accountinfo_menu.php';
		

	}else if(!empty($pubweidinfo)){//公共服务号或者公共认证订阅号,对菜单回复内容进行清空，采用切换后的素材重新设置

		foreach($pubweidinfo as $pubinfo){	
			$WEID=$pubinfo->WEID;
			$update=wechat_menu_public_updatenull("","",$WEID);
		}

	}
	echo json_encode(array());
}else if(!empty($active)){//激活不激活的设置
	if($status==1){//设置为激活
		$status=2;
		$update=getWechatGroup_update($status,$gweid);
		echo json_encode(array());
	}else{//设置为不激活，所有设置为共享的都要变成不共享
		$update=getWechatGroup_update($status,$gweid);
		$user_id=wechat_group($gweid);
		//所有激活变不激活，所有共享菜单都要重新生成
		$wechatsinfo=getWechatGroupActiveInfo($user_id,1);
		foreach($wechatsinfo as $wechatinfo){
			$gweid=$wechatinfo->GWEID;
			$weidinfo=web_admin_usechat_prisvcinfo_group($gweid);//个人服务号或者个人认证订阅号
			$pubweidinfo=web_admin_usechat_pubsvcinfo_group($gweid);//共用服务号或者共用认证订阅号，对现有菜单内容清空
			if(!empty($weidinfo)){//私人服务号或者认证订阅号,对微信菜单进行更新
				$GWEID=$gweid;//自己的GWEID
				include 'wechat_accountinfo_menu.php';
			}else if(!empty($pubweidinfo)){//公共服务号或者公共认证订阅号,对菜单回复内容进行清空，采用切换后的素材重新设置
				foreach($pubweidinfo as $pubinfo){	
					$WEID=$pubinfo->WEID;
					$update=wechat_menu_public_updatenull("","",$WEID);
				}
			}
		}		
		$update=getWechatGroupActive_update(0,$user_id);	
		echo json_encode(array());
	}
	
}else if(!empty($setActive)){
	$user_id=wechat_group($gweid);
	//将现用户设置为激活的变为不激活，切为共享
	$update=getWechatGroupActive_updateActive(1,2,$user_id);
	//设置该号为激活的微信号
	$update=getWechatGroup_update(2,$gweid);
	//所有设置为共享的号都重新生成当下激活的微信号的菜单+包括刚刚设置为激活号的这个微信号
	$wechatsinfo=getWechatGroupActiveAllInfo($user_id,1);
	foreach($wechatsinfo as $wechatinfo){
		$gweidshared=$wechatinfo->GWEID;
		$weidinfo=web_admin_usechat_prisvcinfo_group($gweidshared);
		$pubweidinfo=web_admin_usechat_pubsvcinfo_group($gweidshared);
		if(!empty($weidinfo)){	
			$GWEID=$gweid;//此处为激活的号的gweid
			include 'wechat_accountinfo_menu.php';
		}else if(!empty($pubweidinfo)){//公共服务号或者公共认证订阅号,对菜单回复内容进行清空，采用切换后的素材重新设置
			foreach($pubweidinfo as $pubinfo){	
				$WEID=$pubinfo->WEID;
				$update=wechat_menu_public_updatenull("","",$WEID);
			}
		}		
	}	
	echo json_encode(array());
}

?>