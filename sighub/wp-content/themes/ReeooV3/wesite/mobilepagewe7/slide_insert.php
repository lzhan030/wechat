<?php
@session_start();
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
global $wpdb;
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
	$menu_title=$_POST['menu_title'];
	$menuiUrl=$_POST['menuiUrl'];
	if($_POST["menuUrl"]==0){				
		$menuiUrl=$_POST["menuiUrl"];				
	}else if($_POST["menuUrl"]==1){
		$menuiUrl=$_POST['menuoUrl'];
	}else if($_POST["menuUrl"]==2){
		$menuiUrl=$_POST['memUrl'];				
	}else if($_POST["menuUrl"]==3){
		$menuiUrl=$_POST['memwsUrl'];				
	}else if($_POST["menuUrl"]==4){
		$menuiUrl=$_POST['memregUrl'];				
	}
	/*如果包含homeurl，则截取后入数据库*/
	$tmp = stristr($menuiUrl,home_url());
	if($tmp===false){
		$inserturl=$menuiUrl;
	}else{
		$str = stristr($menuiUrl, home_url());
		$postion=intval($str)+intval(strlen(home_url()));
		$inserturl=substr($menuiUrl, $postion);		
	}
	
	
	
	
	global $wpdb;
	$slidecount=$wpdb -> get_var("SELECT count(*) FROM {$wpdb -> prefix}site_nav WHERE `site_id`={$siteId} AND `position`=3");
	
	
	/* remove the slide upload count limit by Sara
	if($slidecount>5){
	echo "<h3>创建失败！\n只能创建六个幻灯片!</h3>";
	echo "<body onload='closeit()'>";
	}
	else if($_FILES["file"]["error"] > 0){ */
	if($_FILES["file"]["error"] > 0){
		$wpdb -> insert($wpdb -> prefix.'site_nav',array(
			'site_id' => $siteId,
			'name' => $menu_title,
			'position'=>3,
			'url' => $inserturl
			
		));

		echo "<h3>新幻灯片已添加成功！</h3>"; 		
		
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
			$insert_imgid=web_admin_add_image($picUrl,$path,$siteId);
			
			$wpdb -> insert($wpdb -> prefix.'site_nav',array(
				'site_id' => $siteId,
				'name' => $menu_title,
				'position'=>3,
				'url' => $inserturl,
				'icon' => $picUrl
			));
			echo "<h3>新幻灯片已添加成功！</h3>";
		}else{
			echo "<h3>上传错误，可能是空间不足，请检查后重试！</h3>";
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