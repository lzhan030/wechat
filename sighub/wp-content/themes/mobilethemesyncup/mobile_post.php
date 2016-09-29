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
		$blogContent=$_POST['blogContent'];
		$blogAuthor=$_POST['blogAuthor'];
		$postSiteId = $_POST['siteId'];

		$wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->prefix}users(user_login,user_nicename,display_name)VALUES (%s,%s,%s)",md5($blogAuthor.time()),$blogAuthor, $blogAuthor));

		$userId =$wpdb->insert_id;
		
		
		//utc_timestamp()比now少8个小时，gmt时间
		$wpdb->query( $wpdb->prepare(
		"
			INSERT INTO $wpdb->posts (post_author,post_date,post_date_gmt,post_content,post_title,post_excerpt,post_name,to_ping, pinged, post_content_filtered)
			VALUES ( %d, now(), utc_timestamp(), %s, %s, %s, %s, %s, %s, %s)
		",

		$userId,$blogContent,$blogTitle,'',$blogAuthor,'','',$postSiteId));
		
		//echo "当前插入的sql语句:".$wpdb -> last_query;
		
		//取到最新的插入的postid
		$insert_postid=$wpdb->insert_id;
		
		//20141117同步到第三方server功能
		if($result!==false){
			
			//20141014add post data to new server
			//$url = "http://www.qq.com";
			$url = THIRD_PARTY_ACCESS_URL;        //这个是在wp-config.php中定义的变量
			//$url = "http://2.wpcloudforsina.sinaapp.com/test-1.0.0-BUILD-SNAPSHOT/postmsg";
			//$url = "http://135.252.226.139:8080/test-1.0.0-BUILD-SNAPSHOT/postmsg";
			$post_data = array (
				"to_ping" => "",
				"pinged" => "",
				"post_title" => $blogTitle,
				"post_content" => $blogContent,
				"post_excerpt" => "",
				"post_content_filtered" => $postSiteId,
				"id" => intval($insert_postid),
				"post_date" => time()*1000,
				"post_name" => "",
				"guid" => "",
				"menu_order" => "",
				"post_type" => "post",
				"post_author" => $blogAuthor,
				"post_date_gmt" => strtotime("-8 hours")*1000,  //gmt时间少8个小时
				"post_status" => "open",
				"comment_status" => "open",
				"ping_status" => "",
				"post_password" => "",
				"post_modified" => "",
				"post_modified_gmt" => "",
				"post_parent" => "",
				"post_mime_type" => "",
				"comment_count" => 0
			);
			$postdata = json_encode($post_data);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 1);
			//指定post数据
			curl_setopt($ch, CURLOPT_POST, 1);
			//添加变量
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
			curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-Type:application/json;charset=UTF-8;Connection:close'));
			curl_setopt($ch,CURLOPT_USERAGENT,"Mozilla/5.0 (X11; Linux i686) AppleWebKit/535.2 (KHTML, like Gecko) Ubuntu/10.04 Chromium/15.0.874.106 Chrome/15.0.874.106 Safari/535.2");
			
			$output = curl_exec($ch);
			$error_code = curl_errno($ch);
			//echo "返回的错误信息:".$error_code;
			$rtn = curl_getinfo($ch,CURLINFO_HTTP_CODE);  
			//echo "返回的rtn信息:".$rtn;
			//返回200ok表示正确sync up了，否则重新提交
			if ($rtn != '200') {
				//向post_meta表中添加一条字段,flag表示文章同步，表示文章同步与否
				$wpdb->query( $wpdb->prepare("INSERT INTO $wpdb->postmeta(post_id, meta_key,meta_value)VALUES (%d, %s ,%s )",$insert_postid, 'syncup_status',0));
				//return $insert_postid."文章添加成功,但是同步失败,请到管理平台更新该文章重新进行同步";
				$registerary = array("status"=>"insertsyncfail","message"=>"发表成功，同步失败");
			}else{
				//向post_meta表中添加一条字段,flag表示文章同步，表示文章同步与否
				$wpdb->query( $wpdb->prepare("INSERT INTO $wpdb->postmeta(post_id, meta_key,meta_value)VALUES (%d, %s ,%s )",$insert_postid, 'syncup_status',1));
				//return $insert_postid."文章添加成功,同步也成功";
				$registerary = array("status"=>"insertsuc","message"=>"发表成功，同步成功");	
			}
			curl_close($ch);

		}else{
			//return "error文章添加失败,同步失败,请重新添加并同步";  // 创建成功
			$registerary = array("status"=>"error","message"=>"发表失败，请重试");
		}
		
					
		echo json_encode($registerary);
		
	
	}
}	
?>