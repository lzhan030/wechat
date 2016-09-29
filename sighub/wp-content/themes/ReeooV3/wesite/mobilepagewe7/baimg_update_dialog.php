<?php
	$path = explode ( 'wp-content', __FILE__ );
	$wp_root_path = $path [0];
	require_once ($wp_root_path . '/wp-load.php');
	include '../common/dbaccessor.php';
	include '../common/web_constant.php';
	$siteId=$_GET["siteId"];//拿到window.open里传递过来的值	
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
	  if (checknull(document.uploadimg.file, "请先上传背景图片!") == true) {
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
		<link rel="stylesheet" href="../../css/bootstrap.min.css">
		<script src="../../js/jquery.min.js"></script>
		<script src="../../js/bootstrap.min.js"></script>
		<title>上传背景图片</title>
	</head>
	<body>
		<div id="primary" class="site-content">
			<div id="content" role="main" style="margin: 50px 0 0 30px; width:80%;">
				<form role="form" name="uploadimg" onSubmit="return validateform()" action="<?php echo constant("CONF_THEME_DIR"); ?>/wesite/mobilepagewe7/baimg_update.php?beIframe&siteId=<?php echo $siteId?>" method="post" enctype="multipart/form-data"> 
					<div class="form-group">
						<label for='pic' style='font-size:16px;margin-bottom:30px;'>请上传新背景图片：</label>
						（建议上传图片大小为575*956）
						<input type='file' class='form-control' name='file' id='file' style='margin-bottom:30px' />					
					</div>
					<input type="submit" class="btn btn-sm btn-primary" style="width:120px" value="更新" />					
				</form>
			</div>
		</div>
	</body>	
</html>