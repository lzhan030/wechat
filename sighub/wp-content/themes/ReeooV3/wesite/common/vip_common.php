<?php
session_start();
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');

include_once 'dbaccessor.php';
require 'class.phpmailer.php';
global $wpdb;

/**
*@function: get
*/

$gweid =  $_GET['gweid'];
$weid =  $_SESSION['weid'][intval($gweid)];
$mid =$_SESSION['gmid'][intval($gweid)]['mid'];
$auth =	$_SESSION['gmid'][intval($gweid)]['auth'];
/*获取fromuser*/
$fromuser=$_SESSION['gopenid'][intval($gweid)];
//如果没有获取到fromuser则通过oauth的获取试试
if(empty($fromuser)){
	if($_SESSION['oauth_openid_common']['gweid']==$gweid){
		$fromuser=$_SESSION['oauth_openid_common']['openid'];
		$weid=$_SESSION['oauth_weid_common']['weid'];
	}
}
?>
<?php 
header("Content-type:application/json;charset=utf-8"); 

//会员注册
 if($_GET['action']=="vip_register" ){

    $realname = $_POST['user_realname'];
	$nickname = $_POST['user_displayname'];
	$mobilenumber = $_POST['user_mobile'];	
	$email = $_POST['user_email'];
	$password = $_POST['user_password'];	
	$uAgent = $_SERVER['HTTP_USER_AGENT']; 
	$osPat = "android|UCWEB|iPhone|iPad|BlackBerry|Symbian|Windows Phone|hpwOS"; 
	if(preg_match("/($osPat)/i", $uAgent )) { 
		$regtype = 'Mobile';//手机终端
	}else{    
		$regtype = 'Web';//pc端
	}
	if((!empty($weid))||(!empty($gweid))){
		$checkemail=web_admin_member_keyget_group($gweid,$email,"email");//20140624 janeen
		$checkmobilenumber=web_admin_member_keyget_group($gweid,$mobilenumber,"mobilenumber");
		
		$memberinfo =null;
		$memberinfo_wgroup=null;
		$vipauditinfo=web_admin_usechat_info_group($gweid);
		foreach($vipauditinfo as $vaudit){
			$vipaudit=$vaudit->wechat_vipaudit;
		}
		/*if((!empty($fromuser))&&(!empty($weid))){
			$memberinfo_wgroup =  web_admin_member_wgroup($weid, $fromuser);
		}else */if((!empty($fromuser))&&(!empty($weid))&&(!empty($gweid))){			
					$memberinfo_wgroup =  web_admin_member_wgroup($weid,$gweid,$fromuser);							
		}
		if(!empty($memberinfo_wgroup)){
			foreach($memberinfo_wgroup as $minfo_wgroup){
				$mid=$minfo_wgroup->mid;
			}
			$memberinfo =  web_admin_member_mid_group($mid,$gweid);
			foreach($memberinfo as $minfo){
				$isaudit=$minfo->isaudit;
			}
		}else{
			$memberinfo =null;
		}
		
		if(!empty($checkemail)){//邮箱已被注册			
			$registerary = array("status"=>"emailrep","message"=>"该邮箱已被占用");			
			echo json_encode($registerary);
		}else if(!empty($checkmobilenumber)){
			$registerary = array("status"=>"mobilenumberrep","message"=>"该手机号已被占用");			
			echo json_encode($registerary);
		}else if(empty($memberinfo)){
			if($vipaudit=='1'){
				$iscreate=web_admin_create_member_group($gweid, $realname, $nickname, $mobilenumber, $email, md5($password), $regtype,2);
			}else{
				$iscreate=web_admin_create_member_group($gweid, $realname, $nickname, $mobilenumber, $email, md5($password), $regtype,1);
			}
			if($iscreate) {
				$loginfo=web_admin_member_login_group($gweid,$email, md5($password));//20140624 janeen update
				foreach($loginfo as $loinf){
					$mid=$loinf->mid;
					$password=$loinf->password;
				}	
				$auth=md5($mid.$password."weauth3647668");
				//$_SESSION['mid']=$mid;
				//$_SESSION['auth']=$auth;
				$_SESSION['gmid'][intval($gweid)] = array('mid'=>$mid ,'auth'=>$auth);
				
				if((!empty($fromuser))&&(!empty($weid))&&(!empty($gweid))){
					$isinsert=web_admin_member_group_insert($mid,$weid,$gweid,$fromuser);
					if(!$isinsert){
						$registerary = array("status"=>"error","message"=>"系统出现问题，请稍后");
						echo json_encode($registerary);
					}else{
					//会员申请成功
						if($vipaudit!='1'){
							$registerary = array("status"=>"success","message"=>"会员申请成功","url"=>"vip_detail.php?gweid=".$gweid."#wechat_redirect");
							echo json_encode($registerary);
						}else{
							$registerary = array("status"=>"success","message"=>"请等待审核","url"=>"vip_perdenied.php?gweid=".$gweid."&iswait=".$vipaudit."#wechat_redirect");
							echo json_encode($registerary);
						}
					}
				}else{
					//会员申请成功
					if($vipaudit!='1'){
						$registerary = array("status"=>"success","message"=>"会员申请成功","url"=>"vip_detail.php?gweid=".$gweid."#wechat_redirect");
						echo json_encode($registerary);
					}else{
						$registerary = array("status"=>"success","message"=>"请等待审核","url"=>"vip_perdenied.php?gweid=".$gweid."&isaudit=1#wechat_redirect");
						echo json_encode($registerary);
					}
				}
			}else{
				$registerary = array("status"=>"error","message"=>"出现错误");
				echo json_encode($registerary);
			}
		}else if(($vipaudit=='1')&&(($isaudit=='0')||($isaudit=='2'))){
			$registerary = array("status"=>"success","message"=>"请等待审核","url"=>"vip_perdenied.php?gweid=".$gweid."&isaudit=".$isaudit."#wechat_redirect");
			echo json_encode($registerary);	
		}else{
			$registerary = array("status"=>"success","message"=>"已有账号","url"=>"vip_detail.php?gweid=".$gweid."#wechat_redirect");
			echo json_encode($registerary);
		}
	}else{	
		$registerary = array("status"=>"error","message"=>"出现错误");
		echo json_encode($registerary);
	}
}	
 
 
 //更新会员信息
 if( $_GET['action']=="vip_detail" ){

    $realname = $_POST['user_realname'];
	$nickname = $_POST['user_nickname'];
	$point = $_POST['user_point'];
	$level = $_POST['user_level'];
	$rtime = $_POST['user_rtime'];
	$mobilenumber = $_POST['user_mobile'];
	$email = $_POST['user_vemail'];
	$password = $_POST['user_password'];	
	
	$emailup=web_admin_member_upemail_group($gweid,$mid,$email);//20140624 janeen update
	$mobilenumberup=web_admin_member_upmobilenumber_group($gweid,$mid,$mobilenumber);
	if(!empty($emailup)){
		$registerary = array("status"=>"emailrep","message"=>"该邮箱已被占用");			
		echo json_encode($registerary);
	}else if(!empty($mobilenumberup)){
		$registerary = array("status"=>"mobilenumberrep","message"=>"该手机号已被占用");			
		echo json_encode($registerary);
	}else{
		if(!empty($password)){
			$returnvalue = web_admin_update_member_pwd($mid, $realname, $nickname, $point, $level, $rtime,$mobilenumber,$email,$password);
			if($returnvalue > 0){	
				$auth=md5($mid.md5($password)."weauth3647668");
				//$_SESSION['auth']=$auth;
				$_SESSION['gmid'][intval($gweid)] = array('mid'=>$mid ,'auth'=>$auth);
			}
		}else{
			$returnvalue = web_admin_update_member_nopwd($mid, $realname, $nickname, $point, $level, $rtime,$mobilenumber,$email);
		}

		if($returnvalue > 0)
			$flag = true;
			$registerary = array("status"=>"success","message"=>"会员信息更新成功","url"=>"vip_detail.php?gweid=".$gweid."#wechat_redirect");			
			echo json_encode($registerary);
	}
}

//邮箱验证
if( $_GET['action']=="vip_repassword" ){
	$email=$_POST['user_email'];
	
	$info=web_admin_member_keyget_group($gweid,$email,"email");//20140624 janeen update
	if(empty($info)){
		$registerary = array("status"=>"nouser","message"=>"没有找到该用户");			
		echo json_encode($registerary);
	}else{
		try {
			$newpass = substr(md5(time()), 0, 6);			
			$mail = new PHPMailer(true); //New instance, with exceptions enabled
			$body = "您的新密码为:".$newpass."<br><br>此邮件为系统邮件，请勿直接回复"; // 发送的内容 
			$body = preg_replace('/\\\\/','', $body); //Strip backslashes
			$mail->CharSet = "UTF-8"; //解决乱码问题
			$mail->IsSMTP(); // tell the class to use SMTP
			$mail->SMTPAuth = true; // enable SMTP authentication
			$mail->Port = 25; // set the SMTP server port
			$mail->Host = "smtp.163.com"; // SMTP server
			$mail->Username = "al_orangeteam@163.com"; // SMTP server username
			$mail->Password = "orangenewsys"; // 填写你自己邮箱的密码
			//$mail->IsSendmail(); // tell the class to use Sendmail
			$mail->From =  "al_orangeteam@163.com"; //发件人邮箱
			$mail->FromName = "微官网"; //发件人 
			$to = $email;
			$mail->AddAddress($to);
			$mail->Subject = "微官网取回密码邮件 ";
			$mail->WordWrap = 80; // set word wrap
			$mail->MsgHTML($body);
			$mail->IsHTML(true); // send as HTML
			$mail->Send();
			
			$newpass=MD5($newpass);
			$uppwd=web_admin_update_password_group($gweid,$newpass,$email);//20140624 janeen update
			if($uppwd) {       
				$registerary = array("status"=>"success","message"=>"新密码已发送至您的邮箱，请查看","url"=>"vip_login.php?gweid=".$gweid."#wechat_redirect");				
				echo json_encode($registerary);
			}else{
				$registerary = array("status"=>"error","message"=>"出现错误");
				echo json_encode($registerary);
			}
		}catch (phpmailerException $e) {
				$registerary = array("status"=>"error","message"=>"出现错误");
				echo json_encode($registerary);
		}
	}
}

if( $_GET['action']=="vip_login" ){

	$email = $_POST['user_email'];
	$user_password = $_POST['user_password'];
	/**
	*@function:判断会员是否审核
	*/
	$vipauditinfo=web_admin_usechat_info_group($gweid);
	foreach($vipauditinfo as $vaudit){
		$vipaudit=$vaudit->wechat_vipaudit;
	}
	$logininfo=web_admin_member_login_group($gweid,$email,md5($user_password));//20140624 janeen update
	
	if(!empty($logininfo)){//login success			
		foreach($logininfo as $loinfo){				
			$mid=$loinfo->mid;
			$password=$loinfo->password;
			$isaudit=$loinfo->isaudit;
		}
		$auth=md5($mid.$password."weauth3647668");
		//$_SESSION['mid']=$mid;//登录成功参数放在session
		//$_SESSION['auth']=$auth;
		$_SESSION['gmid'][intval($gweid)] = array('mid'=>$mid ,'auth'=>$auth);
		if(!empty($fromuser)){
		    $vipinfo=web_admin_member_group_fromuser_isexist($mid,$fromuser);//20140624 janeen update
			if((empty($vipinfo))&&(!empty($weid))&&(!empty($gweid))){
			   $isinsert=web_admin_member_group_insert($mid,$weid,$gweid,$fromuser);
				if(!$isinsert){
					$registerary = array("status"=>"error","message"=>"系统出现问题，请稍后");
					echo json_encode($registerary);
				}
			}
		}
         	
			$urlparts = parse_url($_GET['redirect_url']);
			$query = parse_str($urlparts['parse_str']);
			
			$query['mid'] = $mid;
			
			$query['gweid'] = $gweid;
			$query['fromuser'] = $fromuser;
			$query['auth'] = $auth;
			
			$url=$_GET['redirect_url'];
			if(!empty($url)){
				$tmp=array();
				$noinfo=false;
				$ifhaveone=stristr($url,"?");
				$ifhavetwo=stristr($url,"#");
				$firloc=strpos($url,"?");
				$endloc=strpos($url,"#");
				
				if(($ifhaveone)&&($ifhavetwo)){	//有问号有井号				
					$query=substr($url,$firloc+1,$endloc-$firloc-1);
				}else if(($ifhaveone)&&(!$ifhavetwo)){//有问号无井号
					$query=substr($url,$firloc+1);
				}else{//无问号有井号+无问号无井号
					$noinfo=true;				
				}
				
				if(!$noinfo){
					$kvs=explode("&",$query);
					foreach($kvs as $k=>$v){
						$tmpkv = explode("=",$v);
						$tmp= array_merge ( $tmp, array($tmpkv[0] => $tmpkv[1] ) );
					}			
					
					if(empty($tmp['gweid'])){	
						$tmp['gweid'] = $gweid;
					}
					$queryString = http_build_query($tmp);					
					$las=explode("#",$url);					
					$paramurl=substr($url,0,$firloc)."?".$queryString.($las[1]?"#".$las[1]:'');
				}else{
					
					if(empty($tmp['gweid'])){
						$tmp['gweid'] = $gweid;
					}
					$queryString = http_build_query($tmp);
					$las=explode("#",$url);
					$paramurl=$url."?".$queryString.($las[1]?"#".$las[1]:'');
				}
				
				if(($vipaudit=='1')&&(($isaudit=='2')||($isaudit=='0'))){
					if($isaudit=='2'){
						$hintmessage="会员身份需要审核";
					}else if($isaudit=='0'){
						$hintmessage="会员申请已被拒绝";
					}
					$registerary = array("status"=>"success","message"=>$hintmessage,"url"=>"vip_perdenied.php?gweid=".$gweid."&isaudit=".$isaudit."#wechat_redirect");
					echo json_encode($registerary);
				}else{
					$registerary = array("status"=>"success","message"=>"登陆成功","url"=>$paramurl."#wechat_redirect");
					echo json_encode($registerary);
				}
			}else{
				if(($vipaudit=='1')&&(($isaudit=='2')||($isaudit=='0'))){
					if($isaudit=='2'){
						$hintmessage="会员身份需要审核";
					}else if($isaudit=='0'){
						$hintmessage="会员申请已被拒绝";
					}
					$registerary = array("status"=>"success","message"=>$hintmessage,"url"=>"vip_perdenied.php?gweid=".$gweid."&isaudit=".$isaudit."#wechat_redirect");
					echo json_encode($registerary);
				}else{
					$registerary = array("status"=>"success","message"=>"登陆成功","url"=>"vip_detail.php?gweid=".$gweid."#wechat_redirect");
					echo json_encode($registerary);
				}
			}	
		
	}else{//login error
	   $registerary = array("status"=>"error","message"=>"邮箱或密码错误");
		echo json_encode($registerary);
	}
}

if( $_GET['action']=="mobiletheme" ){
	$siteId = $_SESSION['orangeSite']; 
	$isShowPic = getSiteMeta('mobilethemeIsShowPic', $siteId);
	$isShowEditor = getSiteMeta('mobilethemeIsShowEditor', $siteId);
	$isShowVipmember = getSiteMeta('mobilethemeIsShowVipmember', $siteId);
	$isShowVipmember_editor = getSiteMeta('mobilethemeIsShowVipmemberEditor', $siteId);
	$useContact = getSiteMeta('mobilethemeContact', $siteId);
		/**
	*@function:判断会员是否审核
	*/
	$vipauditinfo=web_admin_usechat_info_group($gweid);
	foreach($vipauditinfo as $vaudit){
		$vipaudit=$vaudit->wechat_vipaudit;
	}
	$memberinfo=null;
	$memberinfo_wgroup=null;
	$findweid=false;	
	/*if((!empty($fromuser))&&(!empty($weid))){	
		//20140623 janeen update
		//$memberinfo =  web_admin_member($weid, $fromuser);
		$memberinfo_wgroup =  web_admin_member_wgroup($weid, $fromuser);		
	}else*/ if((!empty($fromuser))&&(!empty($weid))&&(!empty($gweid))){		
				$memberinfo_wgroup =  web_admin_member_wgroup($weid,$gweid,$fromuser);						
	}
	if(!empty($memberinfo_wgroup)){
		foreach($memberinfo_wgroup as $minfo_wgroup){
			$mid=$minfo_wgroup->mid;
		}
		$memberinfo =  web_admin_member_mid_group($mid,$gweid);
		foreach($memberinfo as $minfo){
			$isaudit=$minfo->isaudit;
		}
	}else{
		$memberinfo=null;
	}
	if((empty($memberinfo))&&(!empty($mid))){				
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
				
		$registerary = array("status"=>"success","message"=>"请登录","url"=>bloginfo('template_directory')."/../ReeooV3/wesite/common/vip_login.php?gweid={$gweid}&redirect_url=".urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])."#wechat_redirect");				
		echo json_encode($registerary);
	}else if(($selCheck['wechatvip']==1)&&(($isShowVipmember_editor == 'true')&&((!empty($memberinfo))&&($vipaudit=='1')&&(($isaudit=='2')||($isaudit=='0'))))){
		if($isaudit=='2'){
			$hintmessage="会员身份需要审核";
		}else if($isaudit=='0'){
			$hintmessage="会员申请已被拒绝";
		}
		$registerary = array("status"=>"success","message"=>$hintmessage,"url"=>bloginfo('template_directory')."/../ReeooV3/wesite/common/vip_perdenied.php?gweid={$gweid}&isaudit={$isaudit}&redirect_url=".urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])."#wechat_redirect");				
		echo json_encode($registerary);
	}else{
	
		$blogTitle=$_POST['blogTitle'];
		$blogContent=$_POST['blogContent'];
		$blogAuthor=$_POST['blogAuthor'];
		$postSiteId = $_POST['siteId'];
		global $wpdb;
		
		$wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->prefix}users(user_login,user_nicename,display_name)VALUES (%s,%s,%s)",md5($blogAuthor.time()),$blogAuthor, $blogAuthor));

		$userId =$wpdb->insert_id;
		
		$result=$wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->prefix}posts(post_author,post_date,post_date_gmt,post_content,post_title,post_name,post_content_filtered)VALUES (%s,now(),utc_timestamp(),%s,%s,%s,%s)",$userId,$blogContent,$blogTitle,$blogAuthor,$postSiteId));
		
		
		$registerary = array("status"=>"insertsuc","message"=>"发表成功");				
		echo json_encode($registerary);
		
	
	}
}	

?>