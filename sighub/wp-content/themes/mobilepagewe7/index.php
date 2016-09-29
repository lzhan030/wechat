<?php
global $_W,$wpdb;


session_start();
require_once 'wp-content/themes/ReeooV3/wesite/common/dbaccessor.php';
require_once 'wp-content/themes/ReeooV3/wechat/common/wechat_dbaccessor.php';
//if(!isset($_GET['weid'])||!isset($_GET['fromuser']))
//{
  //  $weid = $_SESSION['WECID'];
//	$fromuser = $_SESSION['fromuser'];
//}
//else
//{
    /*20140430$weid =  $_GET['weid'];
    $fromuser = $_GET['fromuser'];20140430*/
	//$_SESSION['WECID']=$weid;
	//$_SESSION['fromuser']=$fromuser;
//}


/**
*@author: janeen
*@version: add by janeen 20140430
*/
/**
*@function: get
*/
global $gweid;
$gweid =  $_GET['gweid'];
$siteId = $_GET['site'];
/**
*@function:封装gweid
*/
if(!empty($siteId)){
	$site=web_admin_get_site($siteId);
	foreach($site as $siteinfo){
		$userid=$siteinfo->site_user;
		$gweid=$siteinfo->GWEID;
	}

	//20150417 sara new added
	//根据当前的gweid去查找有没有处在共享虚拟号下，如果是虚拟号下的，需要将gweid换为虚拟号的gweid
	$gweid = virtualgweid_open($gweid);
}



define('IN_IA',true);
define('IN_MOBILE', true);
require "./framework/bootstrap.inc.php";

if (!empty($_GPC['styleid'])) {
	$_W['account']['styleid'] = $_GPC['styleid'];
	$_W['account']['template'] = pdo_fetchcolumn("SELECT name FROM ".tablename('site_templates')." WHERE id = '{$_W['account']['styleid']}'");
}

$site_id = intval($_GET['site']);

$_W['site_id'] = $site_id;
$position = 1;
$title = $_W['account']['name'] . '微站';
$navs = mobile_nav($position);
$template = $wpdb -> get_var("SELECT site_value FROM {$wpdb -> prefix}orangesitemeta WHERE `site_id`={$site_id} AND `site_key` = 'we7templatestyle'");
$_W['styles'] = mobile_styles($site_id,$template);
$template_url = get_bloginfo('template_url').'/template/'.$template;
include './wp-content/themes/mobilepagewe7/template/'.$template.'/index.tpl.php';
get_footer(); 
?>