<?php

require_once './wp-content/themes/ReeooV3/wesite/common/dbaccessor.php';
require_once './wp-content/themes/ReeooV3/wechat/common/wechat_dbaccessor.php'; 
//加上这个代码，从js传参数过来

$wid=$_GET["id"];

//微信端的自定义菜单删除
require_once './wp-content/themes/ReeooV3/wechat/common/menu_delete_forwechat.php';

$account_delete=adminaccount_delete($wid);//删除了wechats和usechat
if($account_delete===false){
	echo "删除失败";
}else{
	$wechats_info_del=web_admin_delete_wechats_info($wid);//删除wechasinfo	
	if($wechats_info_del===false){
		echo "删除失败";
	}else{
		$user_menu_del=wechat_public_user_menu_del($wid);//删除user_menu对应记录
		if($user_menu_del===false){
			echo "删除失败";
		}else{
			echo "删除成功";
		}
	}	
}		

?>