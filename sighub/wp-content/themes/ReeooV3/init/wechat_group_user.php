<?php
$tmp_path = explode ( 'ReeooV3', __FILE__ );
$template_path=$tmp_path[0];
require_once $template_path.'ReeooV3/wechat/common/session.php';
require_once '../wesite/common/dbaccessor.php';
include '../wechat/common/wechat_dbaccessor.php';
?>

<?php
$groupid = $_POST["id"];
$results = array();
$userList=wechat_group_user_list($groupid);
foreach($userList as $user){
	$nodes = array();
	$nodes['id'] = $user->id;
	$nodes['name'] = $user->name;
	array_push($results,$nodes);
}
echo json_encode($results);

?>