<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
 global $wpdb, $table_prefix,$gweid;
?>
<?php 
	$sharetitle = get_bloginfo('name','display');
	share_page_in_wechat($gweid, array(
	'title' => $sharetitle,
	'desc' => "微官网活动，期待您的参与！",
	'link' => 'http://'.$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] . '&wxref=mp.weixin.qq.com' ));
?>
</body>
</html>
