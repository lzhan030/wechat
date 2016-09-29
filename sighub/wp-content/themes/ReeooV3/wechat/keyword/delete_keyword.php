<?php 
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
?>

<?php
	include '../common/wechat_dbaccessor.php';
	include 'keyword_permission_check.php';
	$apId=$_GET["keywordId"];
	//?这里添加一块，如果是文本的关键词删除，把文本素材表的也删除?

	$del=wechat_mess_kw_delete($apId);
	if($del===false){
		echo "删除失败！";
	}else{
		echo "删除成功！";
	}
?>

<body onload="closeit()">
</body>

<script language='javascript'>

	function closeit() {
		top.resizeTo(300, 200); 		
		setTimeout("self.close()", 2000); 
		opener.location.href='<?php echo constant("CONF_THEME_DIR"); ?>/wechat/keyword/keyword_list.php?beIframe';
	}
</script>