<?php
session_start();
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
header("Content-type:text/html;charset=utf-8");
function https_request($url){	
	$ch = curl_init();	
	curl_setopt($ch,CURLOPT_URL,$url);	
	curl_setopt($ch,CURLOPT_HEADER,0);	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );	
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);	
	$res = curl_exec($ch);	
	if (curl_errno($ch)) {
		return FALSE;
	}	
	curl_close($ch);	
	return $res;
}
//封装参数
$appid = $_GET["appid"];
$secret = $_GET["secret"];
$reurl = $_GET["reurl"];
$code = $_GET["code"];
$gweid = $_GET["gweid"];

//获取token和openid
$get_token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$secret.'&code='.$code.'&grant_type=authorization_code';
$res=https_request($get_token_url);
if($res===FALSE){
	$errorcode="error";
}else{
	$json_obj = json_decode($res,true);
	if(!empty($json_obj['openid'])){
		//根据token和openid查询用户信息
		$openid = $json_obj['openid'];
		$access_token = $json_obj['access_token'];
		$expires_in=$json_obj['expires_in'];
		$expires_time=time()+$expires_in;//防止accesstoken过期但session还存在
		$oauth_openid=array('gweid'=>$gweid,'openid'=>$openid);
		$_SESSION['oauth_openid'] = $oauth_openid;
		$_SESSION['addaccesstoken'] = $access_token;
		$_SESSION['expires_time'] = $expires_time;
	}else{
		$errorcode="error";
	}
}

if(isset($reurl) && !empty($reurl))
header('Location: '.$reurl."&code=".$code."&state=123"."&errorcode=".$errorcode);
?>
