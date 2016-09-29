<?php defined('IN_IA') or exit('Access Denied');?>

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
		<script type="text/javascript">
		    function vipreg(){				
				if($("#user_phone").val()==""){
					alert("请输入手机号");
				}
				else if($("#user_phone").val()!=$("#user_phone_confirm").val()){
					alert("两次输入的手机号不一致");
				}else{
					$("#vipreg").submit();
				}
		    }
		   
			$(function(){
				isSubmitting = false;
				var actionparm="vipreg";
				var ajax_option={
					url:window.location.href,
					success: function(data){
						if (data.status == 'success'){
							alert(data.message);						
							//location.href=data.url;
							window.location.reload();
						}else if (data.status == 'error'){
							alert(data.message);
						}
						isSubmitting = false;
					},
			        error: function(data){
						isSubmitting = false;
					},
					dataType: 'json'
				}
				$('#vipreg').submit(function(){
					if(isSubmitting)
					return false;
					isSubmitting = true;
					$(this).ajaxSubmit(ajax_option);
					return false;
				});
			});
		</script>	
	</head>
	<div class="mobile-div img-rounded" style="padding-bottom:23px">
		<form id="vipreg" action="" method="post">		
			<div class="mobile-hd"><font class="fontpurple">填写手机号注册</font></div>
			<table width="95%" height="150" border="0" cellpadding="10px" style="margin-left: 5%; margin-top:15px;" id="table2">
				<tbody>
					<tr>
						<td><label for="user_phone">手机号: </label></td>
						<td><input type="text" value="" class="form-control" id="user_phone" name="user_phone"></td>
					</tr>
					<tr>
						<td><label for="user_phone_confirm">确认手机号: </label></td>
						<td><input type="text" value="" class="form-control" id="user_phone_confirm" name="user_phone_confirm"></td>
					</tr>			
				</tbody>
			</table>	
			<div style="margin-top:3%; margin-left:24%;">
				<input type="button" onclick="vipreg();" class="btn btn-primary" value="确定" id="checkaccount" style="width:30%">
				<input type="button" onclick="javascript:history.back(-1);" class="btn btn-default" value="取消" id="sub3" style="width:30%; margin-left:3%;">		
			</div>
		</form>
	</div>

<?php 
$viptitle='注册';
include $this -> template('footer');
?>
