<?php
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');

include './wp-content/themes/ReeooV3/wechat/common/wechat_dbaccessor.php';
$menuId=$_GET["menuId"];//拿到window.open里传递过来的值
$M_id=$_GET["M_id"];

$menus=wechat_public_menu_get($menuId);
foreach ($menus as $menu) {
	$menuname=$menu->menu_name;
}
?>

<!--判断填写内容是否为空-->
<script language="javascript">
	function validateform()
	{
	  if (document.getElementById("menuname").value == "") {
		alert("菜单名称不能为空");
		return false;
	  }
	  return true; 
	}
</script>

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
				<form role="form" name="updatemenu" onSubmit="return validateform()" action="?admin&page=adminmenu/menu_up&header=0&M_id=<?php echo $M_id;?>&menuId=<?php echo $menuId;?>" method="post" enctype="multipart/form-data"> 
					<div class="form-group">
						<label for='pic' style='font-size:16px;margin-bottom:30px;'>菜单名称修改为：</label>
						<input type="text" class="form-control" id="menuname" name="menuname" value="<?php echo $menuname;?>" style="margin-bottom:30px"/>
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
	</script>
</html>


