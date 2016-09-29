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
	global $wpdb;
	//判断是否是分组管理员中的用户
	$groupadminflag = web_admin_issuperadmin($current_user->ID);
	$currentuser =  ((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($groupadminflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
	//$currentuser= (is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
	
	$sendContent=$_POST['sendContent'];
	//先通过unescape解码js传递过来的escape编码后的内容
	$content=stripslashes($_POST['content']);
     if(isset($_POST['clear_text']))
    {	
		$b = wechat_clearNews_group('subscribe',$_SESSION['GWEID'],'weChat_text');		
		echo '<script>';
        if($b)
            echo "alert('删除成功!');";
        else
            echo "alert('没找到要删除的信息，删除失败!');";
        echo "location.href='".$_SERVER["HTTP_REFERER"]."';</script>";
        exit;
    }
	$mes=wechat_mess_kw_get_group('subscribe',$_SESSION['GWEID']);
	if($mes!=null){
		foreach($mes as $messa){
			//判断有关注回复记录
			$arp_id=$messa->arply_id;
			$text=wechat_autrplay_text_get_group("subscribe",$_SESSION['GWEID']);
			if($text!=null){
				foreach($text as $t){
					$text_id=$t->text_id;
				}

				$update=wechat_autrplay_text_update($content,$text_id);
				if($update===false){
					echo "内容更新失败！";
				}
				$aty_upt=wechat_autrplay_acty($text_id,"weChat_text",$arp_id);
               if($aty_upt===false){
					echo "内容更新失败！";
				}else{
					echo "成功设置该文本为默认回复内容";
				}
			}else{
				$text_id=wechat_autrplay_text_insert_group("subscribe",$content,$currentuser,$_SESSION['GWEID']);
				$aty_upt=wechat_autrplay_acty($text_id,"weChat_text",$arp_id);
				if($aty_upt===false){
					echo "内容更新失败！";
				}else{
					echo "成功设置该文本为默认回复内容";
				}		
			}
			
			
		}
	}else{
		$text_id=wechat_autrplay_text_insert_group("subscribe",$content,$currentuser,$_SESSION['GWEID']);
		$insert=wechat_mess_kw_add_group("weChat_text",$text_id,"subscribe",$currentuser,$_SESSION['GWEID']);
		if($insert===false){
			echo "内容添加失败！";
		}else{
			echo "成功设置该文本为默认回复内容";
		}	
	}
?>