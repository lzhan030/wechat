<?php
	$path = explode ( 'wp-content', __FILE__ );
	$wp_root_path = $path [0];
	require_once ($wp_root_path . '/wp-load.php');
	require_once $wp_root_path.'/wp-content/themes/admin/cgi-bin/virtual_gweid.php';

	$group_name = $_POST['group_name'];
	$group_description =  $_POST['group_description'];
	$sql = $wpdb -> prepare("SELECT COUNT(*) as groupCount FROM ".$wpdb->prefix."group WHERE group_name = %s",$group_name);
	$myrows = $wpdb->get_results($sql);	
	foreach ($myrows as $groupcount) 
	{
		$countgroup = $groupcount -> groupCount;
	}
	
	if ($countgroup == 0) 
	{
		$wpdb->query( $wpdb->prepare("INSERT INTO ".$wpdb->prefix."group"." (group_name, description) VALUES (%s, %s)",$group_name, $group_description));
		$insertRlt = $wpdb->insert_id;
		if($insertRlt) {
			//create virtual account with userid = 0
			$userid = 0;
			$rlt = add_virtual_gweid($userid, $insertRlt);
		}
	}
?>

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>创建新分组</title>
	<link rel="stylesheet" href="../css/wsite.css">
</head>
<body onload="closeit()">
	<div class="mainpop" style="margin-top: 45px; margin-left: 60px;">
		<?php if($countgroup != 0) {?> 
			<h4>分组名已存在，请重新提交！</h4>
		<?php } elseif(!empty($insertRlt)) {?>
			<h4>创建成功!</h4>
		<?php } else {?> 
			<h4>添加失败!</h4>
		<?php } ?>
	</div>
	<script language='javascript'>
		function closeit() {
			top.resizeTo(320, 200); //控制网页显示的大小		
			setTimeout("self.close()", 3000); //毫秒
			opener.location.reload();  //主页面刷新显示
		}
	</script>
</body>
