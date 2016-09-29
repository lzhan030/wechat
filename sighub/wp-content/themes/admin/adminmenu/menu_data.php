<?php
	@session_start();

include './wp-content/themes/ReeooV3/wechat/common/wechat_dbaccessor.php';

$mid=$_GET["M_id"];
$results = array();
$menuList=wechat_public_menu_list($mid);
foreach($menuList as $menu){
	$nodes = array();
	$nodes['id'] = $menu->menu_id;
	$nodes['name'] = $menu->menu_name;
	$nodes['pid'] = $menu->parent_id;
	array_push($results,$nodes);
}

echo json_encode($results);
?>