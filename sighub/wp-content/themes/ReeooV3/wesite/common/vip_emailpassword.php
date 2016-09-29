<?php
session_start();
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');

include_once 'dbaccessor.php';
require 'class.phpmailer.php';


$gweid =  $_GET['gweid'];
$weid =  $_SESSION['weid'][intval($gweid)];
/**
*@function:封装gweid
*/
if(!empty($siteId)){
	$site=web_admin_get_site($siteId);
	foreach($site as $siteinfo){
		$userid=$siteinfo->site_user;
		$gweid=$siteinfo->GWEID;
	}
	//20150421 sara new added
	//根据当前的gweid去查找有没有处在共享虚拟号下，如果是虚拟号下的，需要将gweid换为虚拟号的gweid
	$gweid = virtualgweid_open($gweid);
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
			
		</style>			
		<title><?php bloginfo('name'); ?></title>
	<script>
	   function vip_repassword(){				
			if($("#user_email").val()==""){
					alert("请输入邮箱");
			}else if($("#user_email").val()!=""){
				var myreg = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/;
					
				if(!myreg.test($("#user_email").val())){
					alert("您的邮箱格式不正确，请重新输入！");
				}else{
				        $("#vip_repassword").submit();
				}
			}
	   }
	</script>
	<script type="text/javascript">
		$(function(){
			isSubmitting = false;
			var actionparm="vip_repassword";
			var ajax_option={			
				url:"vip_common.php?action="+actionparm+"&gweid=<?php echo $gweid;?>",
				success: function(data){
					if (data.status == 'nouser'){
						alert(data.message);
					}else if (data.status == 'success'){
						alert(data.message);						
						location.href=data.url;						
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
			$('#vip_repassword').submit(function(){
				if(isSubmitting)
				return false;
				isSubmitting = true;
				$(this).ajaxSubmit(ajax_option);
				
				return false;
			});
		});
	</script>	
	</head>
<div class="mobile-div img-rounded" style="padding-bottom:32px">
	<form id="vip_repassword" action="" method="post">		
		<div class="mobile-hd"><font class="fontpurple">找回密码：</font></div>
	<table width="95%" height="140" border="0" cellpadding="10px" style="margin-left: 5%; margin-top:15px;" id="table2">
		<tbody>
			<tr>
				<td><label for="user_email">请填写您需要找回的邮箱帐号: </label></td>				
			</tr>
			<tr>			
				<td><input type="text" value="<?php echo $email?>" class="form-control" id="user_email" name="user_email"></td>	
			</tr>		
		</tbody>
	</table>	
	<div style="margin-top:3%; margin-left:20%;">
	    <input type="button" onclick="vip_repassword();" class="btn btn-primary" value="发送" id="checkaccount" style="width:30%">
		<input type="button" onclick="javascript:history.back(-1);" class="btn btn-default" value="取消" id="sub3" style="width:30%; margin-left:3%;">			
	</div>
	</form>
</div>
<?php 
$viptitle='会员找回密码';
include 'vip_footer.php';
?>
