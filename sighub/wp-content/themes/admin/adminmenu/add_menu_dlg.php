<?php
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
include './wp-content/themes/ReeooV3/wechat/common/wechat_dbaccessor.php';
$menuId=$_GET["menuId"];//拿到window.open里传递过来的值
$parId=$_GET["parid"];
$M_id=$_GET["M_id"];

?>

<!--判断填写内容是否为空-->
<script language="javascript">
		function validateform()
	{
	  if (document.getElementById("mename").value == "") {
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
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/test.css" type="text/css" />
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
		<title>添加菜单</title>
	</head>
	<body>
		<div id="primary" class="site-content">
			<div id="content" role="main" style="margin: 50px 0 0 80px; width:80%;">
				<form role="form" name="addmenu" onSubmit="return validateform()" action="?admin&page=adminmenu/menu_add&header=0&parId=<?php echo $menuId;?>&M_id=<?php echo $M_id ?>" method="post" enctype="multipart/form-data"> 
					<div class="form-group">
						<label for='pic' style='font-size:16px;margin-bottom:30px;'>菜单名称：</label>
						<input id="mename" type="text" class="form-control" name="menuname" value="" style="margin-bottom:30px"/>
					</div>
					<div style="margin-top:45px; float:right">
						<input type="submit" class="btn btn-sm btn-primary" style="width:120px" value="添加" />	
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