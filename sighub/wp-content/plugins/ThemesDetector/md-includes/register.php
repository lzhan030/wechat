<?php
//added by Harvey for add registe hook 

add_action( 'user_register', 'initSpace',10,1 );
function initSpace($userid){
	global $wpdb;
	$wpdb->query( $wpdb->prepare("INSERT INTO ".$wpdb->prefix."wesite_space(userid, defined_space,  used_space)VALUES (%d, %f, %f)",$userid, 100, 0.00));
}
add_action( 'user_register', 'addExpireDate',10,1 );
function addExpireDate($userid){
	add_user_meta( $userid, "startdate", date("Y-m-d"), true ); 
	add_user_meta( $userid, "enddate", date("Y-m-d",strtotime("+3 month -1 day")), true ); 
}
add_action('user_register', 'addSchoolOptions',10,1 );
function addSchoolOptions($userid){
	add_user_meta( $userid, "school_homework_displaycount", 50, true ); 
	add_user_meta( $userid, "school_video_displaycount", 50, true ); 
	add_user_meta( $userid, "school_notice_displaycount", 50, true ); 
}