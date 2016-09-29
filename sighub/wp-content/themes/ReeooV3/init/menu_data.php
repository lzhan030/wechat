<?php
$tmp_path = explode ( 'ReeooV3', __FILE__ );
$template_path=$tmp_path[0];
require_once $template_path.'ReeooV3/wechat/common/session.php';

include '../wechat/common/wechat_dbaccessor.php';
?>

<?php
$mid=$_GET["mid"];
$results = array();
$menuList=wechat_public_menu_list($mid);
foreach($menuList as $menu){
	$nodes = array();
	$nodes['id'] = $menu->menu_id;
	$nodes['name'] = $menu->menu_name;
	$nodes['pid'] = $menu->parent_id;
	//$nodes['type'] = $menu->menu_type;

	array_push($results,$nodes);
}

echo json_encode($results);
?>