<?php
/**
 *    初始化框架
 *
 *    $sn$
 */
session_start();
define( 'FRAMEWORKPATH', dirname(__FILE__) . '/' );

require_once FRAMEWORKPATH.'/../wp-load.php';

date_default_timezone_set('PRC');

define('IN_IA', true);
define('IA_ROOT', str_replace("\\",'/', dirname(dirname(__FILE__))));
define('MAGIC_QUOTES_GPC', (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) || @ini_get('magic_quotes_sybase'));
define('TIMESTAMP', time());
define('MODULES_DIR',IA_ROOT.'/wp-content/themes/ReeooV3/wechat/');
define('PUBLIC_TEMPLATE_DIR',IA_ROOT.'/wp-content/themes/ReeooV3/');

require_once ABSPATH.'/framework/public/function/router.func.php';
require_once ABSPATH.'/framework/public/function/global.func.php';
require_once ABSPATH.'/framework/public/function/pdo.func.php';
require_once ABSPATH.'/framework/public/moduleEngine/moduleSite.class.php';
require_once ABSPATH.'/framework/public/moduleEngine/WeUtility.class.php';



$_W = $_GPC = array();
global $wpdb;

$_W['config']['db']['tablepre'] = $wpdb -> prefix;
$_W['script_name'] = basename($_SERVER['SCRIPT_FILENAME']);

$_W['charset'] = 'utf8';
if(basename($_SERVER['SCRIPT_NAME']) === $_W['script_name']) {
	$_W['script_name'] = $_SERVER['SCRIPT_NAME'];
} else if(basename($_SERVER['PHP_SELF']) === $_W['script_name']) {
	$_W['script_name'] = $_SERVER['PHP_SELF'];
} else if(isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $_W['script_name']) {
	$_W['script_name'] = $_SERVER['ORIG_SCRIPT_NAME'];
} else if(($pos = strpos($_SERVER['PHP_SELF'],'/' . $scriptName)) !== false) {
	$_W['script_name'] = substr($_SERVER['SCRIPT_NAME'], 0, $pos) . '/' . $_W['script_name'];
} else if(isset($_SERVER['DOCUMENT_ROOT']) && strpos($_SERVER['SCRIPT_FILENAME'], $_SERVER['DOCUMENT_ROOT']) === 0) {
	$_W['script_name'] = str_replace('\\', '/', str_replace($_SERVER['DOCUMENT_ROOT'], '', $_SERVER['SCRIPT_FILENAME']));
} else {
	$_W['script_name'] = 'unknown';
}
$_W['script_name'] = htmlspecialchars($_W['script_name']);

$sitepath = substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/'));
$_W['siteroot'] = htmlspecialchars('http://'.$_SERVER['HTTP_HOST'].$sitepath);
if($_SERVER['SERVER_PORT'] != '80' && $_SERVER['SERVER_PORT'] != '88' && empty($_W['platform'])) {
	$_W['siteroot'] .= ":{$_SERVER['SERVER_PORT']}/";
} else {
	$_W['siteroot'] .= '/';
}

$_W['isajax'] = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
$_W['ispost'] = $_SERVER['REQUEST_METHOD'] == 'POST';

foreach($_COOKIE as $key => $value) {
	if(substr($key, 0, $cplen) == $_W['config']['cookie']['pre']) {
		$_GPC[substr($key, $cplen)] = $value;
	}
}
$_GPC = array_merge($_GET, $_POST, $_GPC);
$_GPC = ihtmlspecialchars($_GPC);

defined('IN_SYS') && require ABSPATH.'/framework/bootstarp.sys.inc.php';
defined('IN_MOBILE') && require ABSPATH.'framework/bootstarp.mobile.inc.php';