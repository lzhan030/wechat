<?php
	require_once './wp-content/themes/ReeooV3/wesite/common/dbaccessor.php';
	global $wpdb;

	$id=$_GET["id"];
	$wpdb->query( "UPDATE ".$wpdb->prefix."wechat_accountapply  SET status = -1 WHERE id = ".$id.";");
?>