<?php defined('IN_IA') or exit('Access Denied');?>
<?php 
	$gweidname = get_bloginfo('name','display');
	$sharelink = 'http://'.$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] . '&wxref=mp.weixin.qq.com';
		if(!empty($link))
			$sharelink = $link;
	share_page_in_wechat($_GET['gweid'], array(
	'title' => $gweidname.' - '.$title,
	'desc' => $content,//var _share_content = _removeHTMLTag("<?php echo $content");
	'link' => $sharelink ));
?>
</body>
</html>