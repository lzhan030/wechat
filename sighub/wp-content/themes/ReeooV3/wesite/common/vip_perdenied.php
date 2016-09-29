<?php
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
/**
*@function: get
*/

$gweid =  $_GET['gweid'];
$mid =$_SESSION['gmid'][intval($gweid)]['mid'];
$auth =	$_SESSION['gmid'][intval($gweid)]['auth'];
$isaudit = $_GET['isaudit'];
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
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/we7/script/bootstrap.js"></script>
	<div class="mobile-div img-rounded" style="padding-bottom:23px">	
		<div class="alert alert-warning" style="padding:5%;margin-top:40%; margin-left:8%;margin-right:8%;" align="center">	    		
			<p style="font-size:16px;" align="center">
				<?php if($isaudit=='0'){?>您的会员申请已经被拒绝<?php }else{?>您的会员权限正在审批中... <?php }?>
			</p>
		</div>
	</div>
<?php 
$viptitle='会员审核';
include 'vip_footer.php';
?>