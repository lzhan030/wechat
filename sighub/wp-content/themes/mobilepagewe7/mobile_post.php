<?php
session_start();
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');

require_once '../ReeooV3/wesite/common/dbaccessor.php';
global $wpdb;

$weid =  $_GET['weid'];
$fromuser = $_SESSION['fromuser'];
$mid = $_SESSION['mid'];
$auth = $_SESSION['auth'];
?>

<?php

if( $_GET['action']=="mobiletheme" ){
	
	$redirect_url=$_GET['redirect_url'];
	$siteId = $_SESSION['orangeSite']; 
	$isShowPic = getSiteMeta('mobilethemeIsShowPic', $siteId);
	$isShowEditor = getSiteMeta('mobilethemeIsShowEditor', $siteId);
	$isShowVipmember = getSiteMeta('mobilethemeIsShowVipmember', $siteId);
	$isShowVipmember_editor = getSiteMeta('mobilethemeIsShowVipmemberEditor', $siteId);
	$useContact = getSiteMeta('mobilethemeContact', $siteId);
	
	$memberinfo=null;		
	if(!empty($fromuser)){		
		$memberinfo =  web_admin_member($weid, $fromuser);		
	}
	if((empty($memberinfo))&&(!empty($mid))){				
		$memberinfo =  web_admin_member_mid($mid,$weid);
		foreach($memberinfo as $minfo){
			$au_password=$minfo->password;
		}
		if($auth!= md5($mid.$au_password."weauth3647668")){
			$memberinfo=null;					
		}
		
	}
	if(($isShowVipmember_editor=='true')&&(empty($memberinfo))){
				
		$registerary = array("status"=>"success","message"=>"请登录","url"=>"/../ReeooV3/wesite/common/vip_login.php?weid={$weid}&redirect_url=".urlencode($redirect_url));				
		echo json_encode($registerary);
	}else{
	
		$blogTitle=$_POST['blogTitle'];
		$blogContent=$_POST['blogContent'];
		$blogAuthor=$_POST['blogAuthor'];
		$postSiteId = $_POST['siteId'];

        $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->prefix}users(user_login,user_nicename,display_name)VALUES (%s,%s,%s)",md5($blogAuthor.time()),$blogAuthor, $blogAuthor));
		$userId =$wpdb->insert_id;
		
        $result=$wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->prefix}posts(post_author,post_date,post_date_gmt,post_content,post_title,post_excerpt,post_name,to_ping, pinged, post_content_filtered)VALUES (%s,now(),utc_timestamp(),%s,%s,%s,%s,%s,%s,%s)",$userId,$blogContent,$blogTitle,'',$blogAuthor,'','',$postSiteId));
		
		$registerary = array("status"=>"insertsuc","message"=>"发表成功");				
		echo json_encode($registerary);
	
	}
}
?>