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
	//添加小按钮
	$siteId=$_REQUEST["siteId"];
	$counts=web_admin_count_menu($siteId);
	$menu_title=$_POST['menu_title'];
	$menuiUrl=$_POST['menuiUrl'];
	if($_POST["menuUrl"]==0){				
		$menuiUrl=$_POST["menuiUrl"];				
	}else if($_POST["menuUrl"]==1){
		$menuiUrl=$_POST['menuoUrl'];
	}else if($_POST["menuUrl"]==2){
		$menuiUrl=$_POST['memUrl'];				
	}else if($_POST["menuUrl"]==3){
		$menuiUrl=$_POST['mem1Url'];				
	}				

	foreach($counts as $count){							
		$siteCount=$count->siteCount;
	}

	if($siteCount>5){
		echo "<h3>创建失败！\n只能创建六个菜单按钮!</h3>";
		echo "<body onload='closeit()'>";
	}
	
	else if($_FILES["file"]["error"] > 0){
		web_admin_create_menu($menu_title, "", $menuiUrl,$siteId);
		echo "<h3>新菜单按钮已添加成功！</h3>"; 
	}else{
		
		$up=new upphoto();
		$up->get_ph_tmpname($_FILES["file"]["tmp_name"]);
		$up->get_ph_type($_FILES["file"]["type"]);
		$up->get_ph_size($_FILES["file"]["size"]);
		$up->get_ph_name($_FILES["file"]["name"]);
		$up->get_ph_surl("/uploads/menu_image/".$_FILES["file"]["name"]);
		$picUrl=$up->save();
		$path=substr( $picUrl,strripos($picUrl,'uploads/')+8 );
		
		if($picUrl!=null){
			$insert_imgid=web_admin_add_image($picUrl,$path,$siteId);
			
			web_admin_create_menu($menu_title, $insert_imgid, $menuiUrl,$siteId);
			echo "<h3>新菜单按钮已添加成功！</h3>";
		}else{
			echo "<h3>上传错误！</h3>";
		}
	}
?>	
<script language='javascript'>
	function closeit() {
		top.resizeTo(300, 200); 		
		setTimeout("self.close()", 3000); 
		opener.location.reload();  
	}
</script>
</body>
</html>