
<?php
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
require_once './wp-content/themes/ReeooV3/wesite/common/dbaccessor.php';

?>

<!--判断填写内容是否为空-->
<script language="javascript">
	function checknull(obj, warning)
	{
	  if (obj.value == "") {
		alert(warning);
		obj.focus();
		return true;
	  }
	return false;
	}

	function validateform()
	{
	  if (checknull(document.content.menu_name, "请输入菜单模板名称!!") == true) {
		return false;
	  }
	  return true; 
	}
</script>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/reset.css" type="text/css" />
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/jquery.Jcrop.css" type="text/css" />
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/test.css" type="text/css" />
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
		<title>添加菜单</title>
	</head>
	<body>
			<div class="main-title">
		<div class="title-1">当前位置：菜单管理 > <font class="fontpurple">菜单模板名称设置 </font>
		</div>
	</div>
		<div id="primary" class="site-content">
			<div id="content" role="main" style="margin: 50px 0 0 80px; width:80%;">
				<form name = "content" action="?admin&page=adminmenu/menu_create&header=0" onSubmit="return validateform()" method="post" > 
					<div class="form-group">
						<label for='pic' style='font-size:16px;margin-bottom:30px;'>菜单模板名称：</label>
						<input type="text" class="form-control" id="menu_name" name="menu_name" value="" style="margin-bottom:30px"/>
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
