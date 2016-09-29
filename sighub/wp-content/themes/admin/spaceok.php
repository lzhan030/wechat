
<?php

require_once './wp-content/themes/ReeooV3/wesite/common/dbaccessor.php';
//加上这个代码，从js传参数过来

global $wpdb;

$id = $_GET["id"];
$space = $_GET["space"];

$userid = $wpdb -> get_var($wpdb->prepare("SELECT userid FROM ".$wpdb->prefix."wesite_spaceapply WHERE id=%d",$id));
$wpdb->query( "UPDATE ".$wpdb->prefix."wesite_spaceapply  SET status = 1 WHERE id = ".$id.";");
//更新userspace表	
$wpdb->query( "UPDATE ".$wpdb->prefix."wesite_space SET defined_space =defined_space+".$space." WHERE userid = ".$userid);
	
?>