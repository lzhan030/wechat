<?php
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
//require_once ($wp_root_path . '../wp-load.php');

//include_once 'web_constant.php';
/*
function web_admin_get_table_name($name) 
{
	global $wpdb;
	return $wpdb->prefix.$name;
}*/


//初始化用户功能表wp_wechat_initfunc_info
function web_admin_initfunc($userId)
{
	global $wpdb;
	
	$wpdb->query( $wpdb->prepare("INSERT INTO ".web_admin_get_table_name("wechat_initfunc_info")."(user_id, func_name, func_flag)VALUES (%d, %s, %s)",$userId, 'wechatwebsite', 0));
	$wpdb->query( $wpdb->prepare("INSERT INTO ".web_admin_get_table_name("wechat_initfunc_info")."(user_id, func_name, func_flag)VALUES (%d, %s, %s)",$userId, 'wechatfuncfirstconcern', 0));
	$wpdb->query( $wpdb->prepare("INSERT INTO ".web_admin_get_table_name("wechat_initfunc_info")."(user_id, func_name, func_flag)VALUES (%d, %s, %s)",$userId, 'wechatfunckeywordsreply', 0));
	$wpdb->query( $wpdb->prepare("INSERT INTO ".web_admin_get_table_name("wechat_initfunc_info")."(user_id, func_name, func_flag)VALUES (%d, %s, %s)",$userId, 'wechatfuncnokeywordsreply', 0));
	$wpdb->query( $wpdb->prepare("INSERT INTO ".web_admin_get_table_name("wechat_initfunc_info")."(user_id, func_name, func_flag)VALUES (%d, %s, %s)",$userId, 'wechatfuncmanualreply', 0));
	$wpdb->query( $wpdb->prepare("INSERT INTO ".web_admin_get_table_name("wechat_initfunc_info")."(user_id, func_name, func_flag)VALUES (%d, %s, %s)",$userId, 'wechatfuncaccountmanage', 0));
	$wpdb->query( $wpdb->prepare("INSERT INTO ".web_admin_get_table_name("wechat_initfunc_info")."(user_id, func_name, func_flag)VALUES (%d, %s, %s)",$userId, 'wechatfuncmaterialmanage', 0));
	$wpdb->query( $wpdb->prepare("INSERT INTO ".web_admin_get_table_name("wechat_initfunc_info")."(user_id, func_name, func_flag)VALUES (%d, %s, %s)",$userId, 'wechatfuncmenumanage', 0));
	$wpdb->query( $wpdb->prepare("INSERT INTO ".web_admin_get_table_name("wechat_initfunc_info")."(user_id, func_name, func_flag)VALUES (%d, %s, %s)",$userId, 'wechatfuncusermanage', 0));
	$wpdb->query( $wpdb->prepare("INSERT INTO ".web_admin_get_table_name("wechat_initfunc_info")."(user_id, func_name, func_flag)VALUES (%d, %s, %s)",$userId, 'wechatactivity_coupon', 0));
	$wpdb->query( $wpdb->prepare("INSERT INTO ".web_admin_get_table_name("wechat_initfunc_info")."(user_id, func_name, func_flag)VALUES (%d, %s, %s)",$userId, 'wechatactivity_scratch', 0));
	$wpdb->query( $wpdb->prepare("INSERT INTO ".web_admin_get_table_name("wechat_initfunc_info")."(user_id, func_name, func_flag)VALUES (%d, %s, %s)",$userId, 'wechatactivity_fortunewheel', 0));
	$wpdb->query( $wpdb->prepare("INSERT INTO ".web_admin_get_table_name("wechat_initfunc_info")."(user_id, func_name, func_flag)VALUES (%d, %s, %s)",$userId, 'wechatactivity_toend', 0));
	$wpdb->query( $wpdb->prepare("INSERT INTO ".web_admin_get_table_name("wechat_initfunc_info")."(user_id, func_name, func_flag)VALUES (%d, %s, %s)",$userId, 'wechatactivity_fortunemachine', 0));
	$wpdb->query( $wpdb->prepare("INSERT INTO ".web_admin_get_table_name("wechat_initfunc_info")."(user_id, func_name, func_flag)VALUES (%d, %s, %s)",$userId, 'wechatvip', 0));
	$wpdb->query( $wpdb->prepare("INSERT INTO ".web_admin_get_table_name("wechat_initfunc_info")."(user_id, func_name, func_flag)VALUES (%d, %s, %s)",$userId, 'template_selno', 0));
	
	
	
	return $site_id;
}


?>