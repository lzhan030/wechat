<?php
   
$weinfo=wechat_wechats_get(intval($wid));
foreach($weinfo as $win){
	$APPID=$win->menu_appId;
	$APPSECRET=$win->menu_appSc;
}	
$ACC_TOKEN=re_Token($APPID,$APPSECRET);
$result=wechat_menu_delete($ACC_TOKEN);
?>