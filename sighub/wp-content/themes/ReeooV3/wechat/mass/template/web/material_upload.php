<?php

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
include '../../wesite/common/upload.php';

$action = $_GET['act'];
if($action=='delimg'){
	$filename = $_POST['imagename'];
	if(!empty($filename)){
		$del_file=substr( $filename,strripos($filename,'/uploads/')+9);
		$uploadpath = wp_upload_dir();
		$or=@unlink(path_join($uploadpath['basedir'], $del_file) );
		if($or==false){
			echo "这是错误的行为";
		}else{
			echo "这是正确的行为";
		}	
		//unlink($filename);
		echo '1';
	}else{
		echo '删除失败.';
	}
}else{
	$picname = $_FILES['file']['name'];
	$picsize = $_FILES['file']['size'];
	if ($picname != "") {
		if ($picsize > 1024000) {
			echo '图片大小不能超过1M';
			exit;
		}
		$type = strstr($picname, '.');
		if ($type != ".gif" && $type != ".jpg"&& $type != ".png"&& $type != ".bmp") {
			echo '图片格式不对！';
			exit;
		}
		
		$up=new upphoto();
		$up->get_ph_tmpname($_FILES["file"]["tmp_name"]);
		$up->get_ph_type($_FILES["file"]["type"]);
		$up->get_ph_size($_FILES["file"]["size"]);
		$up->get_ph_name($_FILES["file"]["name"]);		
		$picUrl=$up->save();
		if($picUrl!=false){
			$path=substr( $picUrl,strripos($picUrl,'uploads/')+8 );
		}else{
			echo '图片上传错误，可能是空间不足，请检查后重试';
			exit;
		}
	}
	$size = round($picsize/1024,2);
	$upload =wp_upload_dir();
	
	if((empty($picUrl))||(stristr($picUrl,"http")!==false)){
		$echopicurl=$picUrl;
	}else{
		$echopicurl=$upload['baseurl'].$picUrl;
	}
	
	$arr = array(
		'name'=>$picname,
		'pic'=>$echopicurl,
		'size'=>$size
	);
	echo json_encode($arr);
}
?>