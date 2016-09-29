<?php 

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
get_header(); 
?>

<?php
include '../common/dbaccessor.php';
$postid=$_GET["postid"];
echo $postid;
$old_content = web_admin_get_post($postid);
$old_content = $old_content['post_content'];
file_unlink_from_xml($old_content);
web_admin_delete_post($postid);

?>