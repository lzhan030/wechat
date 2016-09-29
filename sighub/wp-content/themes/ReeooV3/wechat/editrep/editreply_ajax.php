<?php
$tmp_path = explode ( 'ReeooV3', __FILE__ );
$template_path=$tmp_path[0];
require_once $template_path.'ReeooV3/wechat/common/session.php';

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
global  $current_user, $wpdb;
if( !isset($current_user->user_login)|| empty($current_user->user_login)){
		wp_redirect(wp_login_url());
}	
include '../common/wechat_dbaccessor.php';
include 'editreply_permission_check.php';
$action = $_GET['action'];
if($action == 'add'){
	$edit_reply_type = 0;
	if(is_array($_POST['edit_reply_type']))
		foreach($_POST['edit_reply_type'] as $reply_type){
			if($reply_type == 'autorep')
				$edit_reply_type |= 4;
			if($reply_type == 'nokeyword')
				$edit_reply_type |= 2;	
			if($reply_type == 'keyword')
				$edit_reply_type |= 1;
		}

	$edit_reply_condition = is_array($_POST['edit_reply_condition_key']) ? $_POST['edit_reply_condition_key'] : array();
			
			
	$data = array(
		'edit_reply_name' => $_POST['edit_reply_name'],
		'edit_reply_type' => $edit_reply_type,
		'edit_reply_keyword' => $_POST['edit_reply_keyword'],
		'edit_reply_condition' => serialize($edit_reply_condition),
		'edit_reply_code' => $_POST['edit_reply_code'],
		'edit_reply_activity' => $_POST['edit_reply_activity'],
		'edit_reply_textstart' => $_POST['edit_reply_textstart'],
		'edit_reply_textend' => $_POST['edit_reply_textend'],
		'GWEID' => $_SESSION['GWEID'],
	);
	$insert_result = $wpdb -> insert($wpdb -> prefix.'wechat_editablereply',$data);
	$result = array('status' => 'error');
	if($insert_result>0){
		$result['status'] = 'success';
		$result['id'] = $wpdb->insert_id;
		$result['name'] = $_POST['edit_reply_name'];
	}

	echo json_encode($result);
}
if($action == 'get'){
	$reply_id = intval($_POST['reply_id']);
	$edit_reply_record = $wpdb -> get_row("SELECT * FROM {$wpdb -> prefix}wechat_editablereply WHERE edit_reply_id={$reply_id}",ARRAY_A);
	$edit_reply_record['edit_reply_condition'] = unserialize($edit_reply_record['edit_reply_condition']);
	$edit_reply_record['edit_reply_code'] = stripslashes($edit_reply_record['edit_reply_code']);
	echo json_encode($edit_reply_record);
}
if($action == 'update'){
	$reply_id = intval($_GET['reply_id']);
	$edit_reply_type = 0;
	if(is_array($_POST['edit_reply_type']))
		foreach($_POST['edit_reply_type'] as $reply_type){
			if($reply_type == 'autorep')
				$edit_reply_type |= 4;
			if($reply_type == 'nokeyword')
				$edit_reply_type |= 2;	
			if($reply_type == 'keyword')
				$edit_reply_type |= 1;
		}
	$edit_reply_condition = is_array($_POST['edit_reply_condition_key']) ? $_POST['edit_reply_condition_key'] : array();
			
			
	$data = array(
		'edit_reply_name' => $_POST['edit_reply_name'],
		'edit_reply_type' => $edit_reply_type,
		'edit_reply_keyword' => $_POST['edit_reply_keyword'],
		'edit_reply_condition' => serialize($edit_reply_condition),
		'edit_reply_code' => $_POST['edit_reply_code'],
		'edit_reply_activity' => $_POST['edit_reply_activity'],
		'edit_reply_textstart' => $_POST['edit_reply_textstart'],
		'edit_reply_textend' => $_POST['edit_reply_textend'],
		'GWEID' => $_SESSION['GWEID'],
	);
	$update_result = $wpdb -> update($wpdb -> prefix.'wechat_editablereply',$data,array('edit_reply_id' => $reply_id));
	$result = array('status' => 'error');
	if($update_result!==FALSE){
		$result['status'] = 'success';
		$result['id'] = $reply_id;
		$result['name'] = $_POST['edit_reply_name'];
	}

	echo json_encode($result);
}
if($action == 'delete'){
	$reply_id = intval($_POST['reply_id']);
	$delete_result = $wpdb -> delete($wpdb -> prefix.'wechat_editablereply',array('edit_reply_id' => $reply_id));
	$result = array('status' => 'error');
	if($delete_result!==FALSE){
		$result['status'] = 'success';
		$result['id'] = $reply_id;
	}

	echo json_encode($result);
}
 ?>