<?php defined('IN_IA') or exit('Access Denied');?>	

</div>
<?php 
	share_page_in_wechat($_GET['gweid'], array(
	'title' => (empty($title)) ? $gweidname.'-'.$_W['account']['name'] : $gweidname.'-'.$title,
	'desc' => "抢红包活动，期待您的参与！",
	'link' => 'http://'.$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] . '&wxref=mp.weixin.qq.com' ));
?>
</body>
</html>