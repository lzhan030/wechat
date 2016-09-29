<?php
session_start();
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
require_once '../ReeooV3/wesite/common/upload.php';
global $wpdb;
?>

<?php
$gweid = $_GET['gweid'];
//处理上传图片
//有些手机中的图片没有扩展名导致上传不成功，这里加上jpg类型
$type =strtolower(strstr($_FILES['file']['name'], '.'));
if($type == false)
{
	$_FILES['file']['name'] = $_FILES['file']['name'].".jpg";
	$type = ".jpg";
}
$picname = $_FILES['file']['name'];
$picsize = $_FILES['file']['size'];

//echo $picname.$picsize;

if ($picname != "") {
	if ($picsize > 10240000) {
		
		$hint = array("status"=>"上传失败","pic"=>"","message"=>"图片大小不能超过10M!");
		echo json_encode($hint);
		exit;
	}
	if ($type != ".gif" && $type != ".jpg"&& $type != ".png" && $type != ".jpeg" && $type != ".bmp") {
		
		$hint = array("status"=>"上传失败","pic"=>"","message"=>"图片格式不对!");
		echo json_encode($hint);
		exit;	
	} 
	//$up=new upphoto();	
	$upload = new upphoto($gweid);   //通过gweid取到userid
	//$picUrl=$up->save();
	$picUrl = $upload -> up_photo(array(
		'name' => $picname,
		'type' => $type,
		'tmp_name' => $_FILES['file']['tmp_name'],
		'error' => $_FILES['file']['error'],
		'size' => $picsize
	));
	
	if($picUrl===false){	
		$hint = array(
		    'pic'=>"",
			'size'=>"",
			'status'=>"上传失败",
			'message'=>"文件上传错误,可能是空间不足,请检查后重试"
		);
		echo json_encode($hint);
		exit;
	}
}
$size = round($picsize/1024,2);
$upload =wp_upload_dir();
if((empty($picUrl))||(stristr($picUrl,"http")!==false)){
	$echopicurl=$picUrl;  //admin/2014/12/1418306009216889574.jpg 
}else{
	$echopicurl=$upload['baseurl'].$picUrl;  //http://wpcloudforsina-wordpress.stor.sinaapp.com/uploads/admin/2014/12/14183060971355922556.jpg 
}
//echo $echopicurl;
if(!empty($picUrl)){
	$hint = array(
		'pic'=>$picUrl,
		'size'=>$size,
		'status'=>"上传成功"
	);
}else{
	$hint = array(
		'pic'=>"",
		'size'=>"",
		'status'=>"上传失败，请重试"
	);
}

echo json_encode($hint);
exit;
?>