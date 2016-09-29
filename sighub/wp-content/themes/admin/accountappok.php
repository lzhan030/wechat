
<?php

	require_once './wp-content/themes/ReeooV3/wesite/common/dbaccessor.php';

	global $wpdb;

	$id = $_GET["id"];
	$user_account = $_GET["account"];

	$userid = $wpdb -> get_var($wpdb->prepare("SELECT userid FROM ".$wpdb->prefix."wechat_accountapply WHERE id=%d",$id));
	$wpdb->query( "UPDATE ".$wpdb->prefix."wechat_accountapply  SET status = 1 WHERE id = ".$id.";");
	update_user_meta( $userid, "useraccount", $user_account, "" );
?>