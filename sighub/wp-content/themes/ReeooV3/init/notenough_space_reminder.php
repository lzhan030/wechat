<?php
	$tmp_path = explode ( 'ReeooV3', __FILE__ );
    $template_path=$tmp_path[0];
    require_once $template_path.'ReeooV3/wechat/common/session.php';
	$path = explode ( 'wp-content', __FILE__ );
	$wp_root_path = $path [0];
	require_once ($wp_root_path . '/wp-load.php');

	global $current_user,$wpdb;
	//当前用户有可能是分组管理员下的
	$getgroupuserids = $wpdb->get_results( "SELECT group_id, flag FROM {$wpdb -> prefix}user_group where user_id = ".$current_user->ID);
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
	$userid = ((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($usergroupid !=0 && $usergroupflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
	//$userid =  (is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;

	//Get the space data
	$userspace = $wpdb->get_results("SELECT * from ".$wpdb->prefix."wesite_space WHERE userid = ".$userid);
	foreach($userspace as $space){
		$totalspace = $space->defined_space;
		$usedspace = $space->used_space;
	}
	$space = number_format(($totalspace - $usedspace),3,".","");
	$ase=get_option( alarm_space ); 
	$reminder =0; // 0 - no need to apply more space; 1 - need to apply more space;
	if ($space<$ase) {
		$reminder = 1;
	} 
	echo $reminder;
?>