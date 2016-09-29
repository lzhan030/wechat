<?php
session_start();
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');

global $wpdb;

$wpdb -> insert('wp_test',array('text' => file_get_contents("php://input")));
$data = json_decode(file_get_contents("php://input"),true);
if(!empty($data) && ($data['code']==0||$data['code']==4)){
	$pkey = $data['id'];
	foreach($data['items'] as $item)
		if($item['code']==0){
			$url = 'http://wevideo.qiniudn.com/'.$item['key'];
			$wpdb->replace($wpdb->prefix.'video',array('persistentId' => $pkey, 'url' => $url));		
		}
}