<?php
session_start();
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
header("Content-type:text/html;charset=utf-8");
echo '<meta http-equiv="Content-Type" content="text/hmtl; charset=utf-8" />';	
function https_request($url){	
	$ch = curl_init();	
	curl_setopt($ch,CURLOPT_URL,$url);	
	curl_setopt($ch,CURLOPT_HEADER,0);	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );	
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);	
	$res = curl_exec($ch);	
	if (curl_errno($ch)) {
		return 'ERROR '.curl_error($ch);
	}	
	curl_close($ch);	
	return $res;
}

//封装参数
$appid = "wxf8fbb548c7f17438";
$appsecret = "f22242f0be30b86c9a1bc21ed81fb8fe";

//获取token
$get_token_url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$appsecret;
$res=https_request($get_token_url);
$json_obj = json_decode($res,true);

//更加token获取关注者列表
$access_token = $json_obj['access_token'];
$url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token=".$access_token;
$result = https_request($url);
$jsoninfo = json_decode($result, true);
var_dump($result);

?>
