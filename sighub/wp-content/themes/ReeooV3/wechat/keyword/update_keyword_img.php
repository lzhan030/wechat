<?php 
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
global $current_user;
?>

<?php
	include '../common/wechat_dbaccessor.php';
	include 'keyword_permission_check.php';
	$news_item_id=$_GET["news_item_id"];
	$keywordId=$_GET["keywordId"];
	$keyword=wechat_keyword_get($keywordId);
    iF(empty($keyword) || empty($news_item_id))
    {echo "不能为空";exit;}
	foreach ($keyword as $key) {
		$updaterlt=wechat_mess_content_update('weChat_news',$news_item_id,$keywordId);
	}
    echo "更新成功";
?>
