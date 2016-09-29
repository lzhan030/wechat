<?php
/**
 * KindEditor PHP
 * 
 * 本PHP程序是演示程序，建议不要直接在实际项目中使用。
 * 如果您确定直接使用本程序，使用之前请仔细确认相关安全设置。
 * 
 */
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');


require_once 'JSON.php';


//文件保存目录路径
$domain = "wordpress";

//定义允许上传的文件扩展名
$ext_arr = array('gif', 'jpg', 'jpeg', 'png', 'bmp');
//最大文件大小
$max_size = 1000000;


//有上传文件时
if (empty($_FILES) === false) {
	//对原文件名进行重新命名，time+rand命名方式20150701
	$_FILES['imgFile']['name'] = time().rand().strstr($_FILES['imgFile']['name'],'.');
	$file_name = $_FILES['imgFile']['name'];
	//服务器上临时文件名
	$tmp_name = $_FILES['imgFile']['tmp_name'];
	//文件大小
	$file_size = $_FILES['imgFile']['size'];
	//检查文件名
	if (!$file_name) {
		alert("请选择文件。");
	}
	//检查是否已上传
	if (@is_uploaded_file($tmp_name) === false) {
		alert("临时文件可能不是上传文件。");
	}
	//检查文件大小
	if ($file_size > $max_size) {
		alert("上传文件大小超过限制。");
	}
	//获得文件扩展名
	$temp_arr = explode(".", $file_name);
	$file_ext = array_pop($temp_arr);
	$file_ext = trim($file_ext);
	$file_ext = strtolower($file_ext);
	//检查扩展名
	if (in_array($file_ext, $ext_arr) === false) {
		alert("上传文件扩展名是不允许的扩展名。");
	}
	
	/*
	//新文件名
	$new_file_name = date("YmdHis") . '_' . rand(10000, 99999) . '.' . $file_ext;
    //上传文件并返回地址
	$s = new SaeStorage();
    $file_url = $s->upload( $domain , $new_file_name , $tmp_name);
	*/
	if ( ! function_exists( 'wp_handle_upload' ) ) 
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
	$uploadedfile = $_FILES['imgFile'];
	$upload_overrides = array( 'test_form' => false );
	$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
	$file_url = $movefile['url'];
	/*
	$str = strstr($movefile['url'], 'uploads');
	$file_url=substr($str, 7);
	*/
	header('Content-type: text/html; charset=UTF-8');
	$json = new Services_JSON();
	echo $json->encode(array('error' => 0, 'url' => $file_url));
	exit;
}

function alert($msg) {
	header('Content-type: text/html; charset=UTF-8');
	$json = new Services_JSON();
	echo $json->encode(array('error' => 1, 'message' => $msg));
	exit;
}
?>