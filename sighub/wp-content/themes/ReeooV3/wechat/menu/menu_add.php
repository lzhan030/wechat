<?php
    $tmp_path = explode ( 'ReeooV3', __FILE__ );
	$template_path=$tmp_path[0];
	require_once $template_path.'ReeooV3/wechat/common/session.php';
	$path = explode ( 'wp-content', __FILE__ );
	$wp_root_path = $path [0];
	require_once ($wp_root_path . '/wp-load.php');
    global $current_user;
?>

<?php
	include '../common/wechat_dbaccessor.php';
	require_once '../../wesite/common/dbaccessor.php';
	$parId=$_GET["parId"];
	$menuName=$_POST["menuname"];
	$ismenuid=$_POST["ismenuid"];
	if(empty($parId) || empty($menuName)){
       echo "不能为空";
	   exit;
    }
    //判断是否是分组管理员中的用户
	$groupadminflag = web_admin_issuperadmin($current_user->ID);
	$currentuser =  ((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($groupadminflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
	//$currentuser= (is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
	
	$adderlt=wechat_menu_add_group($parId,$menuName,"","",$currentuser,$_SESSION['GWEID']);
	//用户设置完一级菜单后又设置二级菜单，要将一级菜单原有的数据清空，防止用户删除二级菜单后发现原有一级菜单的设置
	$update=wechat_menu_updateforchid($parId,-1,"","");
	
	if($adderlt===false){//用===，不用==，否则返回0也会执行
		echo "添加失败！";
	}else if($update===false){
		echo "添加失败！";
	}else{
		echo "添加成功！";
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
			var ismenuid="<?php echo $ismenuid; ?>";
			var url="<?php echo get_template_directory_uri(); ?>";
			if(ismenuid!=''){
				opener.location.href=url+'/wechat/menu/menu.php?beIframe';
			}else{
				opener.location.href=url+'/wechat/menu/menu_invented.php?beIframe';
			}
		}
	</script>
</html>