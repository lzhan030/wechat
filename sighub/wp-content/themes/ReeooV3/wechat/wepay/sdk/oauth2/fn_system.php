<?php
session_start();
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
header("Content-type:text/html;charset=utf-8");

if(empty($_SESSION['wechat_user'])){
	
	header("Location:".home_url()."/wp-content/themes/ReeooV3/wechat/wepay/sdk/oauth2/oauth_login.php");
}else{
	print_r($_SESSION['wechat_user']);
}

?>