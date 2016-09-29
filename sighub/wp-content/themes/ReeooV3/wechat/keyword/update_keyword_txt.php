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
	//先通过unescape解码js传递过来的escape编码后的内容
	$content=stripslashes($_REQUEST['content']);
	$keywordId=$_GET["keywordId"];
	$keyword=wechat_keyword_get($keywordId);
    if($content==null||$content=="")
    { echo '不能为空';exit;  }
	foreach ($keyword as $key) {
		/*if exist weChat_text, then update the table wp_wechat_material_text*/
		if ($key->arply_type == "weChat_text"){
			$text_id = $key->arplymesg_id;
			$updaterlt=wechat_autrplay_text_update($content, $text_id);
		} else if ($key->arply_type == "weChat_news"){
		/*if exist weChat_news, then insert the table wp_wechat_material_text and update arply_type & arplymesg_id at table wp_wechat_autoreply*/
			//判断是否是分组管理员中的用户
			$groupadminflag = web_admin_issuperadmin($current_user->ID);
			$currentuser =  ((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($groupadminflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
			//$currentuser= (is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
			$insert_id=wechat_autrplay_text_insert_group('keyword', $content,$currentuser, $_SESSION['GWEID']);
			$updaterlt=wechat_autrplay_acty($insert_id, 'weChat_text', $keywordId);
		}
	}
    echo '更新成功';
?>
