
<?php

$tmp_path = explode ( 'ReeooV3', __FILE__ );
$template_path=$tmp_path[0];
require_once $template_path.'ReeooV3/wechat/common/session.php';

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
require_once '../wesite/common/dbaccessor.php';
global $wpdb;
global  $current_user;
$userid = $current_user -> ID;
$gweid = $_SESSION['GWEID'];
//加上这个代码，从js传参数过来
?>

<?php
global $wpdb;

$vericode = $_GET["vericode"];
$wid = $_GET["wid"];
$weid = $_GET["weid"];
if($vericode!=null){
	$vericodecounts=web_admin_pubvericode_count($vericode, $wid, $weid);
	
	foreach($vericodecounts as $vericodecount){
		$count=$vericodecount->accountCount;
	}
	if($count>=1)
	{
	  echo "验证码添加重复，请重新添加";
	}
	else
	  echo "可以添加";
}
	
?>