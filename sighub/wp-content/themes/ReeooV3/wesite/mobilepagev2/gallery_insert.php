<?php
@session_start();

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');

?>


<?php

include '../common/dbaccessor.php';
include '../common/upload.php';
	
	if($_FILES["file"]["error"] > 0){
			echo "no image";
	}else{
		$up=new upphoto();

		$up->get_ph_tmpname($_FILES["file"]["tmp_name"]);
		$up->get_ph_type($_FILES["file"]["type"]);
		$up->get_ph_size($_FILES["file"]["size"]);
		$up->get_ph_name($_FILES["file"]["name"]);
		$up->get_ph_surl("/uploads/2013/11/".$_FILES["file"]["name"]);
		$picUrl=$up->save();
		if($picUrl!=false){
			//$path=substr( $picUrl,strripos($picUrl,'uploads/')+8 );
			$path=substr( $picUrl,1);
			
			$siteId=$_REQUEST["siteId"];
			//往post和postmeta里添加图片记录
			$insert_imgid=web_admin_add_image($picUrl,$path,$siteId);
			web_admin_create_gallery($insert_imgid,$siteId);
		}else{
			echo '图片上传错误，可能是空间不足，请检查后重试';
		}
	}					

 ?>
