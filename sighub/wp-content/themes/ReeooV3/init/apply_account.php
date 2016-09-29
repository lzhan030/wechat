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

	//get account number	
	$useraccount = get_user_meta($userid, "useraccount", true);
	if(empty($useraccount)) {
		$useraccount = 0; 
	}	
	
	//insert new application data	
	if( isset($_POST['user_account'])){
		$user_account = $_POST['user_account'];
		$user_description = $_POST['user_description'];
		web_user_set_accountapply($userid, $user_account, $user_description);
	}
?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css" />
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
		<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js"></script>
		<script src="<?php bloginfo('template_directory'); ?>/js/bootstrap.min.js"></script>
		<title>可建立公众号数目申请</title>
	
		<script>
			<?php if( isset($_POST['user_account'])){ 
			?>
				alert("您的申请正在审核中，我们的管理员会尽快与您联系，谢谢！");               
				window.close();	
			<?php }?>
			
			function applyok(){
			    document.getElementById('accountapply').submit();	
		    }
			function Cancle(){
				window.opener=null;
				setTimeout("self.close()",0);
	        }
		</script>
	</head>
	<body>
		<div>
			<form id="accountapply" action="" method="post">
			<table width="500" border="0" cellpadding="10px" style="margin-left: 5%; margin-top:40px; font-size:14px;" id="table2">
				<tbody>
					<tr>
						<td>请选择可建立公众号数目:</td>
						<td>
							<input type="text" name="user_account" id="user_account" class="form-control" value="<?php echo $useraccount; ?>" autofocus="autofocus" />
						</td>
					</tr>
					<tr>
						<td>申请理由: </td>
						<td width="300"><input type="text" value="" class="form-control" id="user_description" name="user_description"></td>
						<td></td>
					</tr>
					
				</tbody>
			</table>
			<div style="margin-top:30px; margin-left:35%;">
				<input type="button" onclick="applyok()" class="btn btn-primary" value="申请" id="checkaccount" style="width:70px">
				<input type="button" class="btn btn-default" value="取消" id="sub3" onclick="Cancle()" style="width:70px; margin-left:20px;">
			</div>
			</form>
		</div>
</body>
</html>
