<?php
$tmp_path = explode ( 'ReeooV3', __FILE__ );
$template_path=$tmp_path[0];
require_once $template_path.'ReeooV3/wechat/common/session.php';

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
require_once '../wesite/common/dbaccessor.php';
require_once 'account_permission_check.php';
global $wpdb;
$userid = $_GET['userid'];
$gweid = $_SESSION['GWEID'];

//echo $userid;
//echo $gweid;

if( isset($_POST['user_space'])){

    $user_space = $_POST['user_space'];
	$user_description = $_POST['user_description'];
     
	
	//将申请扩容添加到数据表中
	web_user_addspaceapply($userid, $user_space, $user_description);

	
}

?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css" />
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
		<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js"></script>
		<script src="<?php bloginfo('template_directory'); ?>/js/bootstrap.min.js"></script>
		<title>扩容申请</title>
	
		<script>
			<?php if( isset($_POST['user_space'])){ 
			?>
				alert("请等待管理员审核，谢谢");               
				window.close();	
			<?php }?>
			
			function applyok(){
			    document.getElementById('spaceapply').submit();	
		    }
			function Cancle(){
			
				window.opener=null;
				setTimeout("self.close()",0);
	        }
		
			
		</script>
	</head>
<div>
	<form id="spaceapply" action="" method="post">
	
	<div class="main-title">
		<div class="title-1">当前位置：扩容申请 > <font class="fontpurple">扩容申请信息 </font>
		</div>
	</div>
	<div class="bgimg"></div>
	
	<table width="450" height="150" border="0" cellpadding="10px" style="margin-left: 5%; margin-top:15px;" id="table2">
		<tbody>
			<tr>
				
				<td>空间大小选择:</td>
				<td>
				    <select name="user_space" class="form-control" size="1" type="text;margin-left:500px;" id="user_space" value="5" maxlength="20">
						<option value="700.00" selected="selected">700M</option>
						<option value="1024.00" >1G</option>
						<option value="2048.00">2G</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>申请理由: </td>
				<td width="300"><input type="text" value="" class="form-control" id="user_description" name="user_description"></td>
				<td></td>
			</tr>
			
		</tbody>
	</table>
	
	
	<div style="margin-top:3%; margin-left:35%;">
	    <input type="button" onclick="applyok()" class="btn btn-primary" value="申请" id="checkaccount" style="width:70px">
		<input type="button" class="btn btn-default" value="取消" id="sub3" onclick="Cancle()" style="width:70px; margin-left:20px;">
	</div>
	</form>
</div>
</html>
