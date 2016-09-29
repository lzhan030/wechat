<?php
/**
 *    访客手机页面入口
 *
 *    $sn$
 */
 
define('IN_MOBILE', true);
require 'framework/bootstrap.inc.php';

$module_name = $_GPC['module'];
$method = 'doMobile'.ucfirst($_GPC['do']);

$module_site = WeUtility::createModuleSite($module_name);
$module_site -> inMobile = true;
$module_site -> module['dir'] = MODULES_DIR.$module_name.'/';
$module_site -> module['name'] = $module_name;


global $_W;

$result=pdo_fetchall("SELECT * FROM wp_wechat_func_info a WHERE NOT EXISTS(SELECT * FROM wp_wechat_initfunc_info b1 WHERE a.func_name=b1.func_name AND type = 'GWEID' AND value = ".$_W['gweid']." AND func_flag = 0) LIMIT 0, 100");	
foreach($result as $initfunc){
	if($selCheck[$initfunc['func_name']] == 0)
		$selCheck[$initfunc['func_name']] = $initfunc['status'];
}

if($module_name=="weSchool"){
	if(($selCheck['wechatvip']!=1)&&($selCheck['wechatschool']!=1)){
	?>
		<script>
			location.href="<?php echo get_bloginfo('template_directory')?>/../ReeooV3/wesite/common/perdenied.php?weid=<?php echo $weid;?>&gweid=<?php echo $_W['gweid'];?>#wechat_redirect";
		</script>
	<?php exit();
	}
}
if($module_name=="research"){
if(($selCheck['wechatvip']!=1)&&($selCheck['wechatresearch']!=1)){
?>
	<script>
		location.href="<?php echo get_bloginfo('template_directory')?>/../ReeooV3/wesite/common/perdenied.php?weid=<?php echo $weid;?>&gweid=<?php echo $_W['gweid'];?>#wechat_redirect";
	</script>
	<?php exit();
}}

if (method_exists($module_site, $method)) {
	exit($module_site->$method());
} else {
	exit("访问的方法 {$method} 不存在.");
}
?>

