<?php 
$tmp_path = explode ( 'ReeooV3', __FILE__ );
$template_path=$tmp_path[0];
require_once $template_path.'ReeooV3/wechat/common/session.php';

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
global  $current_user;
if( !isset($current_user->user_login)|| empty($current_user->user_login)){
		wp_redirect(wp_login_url());
}	
get_header(); 
?>



<?php

include '../common/wechat_dbaccessor.php';

$menuId=$_GET["menuId"];
$menuType=$_GET["menuType"];
$menuKey=$_GET["menuKey"];
$menuName=$_GET["menuName"];
$menuPad=$_GET["menuPad"];
$content=$_GET["content"];
if($menuId==-1){
}else{	
	if($menuType=="weChat_text"){
		//这里插入文本素材库
		//20140623 janeen update
		//$menKey=wechat_autrplay_text_insert("menu",$content,$current_user->ID,$_SESSION['WEID']);
		$menKey=wechat_autrplay_text_insert_group("menu",$content,$current_user->ID,$_SESSION['GWEID']);
		//end
		$menuinKey="t".$menKey;
	}else if($menuType=="weChat_news"){
		$menuinKey="s".$menuKey;
	}else if($menuType=="view"){
		/*如果包含homeurl，则截取后入数据库*/
		$tmp = stristr($menuKey,home_url());
		if($tmp===false){
			$menuinKey=$menuKey;
		}else{
			$str = stristr($menuKey, home_url());
			$postion=intval($str)+intval(strlen(home_url()));
			$menuinKey=substr($menuKey, $postion);		
		}
	}
	$update=wechat_menu_update($menuId,$menuPad,$menuName,$menuType,$menuinKey);
	
}			




?>

<body onload='closeit()'>
<script language='javascript'>
	function closeit() {
		top.resizeTo(300, 200); //������ҳ��ʾ�Ĵ�С		
		setTimeout("self.close()", 3000); //����
		
		opener.location.reload();  //��ҳ��ˢ����ʾ
	}
    
</script>