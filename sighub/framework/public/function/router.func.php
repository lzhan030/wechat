<?php
/**
 *    路由相关公用函数
 *
 *    $sn$
 */
defined('IN_IA') or exit('Access Denied');

function create_url($router, $params = array(),$url_pattern = '') {
	list($module, $controller, $do) = explode('/', $router);
	$queryString = http_build_query($params, '', '&');
	if(isset($url_pattern) && !empty($url_pattern))
		return home_url()."/{$url_pattern}/?". (empty($do) ? '' : 'do='.$do) . '&'. $queryString;
	else
		return home_url().'/'.$module.'.php?module='.$controller . (empty($do) ? '' : '&do='.$do) . '&'. $queryString;
}