<?php
session_start();
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
global $wpdb;
global $current_user;  //update to posted userid
//$userid = $_POST['userid'];
if(empty($_POST['gweid']))
	exit();

//$condition = is_super_admin( $userId )?'':"AND user_id='{$current_user -> ID}'";
//20150415 sara new added
$getgroupuserids = $wpdb->get_results( "SELECT group_id, flag FROM {$wpdb -> prefix}user_group where user_id = ".$current_user -> ID);
if(!empty($getgroupuserids)){
	foreach($getgroupuserids as $getgroupinfo)
	{
	    $usergroupid = $getgroupinfo -> group_id;
	    $usergroupflag = $getgroupinfo -> flag;
	}
}else{  //分组里没有记录，则属于默认分组，groupid为0，对应的flag为0
	$usergroupid = 0;
	$usergroupflag = 0;
}
//如果是分组管理员，和admin的条件是类似的
//isset($_POST['user_id'])表示不是分组管理员 但是是分组管理员下的一个用户
if(is_super_admin( $userId ) || ($usergroupid !=0 && $usergroupflag == 1) || ($usergroupid !=0 && $usergroupflag == 1 && isset($_POST['user_id']))){
	$condition = '';
}else{	
	$condition = "AND user_id='{$current_user -> ID}'";
}
$_POST['gweid'] = intval($_POST['gweid']);
$gweid_flag = $wpdb -> get_var("SELECT shared_flag FROM {$wpdb -> prefix}wechat_group WHERE GWEID = '{$_POST['gweid']}' {$condition}");
//$gweid = $wpdb -> get_var("SELECT shared_flag FROM {$wpdb -> prefix}wechat_group WHERE GWEID = '{$_POST['gweid']}' AND user_id=".$userid);
if($gweid_flag === NULL || $gweid_flag === FALSE){
	exit();
}
	
if(is_super_admin( $userId ) || ($usergroupid !=0 && $usergroupflag == 1) || ($usergroupid !=0 && $usergroupflag == 1 && isset($_POST['user_id'])))
	$GWEID_matched_userid = $wpdb -> get_var("SELECT user_id FROM {$wpdb -> prefix}wechat_group WHERE `GWEID`='{$_POST['gweid']}' ");
else
	$GWEID_matched_userid = $current_user -> ID;

if($gweid_flag == 1){
	$gweid = $wpdb -> get_var("SELECT GWEID FROM {$wpdb -> prefix}wechat_group WHERE shared_flag = 2 AND user_id = {$GWEID_matched_userid}");
	//$gweid = $wpdb -> get_var("SELECT GWEID FROM {$wpdb -> prefix}wechat_group WHERE shared_flag = 2 AND user_id=".$userid);
	if($gweid === NULL || $gweid === FALSE)
	exit();
}else
	$gweid = $_POST['gweid'];

$_SESSION['GWEID'] = $gweid;
$_SESSION['exact_GWEID'] = $_POST['gweid'];
if(is_super_admin( $userId ) || ($usergroupid !=0 && $usergroupflag == 1) || ($usergroupid !=0 && $usergroupflag == 1 && isset($_POST['user_id']))){
	$_SESSION['GWEID_matched_userid'] = $GWEID_matched_userid;
	if($current_user ->ID == $_SESSION['GWEID_matched_userid'])
		unset($_SESSION['GWEID_matched_userid']);
}
//TODO Verify the GWEID is belong to the user by Harvey
echo json_encode(array('status' => 'success','gweid' => $_SESSION['GWEID'],'exact_gweid' => $_SESSION['exact_GWEID'], 'user_id' => empty($_SESSION['GWEID_matched_userid'])?$current_user ->ID:$_SESSION['GWEID_matched_userid'] ));

?>