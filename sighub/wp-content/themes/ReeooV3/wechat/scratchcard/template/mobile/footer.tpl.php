<?php defined('IN_IA') or exit('Access Denied');?>	
</div>
<?php 
$gweidname = get_bloginfo('name','display');
share_page_in_wechat($gweid, array(
	'title' => (empty($title)) ? $gweidname.'-'.$_W['account']['name'] : $gweidname.'-'.$title,
	'desc' => "刮刮卡抽奖活动，期待您的参与！",
	'link' => 'http://'.$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] . '&wxref=mp.weixin.qq.com' ));
?>
</body>
</html>