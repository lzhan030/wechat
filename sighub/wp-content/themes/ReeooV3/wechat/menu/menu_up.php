<?php
    $tmp_path = explode ( 'ReeooV3', __FILE__ );
	$template_path=$tmp_path[0];
	require_once $template_path.'ReeooV3/wechat/common/session.php';

	$path = explode ( 'wp-content', __FILE__ );
	$wp_root_path = $path [0];
	require_once ($wp_root_path . '/wp-load.php');
?>

<?php
	include '../common/wechat_dbaccessor.php';
	include 'menu_permission_check.php';
	$menuId=$_GET["menuId"];
	$menuName=$_POST["menuname"];
	$ismenuid=$_POST["ismenuid"];
	if(empty($menuId) || empty($menuName)){
       echo "不能为空";exit;
    }
	
	$updaterlt=wechat_menu_name_update($menuName, $menuId);

	if($updaterlt===false){
		echo "更新失败！";
	}else{
		echo "更新成功！";
	}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	</head>
	<body onload="closeit()">
	</body>

	<script language='javascript'>
		function closeit() {
			top.resizeTo(300, 200); 		
			setTimeout("self.close()", 2000); 
			//更新菜单名称时,传递menusecid默认选中
			var ismenuid="<?php echo $ismenuid; ?>";
			var menuId="<?php echo $menuId; ?>";
			var url="<?php echo get_template_directory_uri(); ?>";
			if(ismenuid==menuId){
				opener.location.href=url+'/wechat/menu/menu.php?beIframe&menusecid='+menuId;
			}else{
				opener.location.href=url+'/wechat/menu/menu_invented.php?beIframe&menusecid='+menuId;
			}
		}
	</script>
</html>