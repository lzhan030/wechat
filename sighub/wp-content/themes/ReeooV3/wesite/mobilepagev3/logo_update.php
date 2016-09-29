<?php

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');

?>


<?php

include '../common/dbaccessor.php';
include '../common/upload.php';

$siteId=$_REQUEST["siteId"];

//上传图片
	if ($_FILES["file"]["error"] > 0){
			echo "no image";

	}else{
		$up=new upphoto();
	
		$up->get_ph_tmpname($_FILES["file"]["tmp_name"]);
		$up->get_ph_type($_FILES["file"]["type"]);
		$up->get_ph_size($_FILES["file"]["size"]);
		$up->get_ph_name($_FILES["file"]["name"]);
		$up->get_ph_surl("/logo_image/".$_FILES["file"]["name"]);
		$picUrl=$up->save();
		if($picUrl!=false){
			$logo_update=web_admin_update_site_logo($siteId,$picUrl);
			if($logo_update===false){
				echo "error";
			}else{
				echo "success";
			}
		}else{
			echo "图片上传错误，可能是空间不足，请检查后重试";
		}
	} 

?>
<body onload="closeit()">
</body>

<script language='javascript'>

	function closeit() {
		top.resizeTo(300, 200); //控制网页显示的大小		
		setTimeout("self.close()", 2000); //毫秒
		opener.location.reload();  //主页面刷新显示
	}
</script>