<?php
$tmp_path = explode ( 'ReeooV3', __FILE__ );
$template_path=$tmp_path[0];
require_once $template_path.'ReeooV3/wechat/common/session.php';
require_once '../wesite/common/dbaccessor.php';
include '../wechat/common/wechat_dbaccessor.php';
?>

<?php
$gweid = $_POST["id"];
$flag = $_POST["status"];
$weid = $_POST["WEID"];

$result=web_admin_changegroupshare($gweid,$weid,$flag);

?>