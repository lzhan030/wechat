<?php
//订单查询
//添加每 10分钟 的计划任务库600
function yue_more_reccurences($a) {
    $a['onehour'] = array('interval' => ORDER_QUERY, 'display' => 'Every one_hour');
    return  $a;
}

add_filter('cron_schedules', 'yue_more_reccurences');


//添加回调钩子
if ( !wp_next_scheduled( 'yue_one_hour_hook' ) ) {
	wp_schedule_event( time(), 'onehour', 'yue_one_hour_hook' );
}

// 把函数注册进钩子
add_action( 'yue_one_hour_hook', 'yue_one_hour_function' );
function yue_one_hour_function() {
  // 这每10分钟执行一次
	require 'framework/bootstrap.inc.php';
	$module_name = "wepay";
	$module_site = WeUtility::createModuleSite($module_name);
	$module_site -> inMobile = true;
	$module_site -> module['dir'] = MODULES_DIR.$module_name.'/';
	$module_site -> module['name'] = $module_name;
	$module_site ->wechatOrderSearch();
}

//收货确认
function wepay_delivery_reccurences($delivery) {
    $delivery['delivery'] = array('interval' => 86400, 'display' => 'Every one_hour');
    return  $delivery;
}

add_filter('cron_schedules', 'wepay_delivery_reccurences');


//添加回调钩子
if ( !wp_next_scheduled( 'delivery_hook' ) ) {
	wp_schedule_event( time(), 'delivery', 'delivery_hook' );
}

// 把函数注册进钩子
add_action( 'delivery_hook', 'wepay_delivery_function' );
function wepay_delivery_function() {
	require 'framework/bootstrap.inc.php';
	$module_name = "wepay";
	$module_site = WeUtility::createModuleSite($module_name);
	$module_site -> inMobile = true;
	$module_site -> module['dir'] = MODULES_DIR.$module_name.'/';
	$module_site -> module['name'] = $module_name;
	$module_site ->wechatDeliveryConfirmed();
}


if ( !wp_next_scheduled( 'twicedaily_hook' ) ) {
	wp_schedule_event( time(), 'twicedaily', 'twicedaily_hook' );
}

add_action( 'twicedaily_hook', 'wepay_update_function' );
function wepay_update_function() {
	require 'framework/bootstrap.inc.php';
	$module_name = "wepay";
	$module_site = WeUtility::createModuleSite($module_name);
	$module_site -> inMobile = true;
	$module_site -> module['dir'] = MODULES_DIR.$module_name.'/';
	$module_site -> module['name'] = $module_name;
	$module_site ->refundStatusUpdate();
}
?>
