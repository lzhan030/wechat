<?php
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
include '../common/wechat_dbaccessor.php';
include 'keyword_permission_check.php';
$keywordId=$_GET["keywordId"];//拿到window.open里传递过来的值
$keyword=wechat_keyword_get($keywordId);
foreach ($keyword as $key) {
	$keycontent=$key->arply_keyword;
}
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
	  if (checknull(document.updatekey.key, "请先输入关键词!") == true) {
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
		<title>更改关键词</title>
	</head>
	<body>
		<div id="primary" class="site-content">
			<div id="content" role="main" style="margin: 50px 0 0 80px; width:80%;">
				<form role="form" name="updatekey" onSubmit="return validateform()" action="<?php echo constant("CONF_THEME_DIR"); ?>/wechat/keyword/update_keyword.php?&keywordId=<?php echo $keywordId;?>" method="post" enctype="multipart/form-data"> 
					<div class="form-group">
						<label for='pic' style='font-size:16px;margin-bottom:30px;'>关键词修改为：</label>
						<input type="text" class="form-control" name="key" value="<?php echo $keycontent;?>" style="margin-bottom:30px"/>
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