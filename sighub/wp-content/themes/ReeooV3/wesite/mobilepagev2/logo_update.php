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

$siteId=$_REQUEST["siteId"];

	//上传图片
	if ($_FILES["file"]["error"] > 0){
			echo "<h3>保存LOGO图片失败！</h3>";
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
				echo "<h3>保存LOGO图片失败！</h3>";
			}else{
				echo "<h3>保存LOGO图片成功！</h3>";
			}
		}else{
			echo "<h3>保存LOGO图片失败，可能是空间不足，请检查后重试！</h3>";
		}
	} 
?>
<script language='javascript'>

	function closeit() {
		top.resizeTo(300, 200); //������ҳ��ʾ�Ĵ�С		
		setTimeout("self.close()", 2000); //����
		opener.location.reload();  //��ҳ��ˢ����ʾ
	}
</script>
</body>
</html>