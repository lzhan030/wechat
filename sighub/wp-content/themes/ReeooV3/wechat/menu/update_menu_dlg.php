<?php
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
include '../common/wechat_dbaccessor.php';
include 'menu_permission_check.php';
$menuId=$_GET["menuId"];
$ismenuid=$_GET["ismenuid"];//用来标明是menu.php还是menu_invented.php

$menus=wechat_menu_get($menuId);
foreach ($menus as $menu) {
	$menuname=$menu->menu_name;
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js"></script>
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
		<script src="<?php bloginfo('template_directory'); ?>js/jquery.min.js"></script>
		<script src="<?php bloginfo('template_directory'); ?>/js/bootstrap.min.js"></script>
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/test.css" type="text/css" />
		<title>更改菜单名称</title>
	</head>
	<body>
		<div id="primary" class="site-content">
			<div id="content" role="main" style="margin: 50px 0 0 80px; width:80%;">
				<form role="form" name="updatemenu" onSubmit="return validateform()" action="<?php echo constant("CONF_THEME_DIR"); ?>/wechat/menu/menu_up.php?&menuId=<?php echo $menuId;?>" method="post" enctype="multipart/form-data"> 
					<div class="form-group">
						<label for='pic' style='font-size:16px;margin-bottom:30px;'>菜单名称修改为：</label>
						<input id="mename" type="text" class="form-control" name="menuname" value="<?php echo $menuname;?>" style="margin-bottom:30px"/>
						<input id="ismenuid" type="hidden" name="ismenuid" value="<?php echo $ismenuid;?>" />
					</div>
					<div style="margin-top:45px; float:right">
						<input type="submit" class="btn btn-sm btn-primary" style="width:120px" value="更新" />	
						<input type="button" class="btn btn-sm btn-default" style="width:120px" value="取消" onclick="Cancel()" />	
					</div>
				</form>
			</div>
		</div>
	</body>	
	<script language='javascript'>
	function Cancel(){
		window.close();
	}
	function validateform(){
		if (document.getElementById("mename").value == "") {
			alert("菜单名称不能为空");
			return false;
		}
		return true; 
	}
	</script>
</html>