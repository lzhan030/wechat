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
//判断是否是分组管理员中的用户
$groupadminflag = web_admin_issuperadmin($current_user->ID);
$currentuser =  ((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($groupadminflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
//$currentuser= (is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
	
$upload =wp_upload_dir();
$itemId_save=$_POST["itemId_save"];
$itemTitle_save=$_POST["itemTitle_save"];
$itemAbs_save=$_POST["itemAbs_save"];
$itemDes_save=$_POST["itemDes_save"];
$itemUrl_save=$_POST["itemUrl_save"];
$picUrl_save=$_POST["picUrl_save"];
$newsId_save=$_POST["newsId_save"];

$news_name=$_POST["material_name"];

echo $newsId_save;
$itemIdAry=explode('|',$itemId_save);
$itemTitleAry=explode('|',$itemTitle_save);
$itemAbsAry=explode('|',$itemAbs_save);
$itemDesAry=explode('|',$itemDes_save);
$itemUrlAry=explode('|',$itemUrl_save);
$picUrlAry=explode('|',$picUrl_save);

$itemIdCot=count($itemIdAry);
if($newsId_save==0){
	$newmax=material_news_getmax();
	foreach($newmax as $nm){
		$newsItemId=$nm->maxnid+1;
	}
	
	for($i=0;$i<$itemIdCot-1;$i++){		
		/*截取后入数据库*/
		$tmp = stristr($picUrlAry[$i],$upload['baseurl']);
		if($tmp===false){
			$insertPicUrl=$picUrlAry[$i];
		}else{
			$str = stristr($picUrlAry[$i], $upload['baseurl']);
			$postion=intval($str)+intval(strlen($upload['baseurl']));
			$insertPicUrl=substr($picUrlAry[$i], $postion);		
		}
		/*如果包含homeurl，则截取后入数据库*/
		$tmp = stristr($itemUrlAry[$i],home_url());
		if($tmp===false){
			$inserturl=$itemUrlAry[$i];
		}else{
			$str = stristr($itemUrlAry[$i], home_url());
			$postion=intval($str)+intval(strlen(home_url()));
			$inserturl=substr($itemUrlAry[$i], $postion);		
		}
		
		$matr_add=material_news_add_group($itemTitleAry[$i],$inserturl,$insertPicUrl,$itemAbsAry[$i],$itemDesAry[$i],$newsItemId,$currentuser,$news_name,$_SESSION['GWEID']);
		
	}
	
}else{
	$newsList=material_news_get($newsId_save);
	foreach($newsList as $news){
		$fordel=true;
		for($i=0;$i<$itemIdCot-1;$i++){
			if($itemIdAry[$i]>0){
				if($news->news_id==$itemIdAry[$i]){
					$fordel=false;
				}
									
			}
		}
		if($fordel){
			$del=material_news_delete("",$news->news_id);
		}
	}
	for($i=0;$i<$itemIdCot-1;$i++){
		$tmp = stristr($picUrlAry[$i],$upload['baseurl']);
		if($tmp===false){
			$insertPicUrl=$picUrlAry[$i];
		}else{
			$str = stristr($picUrlAry[$i], $upload['baseurl']);
			$postion=intval($str)+intval(strlen($upload['baseurl']));
			$insertPicUrl=substr($picUrlAry[$i], $postion);		
		}
		/*如果包含homeurl，则截取后入数据库*/
		$tmp = stristr($itemUrlAry[$i],home_url());
		if($tmp===false){
			$inserturl=$itemUrlAry[$i];
		}else{
			$str = stristr($itemUrlAry[$i], home_url());
			$postion=intval($str)+intval(strlen(home_url()));
			$inserturl=substr($itemUrlAry[$i], $postion);		
		}
		if($itemIdAry[$i]<=0||$itemIdAry[$i]==""){
			$matr_add=material_news_add_group($itemTitleAry[$i],$inserturl,$insertPicUrl,$itemAbsAry[$i],$itemDesAry[$i],$newsId_save,$currentuser,$news_name,$_SESSION['GWEID']);
		}
		if(material_item_get($itemIdAry[$i])){
			$updsuc=material_news_update($itemTitleAry[$i],$inserturl,$insertPicUrl,$itemAbsAry[$i],$itemDesAry[$i],$itemIdAry[$i],$news_name);
		}		
	}
}
?>