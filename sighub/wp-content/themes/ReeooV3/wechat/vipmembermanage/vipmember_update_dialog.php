<?php
	$tmp_path = explode ( 'ReeooV3', __FILE__ );
	$template_path=$tmp_path[0];
	require_once $template_path.'ReeooV3/wechat/common/session.php';

	$path = explode ( 'wp-content', __FILE__ );
	$wp_root_path = $path [0];
	require_once ($wp_root_path . '/wp-load.php');
	global  $current_user;
	if( !isset($current_user->user_login)|| empty($current_user->user_login)){
			wp_redirect(wp_login_url());
	}	
	get_header();

	include '../common/wechat_dbaccessor.php';
	include '../../wesite/common/dbaccessor.php';
	include '../common/wechat_constant.php';
	//判断是否是分组管理员的公众号,分组管理员不需要进行此功能的check
	$groupadmincount = is_superadmin($_SESSION['GWEID']);
	if($groupadmincount == 0)
		include 'vmember_permission_check.php';
	/**
	*@function:判断会员是否审批
	*/
	$vipauditinfo=web_admin_usechat_info_group($_SESSION['GWEID']);

	foreach($vipauditinfo as $vaudit){
		$vipaudit = $vaudit->wechat_vipaudit;
	}
 	$vipmemberId = $_GET["vipmemberId"];//获取会员的id
	
	$vmember = web_admin_get_vipmember($vipmemberId);
	$vipopenids = web_admin_get_memberopenid($vipmemberId); 
	foreach($vmember as $vmemberinfo){
		$vipmemberUser= $vmemberinfo->from_user;
		$realName=$vmemberinfo->realname;
		$nickName=$vmemberinfo->nickname;
		$point=$vmemberinfo->point;
		$point_cost=$vmemberinfo->point_cost;//point update
		$level=$vmemberinfo->level;
		$rtime=$vmemberinfo->rtime;
		$mobilenumber=$vmemberinfo->mobilenumber;
		$email=$vmemberinfo->email;
		$billingplan=$vmemberinfo->billing_plan;
		$regtype=$vmemberinfo->reg_type;
		$apptype=$vmemberinfo->app_type;
		$isaudit=$vmemberinfo->isaudit;
	}
	$refreshOpener=$_GET["refreshOpener"];
?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery-1.10.2.min.js"></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.form.js" type="text/javascript" ></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/bootstrap.min.js"></script>
	<title>更新会员信息</title>
</head>

<div class="main_auto">
	<div class="main-title">
		<div class="title-1">当前位置：会员管理 ><font class="fontpurple">会员更新</font></div>
	</div>
	<div class="bgimg"></div>
	<div style="width:690px;margin-left:90px;margin-top:30px;" >
		<form name ="vipsetting" id="vipsetting" name ="content" action="<?php echo constant("CONF_THEME_DIR"); ?>/wechat/vipmembermanage/vipmember_update.php?&beIframe&vipmemberId=<?php echo $vipmemberId?>&refreshOpener=<?php echo $refreshOpener ?>" method="post" enctype="multipart/form-data">
			<table class="gridtable" width="400" height="400" border="0" cellpadding="5px" align="center" style="margin-top:20px;">
				<tr>
					<td><label>会员编号: </label></td>
					<td><?php echo $vipmemberId?></td>
				</tr>
				<tr>
					<td><label for="realname">真实姓名: </label></td>
					<td><input type="text" id="real_Name" class="form-control" name="real_Name" value=<?php echo $realName?> > </td>
				</tr>
				<tr>
					<td><label for="nickname">微信昵称: </label></td>		
					<td><input type="text" id="nick_Name" class="form-control" name="nick_Name" value=<?php echo $nickName?> > </td>			
				</tr>
				<tr>
					<td><label for="isaudit">会员审批状态: </label></td>		
					<td>
					<?php if($vipaudit!='0'){?>
						<input type="radio" name="isaudit"  value="1" style="vertical-align:middle;  margin-bottom:5px;margin-left:22px;margin-right:2px;" <?php if($isaudit=='1') echo 'checked' ?>>审批通过</input>
						<input type="radio" name="isaudit"   value="2" style="vertical-align:middle;  margin-bottom:5px;margin-left:10px;margin-right:2px;" <?php if($isaudit=='2') echo 'checked' ?>>审批中</input>
						<input type="radio" name="isaudit"   value="0" style="vertical-align:middle;  margin-bottom:5px;margin-left:11px;margin-right:2px;" <?php if($isaudit=='0') echo 'checked' ?>>拒绝</input>				
					<?php }else{?>
						<input type="text"  class="form-control" readonly="true" value="审批通过" > </td>
					<?php }?>					
					</td>
				</tr>
				<tr>	
					<td><label>微信Openid:</label></td>
					<td>
						<?php if(!empty($vipopenids)) {
							foreach($vipopenids as $openid) {
								echo $openid->from_user;
								echo '<br/>';
							}
						}?>
					</td>
				</tr>
				<tr>
					<td><label for="point">会员现有积分: </label></td>	
					<td><input type="text" id="point" class="form-control" name="point" value=<?php echo $point?> > </td>
				</tr>
				<tr><!--point update-->
					<td><label for="point">会员花费积分: </label></td>	
					<td><?php echo intval($point_cost); ?></td>
				</tr>
				<tr>
					<td><label for="level">会员级别: </label></td>		
					<td><input type="text" id="level" class="form-control" name="level" value=<?php echo $level?> > </td>
				</tr>
				<tr>
					<td><label for="rtime">注册时间: </label></td>	
					<td><input type="text" id="rtime" class="form-control" name="rtime" value=<?php echo $rtime?> > </td>
				</tr>
				<tr>
					<td><label for="mobnumber">手机号码: </label>	</td>	
					<td><input type="text" id="mobilenumber" class="form-control" name="mobilenumber" value=<?php echo $mobilenumber?> > </td>
				</tr>
					<tr>
					<td><label for="email">邮箱地址: </label>	</td>	
					<td><input type="text" id="email" class="form-control" name="email" value=<?php echo $email?> > </td>
				</tr>
				<tr>
					<td><label for="billingplan">计费计划: </label></td>		
					<td><input type="text" id="billing_plan" class="form-control" name="billing_plan" value=<?php echo $billingplan?> > </td>
				</tr>
				<tr>
					<td><label for="regtype">注册方式: </label></td>		
					<td><input type="text" id="reg_type" class="form-control" name="reg_type" value=<?php echo $regtype?> ></td>
				</tr>
				<tr>
					<td><label for="apptyoe">使用方式: </label></td>		
					<td><input type="text" id="app_type" class="form-control" name="app_type" value=<?php echo $apptype?>></td>
				</tr>
			</table>
			<div width="150" align="center">
				<input type="button" class="btn btn-primary" value="确定" onclick="javascript:submitform()" style="width:120px;margin-top:25px"/>
				<input type="button" class="btn btn-default" value="返回" onclick="location.href='<?php bloginfo('template_directory'); ?>/wechat/vipmembermanage/vipmember_list.php?beIframe'" style="width:120px;margin-top:25px"/>
			</div>
		</form>
	</div>
</div>
</html>
<script type="text/javascript">
function submitform(){
   
	$("#vipsetting").ajaxSubmit({
		//定义返回JSON数据，还包括xml和script格式                
		dataType:'json',               
		beforeSend: function() {                   
		//表单提交前做表单验证               
		},               
		success: function(data) {  
			//提交成功后调用 
			alert(data.message);   
			if(data.status=='success'){
				window.location.href = data.url;
			}
		}          
	}); 
	return true;
		
}
</script>