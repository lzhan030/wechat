<?php
session_start();
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');

require_once '../ReeooV3/wesite/common/dbaccessor.php';
global $wpdb;

/**
*@function: get
*/

$gweid =  $_GET['gweid'];
$mid =$_SESSION['gmid'][intval($gweid)]['mid'];
$auth =	$_SESSION['gmid'][intval($gweid)]['auth'];
$siteId = $_GET['siteId'];
if(empty($siteId)){
	$siteId = $_SESSION['orangeSite'];
}else{
	$_SESSION['orangeSite'] = $siteId;
}

/**
*@function:封装gweid
*/
if(!empty($siteId)){
	$site=web_admin_get_site($siteId);
	foreach($site as $siteinfo){
		$userid=$siteinfo->site_user;
		$gweid=$siteinfo->GWEID;
		$gweidt=$siteinfo->GWEID;
	}
	
	//20150417 sara new added
	//根据当前的gweid去查找有没有处在共享虚拟号下，如果是虚拟号下的，需要将gweid换为虚拟号的gweid
	$gweid = virtualgweid_open($gweid);

	$mid =$_SESSION['gmid'][intval($gweid)]['mid'];
	$auth =	$_SESSION['gmid'][intval($gweid)]['auth'];
}

/**
*@function:判断会员是否审核
*/
$vipauditinfo=web_admin_usechat_info_group($gweid);
foreach($vipauditinfo as $vaudit){
	$vipaudit=$vaudit->wechat_vipaudit;
}
/*获取fromuser*/
$fromuser=$_SESSION['gopenid'][intval($gweid)];
$weid =  $_SESSION['weid'][intval($gweid)];
//如果没有获取到fromuser则通过oauth的获取试试
if(empty($fromuser)){
	if($_SESSION['oauth_openid_common']['gweid']==$gweid){
		$fromuser=$_SESSION['oauth_openid_common']['openid'];
		$weid=$_SESSION['oauth_weid_common']['weid'];
	}
}
?>

<?php

if( $_GET['action']=="mobiletheme" ){
	
	$redirect_url=$_GET['redirect_url'];
	if(empty($siteId)){
		$siteId = $_SESSION['orangeSite']; 
	}
	$isShowPic = getSiteMeta('mobilethemeIsShowPic', $siteId);
	$isShowEditor = getSiteMeta('mobilethemeIsShowEditor', $siteId);
	$isShowVipmember = getSiteMeta('mobilethemeIsShowVipmember', $siteId);
	$isShowVipmember_editor = getSiteMeta('mobilethemeIsShowVipmemberEditor', $siteId);
	$useContact = getSiteMeta('mobilethemeContact', $siteId);
	
	/**
	*@function:通过fromuser拿到会员信息
	*/
	$memberinfo=null;
	$memberinfo_wgroup=null;
	/*if((!empty($fromuser))&&(!empty($weid))){		
		//20140624 janeen update
		//$memberinfo =  web_admin_member($weid, $fromuser);
		$memberinfo_wgroup =  web_admin_member_wgroup($weid, $fromuser);				
	}else*/ if((!empty($fromuser))&&(!empty($weid))&&(!empty($gweid))){
				$memberinfo_wgroup =  web_admin_member_wgroup($weid,$gweid,$fromuser);				
	}
	if(!empty($memberinfo_wgroup)){
		foreach($memberinfo_wgroup as $minfo_wgroup){
			$mid=$minfo_wgroup->mid;
		}
	  //$memberinfo =  web_admin_member_mid($mid,$weid);
		$memberinfo =  web_admin_member_mid_group($mid,$gweid);
		foreach($memberinfo as $minfo){
			$isaudit=$minfo->isaudit;
		}
	}else{
		$memberinfo=null;
	}
	
	/**
	*@function:已经登陆通过mid拿到会员信息
	*/
	if((empty($memberinfo))&&(!empty($mid))){				
		//$memberinfo =  web_admin_member_mid($mid,$weid);
		$memberinfo =  web_admin_member_mid_group($mid,$gweid);
		foreach($memberinfo as $minfo){
			$au_password=$minfo->password;
			$isaudit=$minfo->isaudit;
		}
		if($auth!= md5($mid.$au_password."weauth3647668")){
			$memberinfo=null;
			unset($_SESSION['gmid'][intval($gweid)]);
		}		
	}
	
	$result = web_user_display_index_groupnew_wesforsel($gweid);
	foreach($result as $initfunc){
		if($selCheck[$initfunc->func_name] == 0)
			$selCheck[$initfunc->func_name] = $initfunc->status;
	}
	
	if(($selCheck['wechatvip']==1)&&(($isShowVipmember_editor=='true')&&(empty($memberinfo)))){
				
		$registerary = array("status"=>"success","message"=>"请登录","url"=>"/../ReeooV3/wesite/common/vip_login.php?gweid={$gweid}&redirect_url=".urlencode($redirect_url));				
		echo json_encode($registerary);
	}else if(($selCheck['wechatvip']==1)&&(($isShowVipmember_editor == 'true')&&((!empty($memberinfo))&&($vipaudit=='1')&&(($isaudit=='2')||($isaudit=='0'))))){
		if($isaudit=='2'){
			$hintmessage="会员身份需要审核";
		}else if($isaudit=='0'){
			$hintmessage="会员申请已被拒绝";
		}		
		$registerary = array("status"=>"success","message"=>$hintmessage,"url"=>"/../ReeooV3/wesite/common/vip_perdenied.php?gweid={$gweid}&isaudit={$isaudit}&redirect_url=".urlencode($redirect_url));				
		echo json_encode($registerary);
	}else{
		
		$blogTitle=$_POST['blogTitle'];
		$blogContent=stripslashes($_POST['blogContent']);
		$blogAuthor=$_POST['blogAuthor'];
		$postSiteId = $_POST['siteId'];

		//echo $blogContent;
		global $wpdb;
		
		$wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->prefix}users(user_login,user_nicename,display_name)VALUES (%s,%s,%s)",md5($blogAuthor.time()),$blogAuthor, $blogAuthor));

		$userId =$wpdb->insert_id;
		
		$result=$wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->prefix}posts(post_author,post_date,post_date_gmt,post_content,post_title,post_excerpt,post_name,to_ping, pinged, post_content_filtered)VALUES (%s,now(),utc_timestamp(),%s,%s,%s,%s,%s,%s,%s)",$userId,$blogContent,$blogTitle,'',$blogAuthor,'','',$postSiteId));
		
		$registerary = array("status"=>"insertsuc","message"=>"发表成功");				
		echo json_encode($registerary);
		
	
	}
}
?>