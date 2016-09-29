<?php

	require_once './wp-content/themes/ReeooV3/wesite/common/dbaccessor.php';
	require_once './wp-content/themes/admin/cgi-bin/zipfile_download.php';

	global $wpdb;

	if(isset($_GET['id']) && !empty($_GET['id']) ){
		$templateid = $_GET['id'];
		$removed = web_admin_deletenewtemplate($templateid);
		if($removed) 
			echo "删除成功！";
		else
			echo "删除失败！";
	} else {
		echo "删除失败！";
	}

?>