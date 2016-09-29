<?php
/**
 *    商家用户PC页面入口
 *
 *    $sn$
 */
 
define('IN_SYS', true);
require 'framework/bootstrap.inc.php';

session_start();
$loginurl=home_url().'login/';

//if( !isset($_SESSION['WEID']) ){
if( !isset($_SESSION['GWEID']) ){//20140625 janeen update
	echo <<<EOT
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <script>
        alert("您的登录已超时，请重新登录");
		window.parent.location.href="{$loginurl}";
	</script>
	</head>
<body>
</body>
</html>
EOT;

exit;
}

if( !isset($current_user->user_login)|| empty($current_user->user_login)){
		wp_redirect(home_url().'/login');
}

$module_name = $_GPC['module'];
$method = 'doWeb'.ucfirst($_GPC['do']);

$module_site = WeUtility::createModuleSite($module_name);
$module_site -> module['dir'] = MODULES_DIR.$module_name.'/';
$module_site -> module['name'] = $module_name;
$module_site -> inMobile = false;

$result=pdo_fetchall("SELECT * FROM wp_wechat_func_info a WHERE NOT EXISTS(SELECT * FROM wp_wechat_initfunc_info b1 WHERE a.func_name=b1.func_name AND type = 'GWEID' AND value = ".$_SESSION['GWEID']." AND func_flag = 0) LIMIT 0, 100");	
foreach($result as $initfunc){
	if($selCheck[$initfunc['func_name']] == 0)
		$selCheck[$initfunc['func_name']] = $initfunc['status'];
}

if($module_name=="weSchool"){
if(($selCheck['wechatvip']!=1)&&($selCheck['wechatschool']!=1)){?>
	<script>
		location.href="<?php echo get_bloginfo('template_directory')?>/../ReeooV3/wesite/common/perdenied_forweb.php?weid=<?php echo $weid;?>&gweid=<?php echo $_SESSION['GWEID'];?>#wechat_redirect";
	</script>
	<?php exit();
}}

if($module_name=="research"){
if(($selCheck['wechatvip']!=1)&&($selCheck['wechatresearch']!=1)){?>
	<script>
		location.href="<?php echo get_bloginfo('template_directory')?>/../ReeooV3/wesite/common/perdenied_forweb.php?weid=<?php echo $weid;?>&gweid=<?php echo $_SESSION['GWEID'];?>#wechat_redirect";
	</script>
	<?php exit();
}}

if (method_exists($module_site, $method)) {
	exit($module_site->$method());
} else {
	exit("访问的方法 {$method} 不存在.");
}

?>
