<?php
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');

?>

<?php
include '../common/wechat_dbaccessor.php';
$vipmemberId=$_GET["vipmemberId"];
$vipmemberUser=$_POST["vipmember_user"];
$realName=$_POST["real_Name"];
$nickName=$_POST["nick_Name"];
$point=$_POST["point"];
$level=$_POST["level"];
$rtime=$_POST["rtime"];
$mobilenumber=$_POST["mobilenumber"];
$email=$_POST["email"];
$billingplan=$_POST["billing_plan"];
$regtype=$_POST["reg_type"];
$apptype=$_POST["app_type"];
$isaudit=$_POST["isaudit"];

$vipmember_update=web_admin_update_vipmember($realName,$nickName,$point,$level,$rtime,$mobilenumber,$email,$billingplan,$regtype,$apptype,$isaudit,$vipmemberId);
if($vipmember_update===false){
	$hint = array("status"=>"error","message"=>"提交失败");
	echo json_encode($hint);
	exit; 
}else{
	$hint = array("status"=>"success","message"=>"提交成功","url"=>get_bloginfo('template_directory')."/wechat/vipmembermanage/vipmember_list.php?beIframe");
	echo json_encode($hint);
	exit;
}
?>
