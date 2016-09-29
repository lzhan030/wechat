<?php

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');

include '../common/dbaccessor.php';
include '../common/web_constant.php';
	
//拿到window.open里传递过来的值	
//$siteId=$_GET["siteId"];	
$Id=$_GET["Id"];
$site_Id=web_admin_get_site_id3($Id);

$slider=web_admin_get_slider_title($Id);

	
?>



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
				<form role="form" action="<?php echo constant("CONF_THEME_DIR"); ?>/wesite/mobilepagev3/baimg_update.php?beIframe&siteId=<?php echo $siteId ?>" method="post" enctype="multipart/form-data"> 
					<div class="form-group">
					<table width="600" bordercolor="#06c" border="0" align="center" cellpadding="10" cellspacing="0" style="margin-top:25px;">
					<?php	foreach($slider as $slidertitle){
								echo "<tr><td><label for='name' style='font-size:16px;margin-bottom:30px;'>背景名称：（可以为空）</label>";
								echo "<input type='text' id='name' class='form-control' name='slider_title' value='{$slidertitle->post_title}' /></td></tr>";
								echo "<tr><td>";
								echo "<input name='sliderid' type='hidden' id='menu_id' value='{$slidertitle->ID}' maxlength='50' /></td></tr>";
							}
					?>
				
				
					<?php  					
						echo "<tr><td><label for='pic' style='font-size:16px;margin-bottom:30px;'>请上传新背景图片：</label>（建议上传图片大小为400*750）</td></tr>";
						echo "<tr><td><input type='file' class='form-control' name='file' id='file' style='margin-bottom:30px' /></td></tr>";					
					?>		
					<?php
					//echo "<input name='sliderid' type='hidden' id='menu_id' value='{$slidertitle->ID}' maxlength='50 />  ";
					//echo "<input name='urlid' type='hidden' id='url_id' value='{$slidertitle->}' maxlength='50' />  ";	
					//echo "<input name='imgid' type='hidden' id='img_id' value='{$slidertitle->meta_id}' maxlength='50' />  ";	
					?>
					</div>
						<tr><td>		
						<div width="150" align="right">
							<input type="submit" class="btn btn-primary" value="更新" style="width:120px; margin-top:30px;"/>		
						</div>	
						</tr></td>
					</table>
					
				</form>
			</div>
		</div>
	</body>	
</html>