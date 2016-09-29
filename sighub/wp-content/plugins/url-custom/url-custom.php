<?php
/*
Plugin Name: url-custom
Plugin URI: 
Description: 商家自定义URL模块支持插件
Version: 1.0
Author: Orange
*/

/*
add_action('init', 'url_custom_load_rules');
function url_custom_load_rules(){
	
	//}
}
*/
global $wpdb;

function load_custom_modules(){
	//echo $_SERVER['REQUEST_URI'];
	//var_dump($_GET);
	//var_dump('OIUYTYUIO');
	define('IN_MOBILE', true);
	require 'framework/bootstrap.inc.php';

	$method = 'doMobile'.ucfirst($_GET['do']);

	$module_site = WeUtility::createModuleSite($_GET['name']);
	$module_site -> inMobile = true;
	$module_site -> module['dir'] = MODULES_DIR.$_GET['name'].'/';
	$module_site -> module['name'] = $_GET['name'];

	global $_W;
	
	if(empty($url_pattern))
		unset($url_pattern);
	$result=pdo_fetchall("SELECT * FROM wp_wechat_func_info a WHERE NOT EXISTS(SELECT * FROM wp_wechat_initfunc_info b1 WHERE a.func_name=b1.func_name AND type = 'GWEID' AND value = ".$_GET['gweid']." AND func_flag = 0) LIMIT 0, 100");	
	foreach($result as $initfunc){
		if($selCheck[$initfunc['func_name']] == 0)
			$selCheck[$initfunc['func_name']] = $initfunc['status'];
	}


	if (method_exists($module_site, $method)) {
		exit($module_site->$method());
	} else {
		exit("访问的方法 {$method} 不存在.");
	}
}




function do_not_load_template($template){
	return false;
}

if ( isset($_SERVER['PATH_INFO']) )
	$pathinfo = $_SERVER['PATH_INFO'];
else
	$pathinfo = '';
$pathinfo_array = explode('?', $pathinfo);
$pathinfo = str_replace("%", "%25", $pathinfo_array[0]);
$req_uri = $_SERVER['REQUEST_URI'];
$req_uri_array = explode('?', $req_uri);
$req_uri = $req_uri_array[0];
$self = $_SERVER['PHP_SELF'];
$home_path = parse_url(home_url());
if ( isset($home_path['path']) )
	$home_path = $home_path['path'];
else
	$home_path = '';
$home_path = trim($home_path, '/');


$req_uri = str_replace($pathinfo, '', $req_uri);
$req_uri = trim($req_uri, '/');
$req_uri = preg_replace("|^$home_path|", '', $req_uri);
$req_uri = trim($req_uri, '/');
$pathinfo = trim($pathinfo, '/');
$pathinfo = preg_replace("|^$home_path|", '', $pathinfo);
$pathinfo = trim($pathinfo, '/');
$_SERVER['WP_QUERY_URI'] = $req_uri . $pathinfo;

$url_mapping_list = $wpdb -> get_results("SELECT * FROM {$wpdb -> prefix}url_pattern_mapping WHERE type='action' AND valid=1", ARRAY_A);
if(is_array($url_mapping_list))
	foreach($url_mapping_list as $url_mapping_element){
		if(empty($url_mapping_element['url_pattern']) || empty($url_mapping_element['module']) )
			continue;
			$url_mapping_element['url_pattern'] = trim($url_mapping_element['url_pattern'],'/');
		if($url_mapping_element['url_pattern']==$_SERVER['WP_QUERY_URI']){
			$_GET['name'] = $url_mapping_element['module'];
			$_GET['do'] = $url_mapping_element['value'];
			$_GET['gweid'] = $url_mapping_element['GWEID'];
			add_filter( 'template_include', 'do_not_load_template', 10, 1 );
			add_action('wp_loaded','load_custom_modules');
			break;
		}
	}

	
$url_mapping_list_normal = $wpdb -> get_results("SELECT * FROM {$wpdb -> prefix}url_pattern_mapping WHERE type='base' AND valid=1", ARRAY_A);
if(is_array($url_mapping_list))
	foreach($url_mapping_list_normal as $url_mapping_element){
		if(empty($url_mapping_element['url_pattern']) || empty($url_mapping_element['module']) )
			continue;
			$url_mapping_element['url_pattern'] = trim($url_mapping_element['url_pattern'],'/');
		if($url_mapping_element['url_pattern']==$_SERVER['WP_QUERY_URI']){
			$_GET['name'] = $url_mapping_element['module'];
			$_GET['gweid'] = $url_mapping_element['GWEID'];
			add_filter( 'template_include', 'do_not_load_template', 10, 1 );
			add_action('wp_loaded','load_custom_modules');
			break;
		}
	}