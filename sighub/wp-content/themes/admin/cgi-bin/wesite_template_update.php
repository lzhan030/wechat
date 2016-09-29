<?php
	$path = explode ( 'wp-content', __FILE__ );
	$wp_root_path = $path [0];
	require_once ($wp_root_path . '/wp-load.php');	
	require_once './wp-content/themes/ReeooV3/wesite/common/dbaccessor.php';	
	require_once './wp-content/themes/admin/cgi-bin/zipfile_download.php';

	$id = $_POST["id"];
	$title = $_POST["title"];
	$activate = $_POST["activate"];
	$background = $_POST["background"];
	$slide = $_POST["slide"];
	$menu = $_POST["menu"];
	$menu_bg = $_POST["menu_bg"];
	$image_icon = $_POST["image_icon"];
	
	//$name = "style" . (string)($id-1);
	$domain = parse_url(home_url());
	$d = $domain["host"];
	$name = $d . (string)($id-1);

	$wesit_url = "wp-content/themes/mobilepagewe7/template/";
	$wesit_new_url = $wesit_url . $name . "/";	
	$rlt = web_admin_update_newtemplate($id, $title, $activate, $background, $slide, $menu, $menu_bg, $image_icon);

	if($_FILES["uploadfile"]["tmp_name"]) {
		$filename = $_FILES["uploadfile"]["name"];
		$source = $_FILES["uploadfile"]["tmp_name"];
		$type = $_FILES["uploadfile"]["type"];
		//delete the folder
		DeleteDir($wesit_new_url);
		//upload and unzip the file
		$unzip_rlt = upload_upzip($filename, $source, $type, $wesit_new_url);
		if($unzip_rlt){
			//insert the db _site_templates
			$message = "模板更新成功！";
			$result = true;				
		} else {
			$message = "模板更新失败！";
			$result = false;			
		}
	} else{
		$message = "模板更新成功";
		$result = true;	
	}

?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="refresh" content="5;url=<?php echo home_url()?>?admin&page=we7stylemanage">
	<style type="text/css">
  		.alert{width: 60%;  margin-left: 18%;  margin-top: 50px;  min-height: 100px;  font-size: 16px;padding: 30px;}
  		.msg{margin-top:20px;margin-left:42px; font-weight:normal;}
  		.title{font-size:24px;font-weight:bold;}
  		.icon{font-size:32px; font-weight: bold}
  	</style>
</head>
<body>
	<?php if($result) {?>
	<div class="alert alert-success" role="alert">
		<span class="glyphicon glyphicon-ok icon"></span> <font class="title">&nbsp<?php echo $message?></font><br/> 
		<div class="msg"><a href="<?php echo home_url()?>?admin&page=we7stylemanage">如果你的浏览器没有跳转，请点击此链接。</a></div>
	</div>
	<?php } else {?>
	<div class="alert alert-warning" role="alert">
		<span class="glyphicon glyphicon-remove icon"></span> <font class="title">&nbsp<?php echo $message?></font><br/> 
		<div class="msg"><a href="<?php echo home_url()?>?admin&page=we7stylemanage">如果你的浏览器没有跳转，请点击此链接。</a></div>
	</div>	
	<?php }?>
</body>
</html>
