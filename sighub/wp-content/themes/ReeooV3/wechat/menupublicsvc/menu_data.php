<?php
$tmp_path = explode ( 'ReeooV3', __FILE__ );
$template_path=$tmp_path[0];
require_once $template_path.'ReeooV3/wechat/common/session.php';
include '../common/wechat_dbaccessor.php';

$WEID=$_GET["WEID"];
$results = array();

$menuList=wechat_menu_publicsvc_list($WEID);

foreach($menuList as $menu){
	$nodes = array();
	$nodes['id'] = $menu->menu_id;
	$nodes['name'] = $menu->menu_name;
	$nodes['pid'] = $menu->parent_id;
	$nodes['type'] = $menu->menu_type;
	if($menu->menu_type=="weChat_text" || $menu->menu_type=="weChat_news" ){
		$mk=substr($menu->menu_key,1);
		$nodes['key'] = $mk;
	}else{
		/*如果没有http，证明为内链，加上home_url显示，插入时，判断有再截取入库*/
		$tmp = stristr($menu->menu_key,"http");
		if(($tmp===false)&&(!empty($menu->menu_key))){
			$menuurllink=home_url().$menu->menu_key;
		}else{				
			$menuurllink=$menu->menu_key;
		}
		$nodes['key'] = $menuurllink;
	}
	array_push($results,$nodes);
}

echo json_encode($results);
?>