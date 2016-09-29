<?php 
	$gweidname = get_bloginfo('name','display');
	share_page_in_wechat($gweid, array(
	'title' => $gweidname.'-微学校',
	'desc' => "点击进入微学校",
	'link' => 'http://'.$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] . '&wxref=mp.weixin.qq.com' ));
?>