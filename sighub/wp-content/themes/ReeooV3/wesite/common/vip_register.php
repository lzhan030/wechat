<?php
session_start();
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
include_once 'dbaccessor.php';

global $wpdb;

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

$flag1 = false;
/**
*@function:通过fromuser拿到会员信息
*/
$memberinfo=null;
$findweid=false;
$memberinfo_wgroup=null;
if((!empty($fromuser))&&(!empty($weid))&&(!empty($gweid))){	
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
if((!empty($memberinfo))&&(($vipaudit!='1')||(($isaudit!='2')&&($isaudit!='0')))){
	$flag1 = true;
	if(isset($_GET['redirect_url']) && !empty($_GET['redirect_url']))
		header('Location: '.$_GET['redirect_url']);
	else
		header('Location: '."vip_detail.php?gweid={$gweid}#wechat_redirect");
	exit();
}else if((!empty($memberinfo))&&($vipaudit=='1')&&(($isaudit=='2')||($isaudit=='0'))){
	$flag1 = true;
	header('Location: '."vip_perdenied.php?gweid={$gweid}&isaudit={$isaudit}#wechat_redirect");
	exit();
}
/**
*@function:已经登陆通过mid拿到会员信息
*/
if(empty($memberinfo)&&(!empty($mid))){//通过mid拿到个人信息
	$memberinfo =  web_admin_member_mid_group($mid,$gweid);//20140624 janeen update
	foreach($memberinfo as $minfo){
	   $au_password=$minfo->password;
	   $isaudit=$minfo->isaudit;
	}
	if(($auth== md5($mid.$au_password."weauth3647668"))&&(($vipaudit!='1')||(($isaudit!='2')&&($isaudit!='0')))){
		$flag1 = true;
		if(isset($_GET['redirect_url']) && !empty($_GET['redirect_url']))
			header('Location: '.$_GET['redirect_url']);
		else
			header('Location: '."vip_detail.php?gweid={$gweid}#wechat_redirect");
		exit();
	}else if(($auth == md5($mid.$au_password."weauth3647668"))&&($vipaudit=='1')&&(($isaudit=='2')||($isaudit=='0'))){
		$flag1 = true;
		header('Location: '."vip_perdenied.php?gweid={$gweid}&isaudit={$isaudit}#wechat_redirect");
		exit();
	}		
}
?>

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
			label{width:80px;}
			tbody tr>:first-child{width:110px;}
		</style>			
		<title><?php bloginfo('name'); ?></title>
		
		<script>

			function vipregister(){
				var myreg = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/;					
				if($("#user_email").val()==""){
					alert("请输入邮箱");
				}
				else if(!myreg.test($("#user_email").val())){
					     alert("您的邮箱格式不正确，请重新输入！");
				}
				else if($("#user_password").val()==""){
					alert("请输入密码");
				}
				else if($("#user_password").val()!=$("#user_confirmpassword").val()){
					alert("两次输入的密码不一致");
				}
				else if($("#user_realname").val()==""){
				   alert("真实姓名是必填项");
				}				
				else if($("#user_displayname").val()==""){
				   alert("微信昵称是必填项");
				}
				else if($("#user_mobile").val()==""){
					alert("手机号是必填项");						
				}else{					
				        $("#vipregister").submit();						
				}				
			}
		</script>
		<script type="text/javascript">
		$(function(){
			isSubmitting = false;
			var actionparm="vip_register";
			var ajax_option={			
				url:"vip_common.php?action="+actionparm+"&gweid=<?php echo $gweid;?>",
				success: function(data){
					if (data.status == 'emailrep'){
						alert(data.message);
					}else if(data.status == 'mobilenumberrep'){
						alert(data.message);
					}else if (data.status == 'success'){
						alert(data.message);	
						<?php if(isset($_GET['redirect_url']) && !empty($_GET['redirect_url'])){ ?>	
							location.href="<?php echo $_GET['redirect_url']; ?>";	
						<?php }else{ ?>				
							location.href=data.url;		
						<?php } ?>						
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
			$('#vipregister').submit(function(){
				if(isSubmitting)
				return false;
				isSubmitting = true;
				$(this).ajaxSubmit(ajax_option);
				
				return false;
			});
		});
	</script>	
	</head>
<div class="mobile-div img-rounded" style="padding-bottom:10px">
	<form id="vipregister" action="" method="post">	
			<div class="mobile-hd">会员注册 > <font class="fontpurple">会员信息填写 </font></div>
		<table width="97%" height="150" border="0" cellpadding="10px" style="margin-left: 3%; margin-top:15px;" id="table2">
			<tbody>
				<tr>
					<td><label for="user_email"><font color="red">*</font>邮箱: </label></td>
					<td><input type="text" value="<?php echo $email; ?>" class="form-control" id="user_email" name="user_email"></td>
					<td><span id="user_emailchk"></span></td>
				</tr>
				<tr>
					<td><label for="user_password"><font color="red">*</font>密码: </label></td>
					<td><input type="password" value="" class="form-control" id="user_password" name="user_password"></td>
				</tr>
				<tr>
					<td><label for="user_confirmpassword"><font color="red">*</font>确认密码: </label></td>
					<td><input type="password" value="" class="form-control" id="user_confirmpassword" name="user_confirmpassword"></td>
				</tr>			
				<tr>
					<td><label for="user_realname"><font color="red">*</font>真实姓名: </label></td>
					<td width="65%"><input type="text" value="<?php echo $realname; ?>" class="form-control" id="user_realname" name="user_realname" ></td>
					<td></td>
				</tr>
				<tr>
					<td><label for="user_nikename"><font color="red">*</font>微信昵称: </label></td>
					<td width="65%"><input type="text" value="<?php echo $nickname; ?>" class="form-control" id="user_displayname" name="user_displayname"></td>
					<td><span id="user_displaynamechk"></span></td>
				</tr>				
				<tr>
					<td><label for="user_mobile"><font color="red">*</font>手机号: </label></td>
					<td><input type="text" value="<?php echo $mobilenumber; ?>" class="form-control" id="user_mobile" name="user_mobile"></td>
				</tr> 				
			</tbody>
		</table>	
		<div style="margin-top:3%;margin-left:24%;margin-right:10%;">
			<input type="button" onclick="vipregister();" class="btn btn-primary" value="申请" id="checkaccount" style="width:70px">
			<input type="button" onclick="javascript:history.back(-1);" class="btn btn-default" value="取消" id="sub3" style="width:70px; margin-left:20px;">
		</div>
		<div style="margin-top:2%;margin-right:10%;float:right">
			<a href="vip_login.php?gweid=<?php echo $gweid;?>#wechat_redirect">会员登录</a>
		</div>
		
		<?php
			$wechat_group=getWechatGroupInfo_gweid($gweid);
			foreach($wechat_group as $wgroup){
				$ISVWEID=$wgroup->WEID;
				$user_id=$wgroup->user_id;
				$shared_flag=$wgroup->shared_flag;
			}	
		?>
				
		<div class="alert alert-info" style="margin-top:3%; margin-left:8%;margin-right:8%;clear:both;<?php if($shared_flag!=2) echo " display:none"; ?>" align="center">
		<p style="font-size:14px;" align="left">如果您已经是以下微信公众号的会员，您可以直接用该会员账号登陆，无须再申请新会员:<br>
		<?php   
		$gweids=getWechatGroupInfo_gweid_activeshared($user_id);//所有共享号
		$first = true;
		foreach($gweids as $gweidinfo){
			$gweidzero=$gweidinfo->GWEID;//共享号的gweid
			$usechatinfo=web_admin_usechat_allinfo_group($gweidzero);//共享号所使用的微信号
			foreach($usechatinfo as $nikename){
				$wechatname=$nikename->wechat_nikename;					
				if($first){
					echo $wechatname; 
					$first = false; 					
				}else{
					echo ", " .$wechatname ;
				}
			}
		}
		?>
		</p>
	</div>	
	</form>
</div>
<?php 
$viptitle='会员注册';
include 'vip_footer.php';
?>

