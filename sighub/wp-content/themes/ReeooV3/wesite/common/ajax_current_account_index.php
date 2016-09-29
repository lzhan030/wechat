<?php
session_start();
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
global $wpdb;
global $current_user;
if(empty($current_user -> ID)){
	echo json_encode(array('status' => 'reload', 'message' =>''));
	exit();
}


if($_SESSION['GWEID'] != NULL){
	echo json_encode(array('status' => 'success', 'message' =>''));
	exit();
}

$account_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb -> prefix}wechat_group where user_id=".$current_user -> ID." AND WEID != 0 order by WEID" );
if( empty( $account_count )){
	echo json_encode(array('status' => 'failed', 'message' =>'您未添加公众号，请先添加公众号。'));
	exit();
}

if( $_SESSION['GWEID'] == NULL ){
	echo json_encode(array('status' => 'failed', 'message' =>'您未选择公众号，请先切换公众号。'));
	exit();
}






?>