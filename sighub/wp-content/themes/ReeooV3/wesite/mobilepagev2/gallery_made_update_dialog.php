<?php
@session_start(); 

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');

?>

<?php
    include '../common/dbaccessor.php';
	include '../common/web_constant.php';
	//拿到window.open里传递过来的值
	$galleryId=$_GET["galleryId"];	
	
	//获取特定的gallery
	$gallery=web_admin_get_gallery($galleryId); 

	//获取gallery的img
	$gallery_img=web_admin_get_gallery_imgs($galleryId);
	  
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
				<form action="<?php echo constant("CONF_THEME_DIR"); ?>/wesite/mobilepagev2/gallery_made_update.php?beIframe" method="post" enctype="multipart/form-data">
					<?php  	 foreach($gallery as $gallery_info){
								foreach($gallery_img as $gallery_img_info){					
									echo "<label for='pic'>图片</label>";
									echo "<input type='file' name='file' id='file' />";					
									echo "<input name='galleryid' type='hidden' id='gallery_id' value='{$gallery_info->ID}' maxlength='50' />  ";
									echo "<input name='imgid' type='hidden' id='img_id' value='{$gallery_img_info->meta_id}' maxlength='50' />  ";	
								}
							}
					?>		
					<div width="150">
						<input type="submit" value="更新" />	
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
