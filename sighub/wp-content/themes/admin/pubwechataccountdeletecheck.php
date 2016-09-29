<?php

require_once './wp-content/themes/ReeooV3/wesite/common/dbaccessor.php';
require_once './wp-content/themes/ReeooV3/wechat/common/wechat_dbaccessor.php'; 
//加上这个代码，从js传参数过来

$wid=$_GET["wid"];
	   
$countuser = web_admin_wechats_info_bywid($wid);
if(($countuser == 0)){	
	$candel="yes";
}else{
	$candel="no";
	$name=":";
	$userid=wechat_usechat_get_disuid($wid);//找商家的名字列出来给管理员
	$first = true;
	foreach($userid as $uid){
		$usid=$uid->user_id;
		$usernames=wp_wechat_users_info($usid);		
		foreach($usernames as $username){
			$uname=$username->user_nicename;
			if($first){
				$first = false; 
				$name=$name.$uname;
			}else{
				$name=$name.",".$uname;
			}
		}
	}
}
$candelarr = array("iscandel"=>$candel,"business"=>$name);
echo json_encode($candelarr);

?>