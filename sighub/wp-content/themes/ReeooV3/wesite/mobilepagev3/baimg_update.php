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
	$slider_title=$_REQUEST["slider_title"];
	$sliderid=$_REQUEST["sliderid"];
	//echo $slider_title;
    //echo $sliderid;
	/*$imgid=web_admin_get_img_id('sliderid')
	foreach ($imgid as $imgids){
		$img_id=$imgids->meta_value;
	}*/
	
//可以不更新图片
	if($_FILES["file"]["error"] > 0){
			web_admin_update_slider($slider_title,$sliderid,null);
	}else{
	//先执行删除上一个的图片信息然后再执行更新图片的操作
		web_admin_delete_slider_img($sliderid);
		$up=new upphoto();
		$up->get_ph_tmpname($_FILES["file"]["tmp_name"]);
		$up->get_ph_type($_FILES["file"]["type"]);
		$up->get_ph_size($_FILES["file"]["size"]);
		$up->get_ph_name($_FILES["file"]["name"]);
		$up->get_ph_surl("/uploads/2013/12/".$_FILES["file"]["name"]);
		//$path=substr( $picUrl,strripos($picUrl,'uploads/')+8 );
		
		$picUrl=$up->save(); //返回一个图片的路径
		
		if($picUrl!=false){   //判断是否返回图片的路径，如果返回则执行以下的操作
			$path=substr( $picUrl,1);
			$bac_update=web_admin_update_slider_img($slider_title,$sliderid,$picUrl,$path,$siteId);
			if($bac_update===false){
				echo "上传背景图片失败！";
			}else{
				echo "上传背景图片成功！";
			}
		}else{
			echo "上传错误，可能是空间不足，请检查后重试！";
		}
	}

?>
<script language='javascript'>
	function closeit() {
		top.resizeTo(300, 200); //控制网页显示的大小		
		setTimeout("self.close()", 2000); //毫秒
		opener.location.reload();  //主页面刷新显示
	}
</script>
</body>
</html>