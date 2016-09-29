<?php
$tmp_path = explode ( 'ReeooV3', __FILE__ );
$template_path=$tmp_path[0];
require_once $template_path.'ReeooV3/wechat/common/session.php';
require_once '../wesite/common/dbaccessor.php';
include '../wechat/common/wechat_dbaccessor.php';


global $wpdb;
$groupid = $_POST["id"];
/* $results = array();
$userList=wechat_group_user_list($groupid);
foreach($userList as $user){
    $nodes = array();
	$nodes['id'] = $user->id;
	$nodes['name'] = $user->name;
	array_push($results,$nodes);
}

echo json_encode($results); */


$results = array();
$jsonresult[] = array();
$getuserids = wechat_group_user_list($groupid);
foreach($getuserids as $getuserid)
{
   
	$guserid = $getuserid -> id;
	$username = $getuserid->name;
	
	
	$getwechatnames = wechat_group_user_account_list($guserid);
	$getwechatnamecounts = wechat_group_user_account_count($guserid);
	foreach($getwechatnamecounts as $getwechatnamecount)
	{
		$gusercount = $getwechatnamecount -> ucount;
	}
	foreach($getwechatnames as $getwechatname)
	{
		//$gweid = $getwechatname -> gweid;
		//$wechat_nikename = $getwechatname->nickname;
		$nodes = array();
		$nodes['count'] = $gusercount;
		$nodes['userid'] = $guserid;
		$nodes['username'] = $username;
		$nodes['gweid'] = $getwechatname -> gweid;
		$nodes['wechat_nikename'] = $getwechatname->nickname;

		array_push($results,$nodes);
	}

	//array_push($jsonresult,$results);
}

echo json_encode($results);
//echo json_encode($jsonresult);
?>