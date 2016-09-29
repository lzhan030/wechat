
<?php

require_once './wp-content/themes/ReeooV3/wesite/common/dbaccessor.php';
//加上这个代码，从js传参数过来

global $wpdb;

$nicename = $_GET["nicename"];
if($nicename!=null){
	$accountcounts=web_admin_pubaccount_count($nicename);
	foreach($accountcounts as $accountcount){
		$count=$accountcount->accountCount;
	}
	if($count>=1)
	{
	  echo "微信昵称添加重复，请重新添加";
	}
	else
	  echo "可以添加";
}
	
?>