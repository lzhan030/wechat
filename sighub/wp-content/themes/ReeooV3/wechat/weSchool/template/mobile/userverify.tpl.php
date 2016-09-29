<?php defined('IN_IA') or exit('Access Denied');?>
<?php //include $this -> template('header');?>


<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes" />
		
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/we7/style/font-awesome.css" />
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/we7/style/common.css?v=<?php echo TIMESTAMP;?>" />
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/video.css">
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/we7/script/jquery-1.7.2.min.js"></script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/we7/script/bootstrap.js"></script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/we7/script/common.js?v=<?php echo TIMESTAMP;?>"></script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/we7/script/emotions.js"></script>
		
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
		<?php 
		if($flag1)
		{?>
		   <script>
	         //location.href = "videolist.php?GWEID=<?php echo $gweid;?>&fromuser=<?php echo $fromuser;?>";
			 location.href = "<?php echo urldecode($_GET['redirect_url'])?>";
	       </script>
		 <?php
		}
		?>
		<script>
	   function user_ver()
	   {
			if($("#user_vercode").val()==""){
				alert("输入不能为空！");
			}else{
				$("#user_ver").submit();
			}

	   }
		</script>
	</head>
<div>
	<form id="user_ver" action="" method="post">
	
	<!--<div class="main-title">
		<div class="title-1">身份验证
		</div>
	</div>
	<div class="bgimg"></div>-->
	<div class="mobile-div img-rounded">
		<div class="mobile-hd">身份验证</div>
		<div class="mobile-content">
			<table width="95%" height="150" border="0" cellpadding="10px" style="margin-left: 5%; margin-top:15px;" id="table2">
				<tbody>
				<!--2014-07-09newaddbegin-->
					<tr>
						<!--<td><label for="user_email">E-mail: </label></td>-->
						<td><label for="user_type" style="font-size:16px;">登陆身份: </label></td>
						<td width="60%">
							<select name="user_type" class="form-control" size="1" type="text;margin-left:500px;" id="user_type" value="5" maxlength="20" >
								<option value="teacher" selected="selected" >老师</option>
								<option value="parent" >家长</option>
							</select>
						</td>
					</tr>
				<!--2014-07-09newaddend-->
					<tr>
						<td><label for="user_vercode" style="font-size:16px;">验证码: </label></td>
						<td width="60%"><input type="text" value="" class="form-control" id="user_vercode" name="user_vercode"></td>
						<td></td>
					</tr>			
				</tbody>
			</table>
			
			
			<div style="margin-top:3%; margin-left:25%;">
				<input type="button" onclick="user_ver();" class="btn btn-primary" value="确定" id="checkaccount" style="width:70px">
				<!--<input type="button" onclick="location.href='<?php //echo $this -> createMobileUrl('index',array( 'GWEID' => $_GPC['GWEID'] , 'fromuser' => $_GPC['fromuser'] ))?>'" class="btn btn-default" value="取消" id="sub3" style="width:70px; margin-left:20px;">-->
				<input type="button" onclick="location.href='<?php echo $this -> createMobileUrl('index',array( 'gweid' => $_GPC['gweid']))?>'" class="btn btn-default" value="取消" id="sub3" style="width:70px; margin-left:20px;">
			</div>
	    </div>
	</div>
	
	</form>
</div>
</html>
<?php include $this -> template('footer');?>
