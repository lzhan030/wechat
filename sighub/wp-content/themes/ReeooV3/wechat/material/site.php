<?php
/**
 * 砸蛋抽奖模块
 *
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */
defined('IN_IA') or exit('Access Denied');

class MaterialModuleSite extends ModuleSite {

	function onWechatAccountDelete($gweid){
		global $wpdb;
		$list = $wpdb -> get_results("SELECT * FROM {$wpdb->prefix}wechat_material_news WHERE GWEID='{$gweid}'",ARRAY_A);
		if(is_array($list))
			foreach($list as $element){
				file_unlink($element['news_item_picurl']);
				file_unlink_from_xml(str_replace('\"', '"', str_ireplace('../', '', str_ireplace('../uploads', '', $element['news_item_description']))));
			}
				
	}

}
