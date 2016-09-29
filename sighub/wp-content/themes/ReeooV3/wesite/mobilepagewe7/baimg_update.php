<?php
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
	<meta charset="<?php bloginfo( 'charset' ); ?>" />																																																	
</head>
<body onload="closeit()">
<?php

	include '../common/dbaccessor.php';
	include '../common/upload.php';
	global $wpdb;
	$siteId=$_REQUEST["siteId"];
	
	//上传图片
	if($_FILES["file"]["error"] > 0){
			echo "<h3>保存背景图片失败！</h3>";
	}else{
		$up=new upphoto();
		$up->get_ph_tmpname($_FILES["file"]["tmp_name"]);
		$up->get_ph_type($_FILES["file"]["type"]);
		$up->get_ph_size($_FILES["file"]["size"]);
		$up->get_ph_name($_FILES["file"]["name"]);
		$up->get_ph_surl("/bac_image/".$_FILES["file"]["name"]);
		$picUrl=$up->save();
		if($picUrl!=false){
			$bac_update=web_admin_update_site_bacimg($siteId,$picUrl);
			$wpdb -> replace($wpdb -> prefix.'site_styles',array(
				'site_id' => $siteId,
				'templateid' => $wpdb -> get_var("SELECT site_value FROM {$wpdb -> prefix}orangesitemeta WHERE site_id='{$siteId}' AND site_key = 'we7templatestyle'"),
				'variable' => 'indexbgimg',
				'content' => $picUrl)
				
			);
			if($bac_update===false){
				echo "<h3>保存背景图片失败！</h3>";
			}else{
				echo "<h3>保存背景图片成功！</h3>";
			}
		}else{
			echo "<h3>保存背景图片失败，可能是空间不足，请检查后重试！</h3>";
		}
	}
?>
<script language='javascript'>
	function closeit() {
		top.resizeTo(300, 200); 		
		setTimeout("self.close()", 2000); 
		opener.location.reload(); 
	}
</script>
</body>
</html>