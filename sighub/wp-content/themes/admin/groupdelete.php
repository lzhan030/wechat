<?php

require_once './wp-content/themes/ReeooV3/wesite/common/dbaccessor.php';
require_once './wp-content/themes/ReeooV3/wechat/common/wechat_dbaccessor.php'; 
//加上这个代码，从js传参数过来
global $wpdb;
$groupid = $_GET['del'];


if(isset($_GET['del']) && !empty($_GET['del']) ){
	$getgusers = $wpdb->get_results($wpdb->prepare("SELECT count(*) as countguser FROM ".$wpdb->prefix."user_group where group_id = %d",$groupid));
	foreach($getgusers as $guser){
		$gcount = $guser -> countguser;
	}
	if($gcount == 0)
	{
		$del_group=wp_delete_group($groupid);
		if($del_group===false){
			echo "删除失败";
		}
		else
			echo "删除成功";
	}
	else{
		echo "该分组有用户，您不能删除";
	}

}

?>