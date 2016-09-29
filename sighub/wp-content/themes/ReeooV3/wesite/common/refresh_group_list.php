<?php
$tmp_path = explode ( 'ReeooV3', __FILE__ );
$template_path=$tmp_path[0];
require_once $template_path.'ReeooV3/wechat/common/session.php';
require_once 'dbaccessor.php';

?>

<?php
$results = array();
$groupList = wechat_getgroup_list();
foreach($groupList as $group){
    $nodes = array();
	$nodes['id'] = $group->id;
	$nodes['name'] = $group->name;
	array_push($results,$nodes);
}

echo json_encode($results);


?>