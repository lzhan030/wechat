<?php
 
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
include '../common/web_constant.php';
$siteId=$_GET["siteId"];
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js"></script>
		<title>无标题文档</title>
	</head>
	<body onunload="closeit()">
		<div id="primary" class="site-content">
			<div id="content" role="main">
				<form action="<?php echo constant("CONF_THEME_DIR");  ?>/wesite/mobilepagev2/gallery_insert.php?beIframe&siteId=<?php echo $siteId ?>" method="post" enctype="multipart/form-data"> 		
					<label for="pic">图片</label>
					<input type="file" name="file" id="file" />
					<div width="150">
						<input type="submit" value="添加" />	
					</div>	
				</form>
			</div>
		</div>
	</body>
</html>

<script language='javascript'>
	function closeit() {
		top.resizeTo(300, 200); //控制网页显示的大小		
		setTimeout("self.close()", 5000); //毫秒
		opener.location.reload();  //主页面刷新显示
	}
</script>