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
		return 'ERROR '.curl_error($ch);
	}	
	curl_close($ch);	
	return $res;
}
//封装参数
$appid = $_GET["appid"];
$secret = $_GET["secret"];
$reurl = $_GET["reurl"];
$code = $_GET["code"];

//获取token和openid
$get_token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$secret.'&code='.$code.'&grant_type=authorization_code';
$res=https_request($get_token_url);
$json_obj = json_decode($res,true);

//根据token和openid查询用户信息
$access_token = $json_obj['access_token'];
$openid = $json_obj['openid'];
$get_user_info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
$res=https_request($get_user_info_url);
$user_obj = json_decode($res,true);

//封装到session中
$_SESSION['oauthuser'] = $user_obj;


/**
* 处理json_encode乱码

$newData = array();
foreach( $user_obj as $key => $value )
{
	$newData[ $key ] = urlencode( $value );
}
echo urldecode( json_encode( $newData ) );
*/
//print_r($user_obj);
if(isset($reurl) && !empty($reurl))
header('Location: '.$reurl);
?>
