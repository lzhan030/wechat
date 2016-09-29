<?php
$tmp_path = explode ( 'ReeooV3', __FILE__ );
$template_path=$tmp_path[0];
require_once $template_path.'ReeooV3/wechat/common/session.php';
require_once '../wesite/common/dbaccessor.php';
include '../wechat/common/wechat_dbaccessor.php';
?>

<?php
$gweid = $_POST["gweid"];
$results = array();
$groupList = wechat_getgroup($gweid);
foreach($groupList as $group){
    $nodes = array();
	if(empty($group->group_id))
	{
	    $nodes['id'] = 0;     //如果一开始并未给用户分配分组，则该值为空，需要判断一下,则其对应的分组为未分组的状态
	}else{
	    $nodes['id'] = $group->group_id;
	}
	
	array_push($results,$nodes);
}

echo json_encode($results);


?>