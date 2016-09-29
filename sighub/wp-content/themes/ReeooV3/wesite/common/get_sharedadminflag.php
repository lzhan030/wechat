<?php
session_start();
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
global $wpdb;
global $current_user;  //update to posted userid
$gweid = $_POST['gweid'];
$weid = $_POST['weid'];

//获取当前数据库中adminshare_flag的值
$sql = $wpdb -> prepare("SELECT adminshare_flag FROM ".$wpdb->prefix."wechat_group w1 where w1.GWEID = %d AND w1.WEID = %d", $gweid, $weid);
$result = $wpdb->get_results($sql);
foreach($result as $res){
	$adminshare_flag = $res -> adminshare_flag;
}
//TODO Verify the GWEID is belong to the user by Harvey
echo json_encode(array('status' => $adminshare_flag ,'message' => ''));

?>