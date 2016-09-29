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

//文件保存目录路径
$domain = "wordpress";

//定义允许上传的文件扩展名
$ext_arr = array('gif', 'jpg', 'jpeg', 'png', 'bmp');
//最大文件大小
$max_size = 1000000;


//有上传文件时
if (empty($_FILES) === false) {
	global $wpdb;
	global  $current_user;
	//判断是否是分组管理员下的用户
	$getgroupuserids = $wpdb->get_results( "SELECT group_id, flag FROM {$wpdb -> prefix}user_group where user_id = ".$current_user->ID);
	if(!empty($getgroupuserids)){
		foreach($getgroupuserids as $getgroupinfo)
		{
		    $usergroupid = $getgroupinfo -> group_id;
		    $usergroupflag = $getgroupinfo -> flag;
		}
	}else{  //分组里没有记录，则属于默认分组，groupid为0，对应的flag为0
		$usergroupid = 0;
		$usergroupflag = 0;
	}
	
	$id = ((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($usergroupid !=0 && $usergroupflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
	
	//$id = (is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
	if(empty($id))
		if(empty($this -> userid))
			return false;
		else
			$id = $this -> userid;

	if ( ! function_exists( 'wp_handle_upload' ) ) 
			require_once( ABSPATH . 'wp-admin/includes/file.php' );


	//原文件名
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
	$size = $file_size/1048576;		
	$size=number_format($size,3,'.','');
	$user_space = $wpdb -> get_row("SELECT * FROM {$wpdb->prefix}wesite_space WHERE userid = {$id}",ARRAY_A);
	$available_space = $user_space['defined_space'] - $user_space['used_space'];
	if($available_space<$size)
		alert("空间不足，请先申请空间");

	$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
	$file_url = $movefile['url'];
	/*
	$str = strstr($movefile['url'], 'uploads');
	$file_url=substr($str, 7);
	*/
	if ( $movefile ) 
		$wpdb->query(
			"
			UPDATE {$wpdb->prefix}wesite_space 
			SET used_space = used_space+{$size}
			WHERE userid = {$id}
			"
		);
	upload_callback($file_url,'file_uploaded','ok');
	exit;
}

function alert($msg) {
	upload_callback('',$msg,'failed');
	exit;
}

function upload_callback($file_name,$result,$resultcode){
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>JustBoil's Result Page</title>
<script language="javascript" type="text/javascript">
	window.parent.window.jbImagesDialog.uploadFinish({
		filename:'<?php echo $file_name; ?>',
		result: '<?php echo $result; ?>',
		resultCode: '<?php echo $resultcode; ?>'
	});
</script>
<style type="text/css">
	body {font-family: Courier, "Courier New", monospace; font-size:11px;}
</style>
</head>

<body>

Result: <?php echo $result; ?>

</body>
</html>
<?php
exit();
}
?>