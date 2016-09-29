<?php
session_start();
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');

include_once 'dbaccessor.php';

/**
*@function: get
*/
$gweid =  $_GET['gweid'];
$weid =  $_SESSION['weid'][intval($gweid)];
$mid =$_SESSION['gmid'][intval($gweid)]['mid'];
$auth =	$_SESSION['gmid'][intval($gweid)]['auth'];
$result = web_user_display_index_groupnew_wesforsel($gweid);
foreach($result as $initfunc){
	if($selCheck[$initfunc->func_name] == 0)
		$selCheck[$initfunc->func_name] = $initfunc->status;
}
if($selCheck['wechatvip']!=1){
	header('Location: '."perdenied.php?gweid={$gweid}#wechat_redirect");
	exit();
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
//$gweid如果是虚拟的，则不执行，否则$gweidtrue=$gweid;
$getvirturals = $wpdb->get_results("SELECT count(*) as gcount FROM {$wpdb->prefix}wechat_group where WEID = 0 AND GWEID=".$gweid,ARRAY_A);

foreach ($getvirturals as $getvirtural) {
	$gcount = $getvirtural['gcount'];
}

if($gcount == 0){
	$gweidtrue = $gweid;
	if((isset($_SERVER['HTTP_USER_AGENT']))&&(strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false)){
		/*认证服务号则通过oauth2.0获取fromuser*/
		require_once 'common_oauth.php';
		
	}
}

/**
*@function:通过fromuser拿到会员信息
*/
$memberinfo=null;
$findweid=false;
$memberinfo_wgroup=null;
if((!empty($fromuser))&&(!empty($weid))&&(!empty($gweid))){//一个商家同一个共用号添加了两次，weid不一样，但数据库只能放其中一个了，放到其中一个肯定会查到记录便不会再插入了			
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

/**
*@function:已经登陆通过mid拿到会员信息
*/
if((empty($memberinfo))&&(!empty($mid))){//通过mid拿到个人信息
	$memberinfo =  web_admin_member_mid_group($mid,$gweid);//20140624 janeen update
	foreach($memberinfo as $minfo){
	   $au_password=$minfo->password;
	   $isaudit=$minfo->isaudit;
	}
	if($auth!= md5($mid.$au_password."weauth3647668")){
		$memberinfo=null;
		unset($_SESSION['gmid'][intval($gweid)]);
	}	
}

if(!empty($memberinfo))	{
	foreach($memberinfo as $member){
		$realname = $member->realname;
		$nickname = $member->nickname;
		$point = $member->point;
		$level = $member->level;
		$rtime = $member->rtime;
		$mobilenumber = $member->mobilenumber;
		$email = $member->email;
		$password=$member->password;
		$billingplan = $member->billing_plan;
		$regtype = $member->reg_type;
		$isaudit=$member->isaudit;
		$memid=$member->mid;
		//$_SESSION['mid'] = $memid;
		//$mid = $_SESSION['mid'];
		$auth=md5($memid.$password."weauth3647668");
		$_SESSION['gmid'][intval($gweid)] = array('mid'=>$memid ,'auth'=>$auth);
		$mid =$_SESSION['gmid'][intval($gweid)]['mid'];
	}
}	
global $wpdb;

if(empty($memberinfo)){
	header('Location: '."vip_login.php?gweid={$gweid}&redirect_url=".urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])."#wechat_redirect");
	exit();

}else if((!empty($memberinfo))&&($vipaudit=='1')&&(($isaudit=='2')||($isaudit=='0'))){
	header('Location: '."vip_perdenied.php?gweid={$gweid}&isaudit={$isaudit}&redirect_url=".urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])."#wechat_redirect");
	exit();

}else{ ?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes" />
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css" />
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/we7/style/font-awesome.css" />
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/we7/style/common.css?v=<?php echo TIMESTAMP;?>" />
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/video.css">
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/we7/script/jquery-1.7.2.min.js"></script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/we7/script/bootstrap.js"></script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/we7/script/emotions.js"></script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/we7/script/jquery.form.js"></script>
		<style type="text/css">
			a:visited {
				color: #FF00FF
			}
			body {
			    background: #ECECEC;
				font: 16px/1.5 "Microsoft Yahei","微软雅黑",Tahoma,Arial,Helvetica,STHeiti;
			}
			label{width:70px;}
		</style>
		<title><?php bloginfo('name'); ?></title>
	<script>
	    function memberedit(){	        
		    var myreg = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/;
			if($("#user_vemail").val()==""){
				alert("Email是必填项");
			}
			else if(!myreg.test($("#user_vemail").val())){
				alert("您的邮箱格式不正确，请重新输入！");
			}
			else if($("#user_password").val()!=$("#user_confirmpassword").val()){
				alert("两次输入的密码不一致");
			}
			else if($("#user_realname").val()==""){
			    alert("真实姓名是必填项");
			}
			else if($("#user_nickname").val()==""){
			    alert("昵称是必填项");
			}
			else if($("#user_mobile").val()==""){
				alert("手机号是必填项");					
			}else{
				$("#memberedit").submit();
			}		
	    }
				
		</script>
		<script type="text/javascript">
		$(function(){
			isSubmitting = false;
			var actionparm="vip_detail";
			var ajax_option={			
				url:"vip_common.php?action="+actionparm+"&gweid=<?php echo $gweid;?>",
				success: function(data){
					if (data.status == 'emailrep'){
						alert(data.message);
					}else if(data.status == 'mobilenumberrep'){
						alert(data.message);
					}else if (data.status == 'success'){
						alert(data.message);						
						//location.href=data.url;
						location.reload();						
					}else if (data.status == 'error'){
						alert(data.message);
					}else{
						alert("出现错误");
					}
					isSubmitting = false;
				},
		        error: function(data){
					alert("出现错误");
					isSubmitting = false;
				},
				dataType: 'json'
			}
			$('#memberedit').submit(function(){
				if(isSubmitting)
				return false;
				isSubmitting = true;
				$(this).ajaxSubmit(ajax_option);
				
				return false;
			});
		});
	</script>	
	</head>
<div class="mobile-div img-rounded">
	<form id="memberedit" action="" method="post">
	<div class="mobile-hd"><font class="fontpurple">我的会员信息</font></div>
	<table width="95%" height="150" border="0" cellpadding="10px" style="margin-left: 5%; margin-top:15px;" id="table2">
		<tbody>
			<tr>
				<td><label for="user_vemail">Email: </label></td>
				<td><input type="text" value="<?php echo $email; ?>" class="form-control" id="user_vemail" name="user_vemail"></td>
			</tr>			
			<tr>
				<td><label for="user_password">密码: </label></td>
				<td><input type="password" placeholder="●●●●●●" value="" class="form-control" id="user_password" name="user_password"></td>
			</tr>
			<tr>
				<td><label for="user_confirmpassword">确认密码: </label></td>
				<td><input type="password" placeholder="●●●●●●" value="" class="form-control" id="user_confirmpassword" name="user_confirmpassword"></td>
			</tr>			
			<tr>
				<td><label for="user_realname">真实姓名: </label></td>
				<td width="65%"><input type="text" value="<?php echo $realname; ?>" class="form-control" id="user_realname" name="user_realname" ></td>
				<td></td>
			</tr>
			<tr>
				<td><label for="user_nikename">微信昵称: </label></td>
				<td width="65%"><input type="text" value="<?php echo $nickname; ?>" class="form-control" id="user_nickname" name="user_nickname"></td>
				<td><span id="user_nicknamechk"></span></td>
			</tr>
			<tr>
				<td><label for="user_mobile">手机号: </label></td>
				<td><input type="text" value="<?php echo $mobilenumber; ?>" class="form-control" id="user_mobile" name="user_mobile"></td>
			</tr>
			<tr>
				<td><label for="user_point">积分: </label></td>
				<td><input type="text" value="<?php echo $point; ?>" class="form-control" id="user_point" name="user_point" readonly="true"></td>
			</tr>			
			<tr>
				<td><label for="user_level">等级: </label></td>
				<td><input type="text" value="<?php echo $level; ?>" class="form-control" id="user_level" name="user_level" readonly="true"></td>
			</tr>
			<tr>
				<td><label for="user_rtime">注册时间: </label></td>
				<td><input type="text" value="<?php echo $rtime; ?>" class="form-control" id="user_rtime" name="user_rtime" readonly="true"></td>
			</tr>						
		</tbody>
	</table>	
	
	<div style="margin-top:3%; margin-bottom:3%; margin-left:35%;">
	    <input type="button" class="btn btn-primary" onclick="memberedit();" value="更新" id="checkaccount" style="width:70px">
	</div>
	</form>
</div>
</html>
<?php
 
$viptitle='会员详情';
include 'vip_footer.php';


}  ?>
