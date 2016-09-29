<?php
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
function editable_reply_content($openid,$GWEID,$WEID,$edit_reply_id,$type,$keyword = NULL){
	global $wpdb;
	//$name = $wpdb->get_var("SELECT vip.nickname FROM {$wpdb->prefix}wechat_member vip ,{$wpdb->prefix}wechat_member_group vipgroup where vipgroup.from_user= '".$openid."' and vipgroup.GWEID='".$GWEID."' and vipgroup.WEID='".$WEID."' and vipgroup.mid=vip.mid");
	$subscribe_id = $wpdb->get_var("SELECT number FROM {$wpdb->prefix}wechat_fans WHERE `WEID`='{$WEID}' AND from_user='{$openid}'");
	$X = $x = $openid;
	$Y = $y = $subscribe_id?$subscribe_id:0;
	$Z = $z = time();
	$S = $s = NULL;
	$L = $l = "青岛";
	$A = $a = "20";
	$code = $wpdb -> get_row("SELECT * FROM {$wpdb -> prefix}wechat_editablereply WHERE `edit_reply_id` = '{$edit_reply_id}'",ARRAY_A);
	if($code['edit_reply_activity'])
		$code['edit_reply_code'] = 'return '.$code['edit_reply_code'].';';
	$text = $code['edit_reply_textstart'].eval(stripslashes($code['edit_reply_code'])).$code['edit_reply_textend'];
	return $text;
}
function editable_reply_exists($openid,$GWEID,$WEID,$type,$keyword = NULL){
	global $wpdb;
	//$name = $wpdb->get_var("SELECT vip.nickname FROM {$wpdb->prefix}wechat_member vip ,{$wpdb->prefix}wechat_member_group vipgroup where vipgroup.from_user= '".$openid."' and vipgroup.GWEID='".$GWEID."' and vipgroup.WEID='".$WEID."' and vipgroup.mid=vip.mid");
	$subscribe_id = $wpdb->get_var("SELECT number FROM {$wpdb->prefix}wechat_fans WHERE `WEID`='{$WEID}' AND from_user='{$openid}'");
	$subscribe_id = $subscribe_id?$subscribe_id:0;
	$timestamp = time();
	$type_codes = array('autorep' => 4,'nokeyword' => 2,'keyword' =>1);
	$condition = " (`edit_reply_type` & {$type_codes[$type]}) = {$type_codes[$type]} ".($type == 'keyword'?"AND `edit_reply_keyword` = '{$keyword}' ":'');
	$reply_rows = $wpdb -> get_results("SELECT * FROM {$wpdb -> prefix}wechat_editablereply WHERE `GWEID` = '{$GWEID}' AND {$condition}",ARRAY_A);
	//return "SELECT * FROM {$wpdb -> prefix}wechat_editablereply WHERE `GWEID` = '{$GWEID}' AND {$condition}";
	if(empty($reply_rows))
		return false;
	foreach($reply_rows as $reply_row){
		$conditions = unserialize($reply_row['edit_reply_condition']);
		if(!is_array($conditions))
			continue;
			return $reply_row['edit_reply_id'];
	}
	return FALSE;
}