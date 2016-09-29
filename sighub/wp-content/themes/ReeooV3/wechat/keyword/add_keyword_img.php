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
	include 'keyword_permission_check.php';
	$news_item_id=$_GET["news_item_id"];
	$keyword=$_GET["keyword"];
    if(empty($keyword) || empty($news_item_id))
    {echo'不能为空';exit;}
	$arr = wechat_mess_kw_isExistInDB_group($keyword,$_SESSION['GWEID']);
	foreach($arr as $arraynumber){
        $count_number=$arraynumber->arrayCount;
    }
    if($count_number >0) {
        echo "添加失败，已有关键字";
        exit;
    }
    //判断是否是分组管理员中的用户
	$groupadminflag = web_admin_issuperadmin($current_user->ID);
	$currentuser =  ((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($groupadminflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
	//$currentuser= (is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
	$insertrlt=wechat_mess_kw_add_group('weChat_news',$news_item_id,$keyword,$currentuser,$_SESSION['GWEID']);
	if ($insertrlt==null) {
		echo "添加失败！";
	} else {
		echo "添加成功！";
	}
?>
