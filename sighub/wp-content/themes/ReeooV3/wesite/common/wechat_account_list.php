<?php
session_start();
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
$gweid=$_SESSION['GWEID'];
?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes" />
		
	</head>
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/button.css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/jquery1.83.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/bootstrap-tab.js"></script>
    <script>
	   $(function(){
			$('.nav-tabs a:last').tab('show');
			$('.nav-tabs a:last').click(function (e) {
			}) 
			
		});
		
		function switab(str)
		{
			if(str == "wechat_add")
			{
			     $("#wechatmanage_iframe").attr("src","<?php echo home_url();?>/wp-content/themes/ReeooV3/init/wechat_account.php?beIframe");
			}
			else if(str == "wechat_manage")
			{
			     $("#wechatmanage_iframe").attr("src","<?php echo home_url();?>/wp-content/themes/ReeooV3/wechat/account/wechat_accountinfo.php?beIframe");
			}
		}

	</script>

	<div class="main-titlenew" style="margin-bottom:2%;margin-left:30px;">
		<div class="title-1">当前位置：公众号管理 
		</div>
	</div>
	
	<div style="margin-left:30px;">
		<ul class="nav nav-tabs" id="tabselect">
			<li><a href="#wechat_add" onclick="switab('wechat_add')" data-toggle="tab" >添加公众号</a></li>
			<li class="active selected"><a href="#wechat_manage" onclick="switab('wechat_manage')" data-toggle="tab" >管理公众号</a></li>
		</ul>
	</div>
	
	<div class="tab-content" style="margin-right: 55px;" >
	    <iframe frameborder="0" id="wechatmanage_iframe" src="<?php echo home_url();?>/wp-content/themes/ReeooV3/wechat/account/wechat_accountinfo.php?beIframe" width="100%" height="900" scrolling="no"></iframe>
	</div>	
</html>

<?php   
    get_footer();
 ?>