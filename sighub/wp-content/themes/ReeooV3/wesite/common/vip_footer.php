<?php 
	$gweidname = get_bloginfo('name','display');
	share_page_in_wechat($gweid, array(
	'title' => $gweidname.'-'.$viptitle,
	'desc' => $gweidname.$viptitle,
	'link' => 'http://'.$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] . '&wxref=mp.weixin.qq.com' ));
?>
</html>