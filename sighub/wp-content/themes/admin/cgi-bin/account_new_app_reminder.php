<?php
	$path = explode ( 'wp-content', __FILE__ );
	$wp_root_path = $path [0];
	require_once ($wp_root_path . '/wp-load.php');	

	$counts = $wpdb->get_results( "SELECT COUNT(*) as c FROM ".$wpdb->prefix."wechat_accountapply WHERE status = 0");
	$applynum = 0;
	if (!empty($counts)){
		foreach($counts as $count){
		   $applynum = $count->c;
		}
	}
	echo $applynum;
?>