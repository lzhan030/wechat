<?php

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');

include '../common/dbaccessor.php';
include '../common/web_constant.php';
	
//拿到window.open里传递过来的值	
$siteId=$_GET["siteId"];	
	
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
				<form role="form" action="<?php echo constant("CONF_THEME_DIR"); ?>/wesite/mobilepagev3/baimg_insert.php?beIframe&siteId=<?php echo $siteId?>" method="post" enctype="multipart/form-data"> 
					<div class="form-group">
						<table width="600" bordercolor="#06c" border="0" align="center" cellpadding="10" cellspacing="0" style="margin-top:25px;">
						<tr>	
							<td>
								<label for="name" style="font-size:16px">背景名称：（可以为空）</label>
								<input type="text" id="name" class="form-control" name="baimg_title"/> 
							</td>
						</tr>
						<tr>	
							<td><label for="pic" style="font-size:16px">上传背景图片：</label>（建议上传图片大小为400*750）</td>
						</tr>
						<tr>	
							<td>
							<img id="pic" src="#" alt="图片预览" height='90' width='90'/>
							<input type="file" class="form-control" name="file" id="file" onchange="previewImage(this)"/>					 
							</td>
						</tr>
							<td>
							<div width="50" hight="10"align="center" >
								<input type="submit" class="btn btn-sm btn-primary" style="width:120px" value="添加" />		
							</div>
							</td>
					
						</table>					
				</form>
			</div>
		</div>
	</body>	
	<script language='javascript'>
	$("#pic").hide();
	function previewImage(file){  
	
		var picsrc = document.getElementById('pic');  
	  
		if (file.files && file.files[0]) {//chrome   
			var reader = new FileReader();
			reader.readAsDataURL(file.files[0]);  
			reader.onload = function(ev){
			picsrc.src = ev.target.result;
			$("#pic").show();
			
			}   
		
		}  else{
			//IE下，使用滤镜 出现问题
			picsrc.style.maxwidth="50px";
			picsrc.style.maxheight = "12px";
			picsrc.style.overflow="hidden";
			var picUpload = document.getElementById('file'); 
			picUpload.select();
			var imgSrc = document.selection.createRange().text;  
			picsrc.filters.item("DXImageTransform.Microsoft.AlphaImageLoader").src = imgSrc;
			picsrc.style.filter="progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale,src=\""+imgSrc+"\")";
			
		}                    
	}  
	</script>
</html>