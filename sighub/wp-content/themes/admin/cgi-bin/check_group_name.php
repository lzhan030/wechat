<?php
	$path = explode ( 'wp-content', __FILE__ );
	$wp_root_path = $path [0];
	require_once ($wp_root_path . '/wp-load.php');	

	$group_name = $_GET['name'];
	if (empty($group_name))
	{
		echo "";
	}
	$sql = $wpdb -> prepare("SELECT COUNT(*) as groupCount FROM ".$wpdb->prefix."group WHERE group_name = %s",$group_name);
	$myrows = $wpdb->get_results($sql);
	foreach($myrows as $m) 
	{
		if(!empty($m)) 
		{
			$gc = $m->groupCount;
		}
	}
	if ($gc == 0) 
	{
		echo "<font color=#008FFF>&nbsp;可以使用!</font>";
	}
	else
	{
		echo "<font color=red>&nbsp;该分组名已存在!</font>";
	} 
?>
