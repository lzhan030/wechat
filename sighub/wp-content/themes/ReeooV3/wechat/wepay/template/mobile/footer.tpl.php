<?php 
	$gweidname = get_bloginfo('name','display');
	share_page_in_wechat($gweid, array(
	'title' => (empty($shoppingtitle)) ? $gweidname.'-'.$_W['account']['name'] : $gweidname.'-'.$shoppingtitle,
	'desc' => $gweidname."微商城",
	'link' => 'http://'.$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] . '&wxref=mp.weixin.qq.com' ));
?>
</body>
</html>