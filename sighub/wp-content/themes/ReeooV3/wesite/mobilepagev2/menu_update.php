<?php
@session_start();

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
	
	$menu_title=$_POST['menu_title'];
			
	if($_POST["menuUrl"]==0){				
		$menuiUrl=$_POST['menuiUrl'];
	}else if($_POST["menuUrl"]==1){
		$menuiUrl=$_POST['menuoUrl'];
	}else if($_POST["menuUrl"]==2){
		$menuiUrl=$_POST['memUrl'];
	}else if($_POST["menuUrl"]==3){
		$menuiUrl=$_POST['memwsUrl'];
	}else if($_POST["menuUrl"]==4){
		$menuiUrl=$_POST['memregUrl'];
	}
	/* else if($_POST["menuUrl"]==4){
		$menuiUrl=$_POST['memhwUrl'];
	}else if($_POST["menuUrl"]==5){
		$menuiUrl=$_POST['memncUrl'];
	}	 */	
	
	/*如果包含homeurl，则截取后入数据库*/
	$tmp = stristr($menuiUrl,home_url());
	if($tmp===false){
		$inserturl=$menuiUrl;
	}else{
		$str = stristr($menuiUrl, home_url());
		$postion=intval($str)+intval(strlen(home_url()));
		$inserturl=substr($menuiUrl, $postion);		
	}				
			
	$menuid=$_POST['menuid'];
	$imgid=$_POST['imgid'];
	$urlid=$_POST['urlid'];
	$delimgid=$_POST['delimgid'];
	
	
	//可以选择不更新图片
	if ($_FILES["file"]["error"] > 0){
		if($delimgid!=-1){
			web_admin_update_menu($menuid,$menu_title, null, $imgid,$inserturl,$urlid);
		}else{
			web_admin_update_menu($menuid,$menu_title, -1, $imgid,$inserturl,$urlid);
		}
	}else{
		
		$up=new upphoto();
	
		$up->get_ph_tmpname($_FILES["file"]["tmp_name"]);
		$up->get_ph_type($_FILES["file"]["type"]);
		$up->get_ph_size($_FILES["file"]["size"]);
		$up->get_ph_name($_FILES["file"]["name"]);
		$up->get_ph_surl("/uploads/menu_image/".$_FILES["file"]["name"]);
		$picUrl=$up->save();
		//$path=substr( $picUrl,strripos($picUrl,'uploads/')+8 );
		if($picUrl!=false){
			$path=substr( $picUrl,1);
			//往post和postmeta里添加图片记录
			$insert_imgid=web_admin_add_image($picUrl,$path,1);
										
			web_admin_update_menu($menuid,$menu_title, $insert_imgid, $imgid,$inserturl,$urlid);
						
			echo "更新图片成功!";
		}else{
			echo '图片上传错误，可能是空间不足，请检查后重试';
		}
	}
		
?>
<?php
    echo "<br>";
	echo "更新成功!";
?>



<script language='javascript'>

	function closeit() {
		top.resizeTo(300, 200); //控制网页显示的大小		
		setTimeout("self.close()", 3000); //毫秒
		opener.location.reload();  //主页面刷新显示
	}
</script>
</body>
</html>