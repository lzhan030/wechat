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
require_once '../../wesite/common/dbaccessor.php';

$menuId=$_GET["menuId"];
$menuType=$_GET["menuType"];
$menuKey=$_GET["menuKey"];
$menuName=$_GET["menuName"];
$menuPad=$_GET["menuPad"];
//$content=$_GET["content"];
$content=stripslashes(unescape($_REQUEST['content']));
if($menuId==-1){
}else{	
	if($menuType=="weChat_text"){
		//这里插入文本素材库
		//判断是否是分组管理员中的用户
		$groupadminflag = web_admin_issuperadmin($current_user->ID);
		$currentuser =  ((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($groupadminflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
		//$currentuser= (is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
		$menKey=wechat_autrplay_text_insert_group("menu",$content,$currentuser,$_SESSION['GWEID']);
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
	
	//20140624 janeen update
	$exist_menuid=wechat_menu_public_exist($menuId,$_SESSION['WEID']);
	if($exist_menuid==false||$exist_menuid==null||$exist_menuid==""){
	    //20140624 janeen update
		$rs=wechat_menu_public_selectwid($menuId,$_SESSION['WEID']);
		foreach($rs as $rsmessage){
				$wid=$rsmessage->wid;
				$mid=$rsmessage->M_id;
			
		}
		$insert=wechat_menu_publicsvc_insert($menuId,$menuType,$menuinKey,$_SESSION['WEID'],$wid,$mid);
	}
	else{
		$update=wechat_menu_publicsvc_update($menuId,$menuType,$menuinKey,$_SESSION['WEID']);
	}
}			

?>

<body onload='closeit()'>
<script language='javascript'>
	function closeit() {
		top.resizeTo(300, 200); 	
		setTimeout("self.close()", 3000); 
		opener.location.reload();
	}
    
</script>