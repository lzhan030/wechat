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
	
	include '../common/wechat_dbaccessor.php';
	require_once '../../wesite/common/dbaccessor.php';
	include 'autoreply_permission_check.php';
	global $wpdb;
	$netId=$_GET["netId"];
	$mes=wechat_mess_kw_get_group('subscribe',$_SESSION['GWEID']);	
	if($mes!=null){
		foreach($mes as $messa){
			$arp_id=$messa->arply_id;
		}
		$aty_upt=wechat_autrplay_acty($netId,"weChat_news",$arp_id);	
		if($aty_upt===false){
			echo "设置失败！";
		}else{
			echo "成功设置该多图文为默认回复内容";
		}
	}else{
		//判断是否是分组管理员中的用户
		$groupadminflag = web_admin_issuperadmin($current_user->ID);
		$currentuser =  ((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($groupadminflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
		//$currentuser= (is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
		wechat_mess_kw_add_group("weChat_news",$netId,"subscribe",$currentuser,$_SESSION['GWEID']);
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
