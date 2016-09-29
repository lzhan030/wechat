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
	
	$counts=web_admin_count_bacimg3($siteId);

	foreach($counts as $count){							
		$bacimgCount=$count->bacCount;
	}

	if($bacimgCount>3){
		echo "<h3>创建失败！\n只能创建四个背景图片!</h3>";
		echo "<body onload='closeit()'>";
	}
	
//上传图片
	else if($_FILES["file"]["error"] > 0){
			echo "没有图片";
	}else{
		$up=new upphoto();
		$up->get_ph_tmpname($_FILES["file"]["tmp_name"]);
		$up->get_ph_type($_FILES["file"]["type"]);
		$up->get_ph_size($_FILES["file"]["size"]);
		$up->get_ph_name($_FILES["file"]["name"]);
		$up->get_ph_surl("/uploads/2013/12/".$_FILES["file"]["name"]);
		
		
		
		$picUrl=$up->save(); //返回一个图片的路径
		
		$upload_dir = wp_upload_dir();
		//$path=substr( $picUrl,strripos($picUrl,'uploads/')+8 );
		
		if($picUrl!=false){   //判断是否返回图片的路径，如果返回则执行以下的操作
            $path=substr( $picUrl,1);
			$baimg_title=$_POST['baimg_title'];//获得背景名称。。可以为空
			$insert_imgid=web_admin_add_image3($picUrl,$path,$siteId,$baimg_title);//往posts里面添加图片信息
			echo "上传背景图片成功";
			/*if($count_bac>5){
				echo "只能添加六页";
				echo "<body onload='closeit()'>";
			}else{
				$bac_insert=web_admin_insert_site_bacimg($siteId,$picUrl);
				echo "上传成功";
			}*/
		}else{
		
			echo "上传失败，可能是空间不足，请检查后重试";
		}
		web_admin_create_slider($baimg_title,$siteId,$insert_imgid);//往posts中添加 slider 并且往postmeta中添加图片的链接
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