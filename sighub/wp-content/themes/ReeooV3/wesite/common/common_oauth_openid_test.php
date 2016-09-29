<?php
session_start();
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
header("Content-type:text/html;charset=utf-8");

function web_admin_getpri_gweid_oauth($gweid){	
	global $wpdb;
	$winfo= $wpdb->get_row( "SELECT u1.WEID,u2.wid,u2.menu_appId,u2.menu_appSc FROM {$wpdb->prefix}wechat_usechat u1,{$wpdb->prefix}wechats u2 where u1.wid=u2.wid and (u2.wechat_type='pri_svc' and u2.wechat_auth='1') and u1.GWEID='".intval($gweid)."'" ,ARRAY_A);
	return $winfo;
}
//封装参数
$appid = $_GET["appid"];
$secret = $_GET["secret"];
$reurl = $_GET["reurl"];
$code = $_GET["code"];
$gweid = $_GET["gweid"];
$gweidtrue = $_GET["gweidtrue"];


$oauth_openid=array('gweid'=>$gweid,'openid'=>substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'),0,8));
$_SESSION['oauth_openid_common'] = $oauth_openid;
$info=web_admin_getpri_gweid_oauth($gweidtrue);
$oauth_weid=array('gweid'=>$gweid,'weid'=>$info['WEID']);
$_SESSION['oauth_weid_common'] = $oauth_weid;

		
if(isset($reurl) && !empty($reurl))
header('Location: '.$reurl."&code=".$code."&state=123"."&errorcode=".$errorcode);
?>