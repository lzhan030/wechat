<?php

defined('IN_IA') or exit('Access Denied');

class WesiteModuleSite extends ModuleSite {

	function onWechatAccountDelete($gweid){
		global $wpdb;
		$list = $wpdb -> get_results("SELECT * FROM {$wpdb->prefix}posts WHERE ID IN ( SELECT id FROM {$wpdb->prefix}orangesite WHERE GWEID='{$gweid}')",ARRAY_A);
		if(is_array($list))
			foreach($list as $element){
				file_unlink($element['post_content'] );
			}
	}
}
