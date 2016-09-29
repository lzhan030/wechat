<?php defined('IN_IA') or exit('Access Denied');?>
	</div>
	<style>
		h5{color:#555;}
	</style>
	<?php 
	$gweidname = get_bloginfo('name','display');
	$sharelink = 'http://'.$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] . '&wxref=mp.weixin.qq.com';
	if(empty($_share['link']))
		$_share['link']= $sharelink;
	share_page_in_wechat($_GET['gweid'], $_share);
	?>
</body>
</html>
