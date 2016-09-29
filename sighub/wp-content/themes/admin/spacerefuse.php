
<?php

require_once './wp-content/themes/ReeooV3/wesite/common/dbaccessor.php';
//加上这个代码，从js传参数过来
global $wpdb;

$id=$_GET["id"];

$wpdb->query( "UPDATE ".$wpdb->prefix."wesite_spaceapply  SET status = -1 WHERE id = ".$id.";");

	
?>