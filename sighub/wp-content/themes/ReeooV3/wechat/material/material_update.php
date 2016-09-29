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
//判断是否是分组管理员的公众号,分组管理员不需要进行此功能的check
$groupadmincount = is_superadmin($_SESSION['GWEID']);
if($groupadmincount == 0)
	include 'material_permission_check.php';
include '../../wesite/common/upload.php';
$groupadminflag = web_admin_issuperadmin($current_user->ID);
$newsItemId=$_POST["netId"];
$itemId=$_POST["itemId"];
$title=$_POST["title"];
$desc=$_POST["desc"];

if($_POST["itmesUrl"]==0){						
	$itemUrl=$_POST["itemiUrl"];
						
}else{
	$itemUrl=$_POST["itemoUrl"];
}			
/*如果包含homeurl，则截取后入数据库*/
	$tmp = stristr($itemUrl,home_url());
	if($tmp===false){
		$inserturl=$itemUrl;
	}else{
		$str = stristr($itemUrl, home_url());
		$postion=intval($str)+intval(strlen(home_url()));
		$inserturl=substr($itemUrl, $postion);		
	}

if($itemId==0){
		
	if($_FILES["file"]["error"] > 0){
			echo "no image";
	}else{
		$up=new upphoto();
		$up->get_ph_tmpname($_FILES["file"]["tmp_name"]);
		$up->get_ph_type($_FILES["file"]["type"]);
		$up->get_ph_size($_FILES["file"]["size"]);
		$up->get_ph_name($_FILES["file"]["name"]);
		$up->get_ph_surl("/weChatImageStorage/".$_FILES["file"]["name"]);
		$itemPicUrl=$up->save();
		if($itemPicUrl!=false){
			//判断是添加一个新的多图文还是添加多图文中的一个图文
			if($newsItemId==0){
				$newmax=material_news_getmax();
				foreach($newmax as $nm){
					$newsItemId=$nm->maxnid+1;
				}
			}
			//判断是否是分组管理员中的用户
			$groupadminflag = web_admin_issuperadmin($current_user->ID);
			$currentuser =  ((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($groupadminflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
			//$currentuser= (is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
			$matr_add=material_news_add_group($title,$inserturl,$itemPicUrl,$desc,$newsItemId,$currentuser,"",$_SESSION['GWEID']);
			if($matr_add===false){
				echo "添加失败";
			}else{
				echo "添加成功";
			}
		}else{
			echo "上传错误，可能是空间不足，请检查后重试";
		}
	}
}else{
	
	if($_FILES["file"]["error"] > 0){
			$matr_update=material_news_update($title,$inserturl,null,$desc,$itemId);
			if($matr_update===false){
				echo "更新失败";
			}else{
				echo "更新成功";
			}
	}else{
		$up=new upphoto();
		$up->get_ph_tmpname($_FILES["file"]["tmp_name"]);
		$up->get_ph_type($_FILES["file"]["type"]);
		$up->get_ph_size($_FILES["file"]["size"]);
		$up->get_ph_name($_FILES["file"]["name"]);
		$up->get_ph_surl("/weChatImageStorage/".$_FILES["file"]["name"]);
		$itemPicUrl=$up->save();
		if($itemPicUrl!=false){
			$matr_update=material_news_update($title,$inserturl,$itemPicUrl,$desc,$itemId);
			if($matr_update===false){
				echo "更新失败";
			}else{
				echo "更新成功";
			}
		}else{
			echo "图片上传错误，可能是空间不足，请检查后重试";
		}
	}
}
?>