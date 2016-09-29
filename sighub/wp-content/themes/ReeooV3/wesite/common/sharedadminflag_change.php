<?php
session_start();
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
global $wpdb;
global $current_user;  //update to posted userid
$groupgweid = $_POST['groupgweid'];
$weid = $_POST['weid'];
$flag = $_POST['flag'];
//将分组管理员的虚拟号对应的共享/不共享状态写入数据库
if($flag == 1){
	$myrows = $wpdb->query( "UPDATE ".$wpdb->prefix."wechat_group SET adminshare_flag = 1 WHERE GWEID = ".$groupgweid." AND WEID =".$weid." ;");		 	
	//需要将改组的会员对应的gweid全部对应到该虚拟号下
}else{
	//如果数据库里对应的adminshare_flag一直为0，则不做任何操作；如果adminshare_flag是由1变为0，则需要执行会员相关的
	$sql = $wpdb -> prepare("SELECT adminshare_flag FROM ".$wpdb->prefix."wechat_group w1 where w1.GWEID = %d AND w1.WEID = %d", $groupgweid, $weid);
	$result = $wpdb->get_results($sql);
	foreach($result as $res){
		$adminshare_flag = $res -> adminshare_flag;
	}
	if($adminshare_flag == 1){
		$myrows = $wpdb->query( "UPDATE ".$wpdb->prefix."wechat_group SET adminshare_flag = 0 WHERE GWEID = ".$groupgweid." AND WEID =".$weid." ;");		 	
	}
}
//TODO Verify the GWEID is belong to the user by Harvey
echo json_encode(array('status' => 'success','message' => ''));

?>