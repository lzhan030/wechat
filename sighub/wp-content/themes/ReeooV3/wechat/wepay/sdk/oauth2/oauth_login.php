<?php
session_start();
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');

$appid = "wxf8fbb548c7f17438";
$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appid.'&redirect_uri='.home_url().'/wp-content/themes/ReeooV3/wechat/wepay/sdk/oauth2/oauth_userinfo.php&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';
header("Location:".$url);

?>