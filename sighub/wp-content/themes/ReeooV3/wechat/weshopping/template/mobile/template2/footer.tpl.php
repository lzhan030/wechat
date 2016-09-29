<?php defined('IN_IA') or exit('Access Denied');?>		<?php  if(empty($footer_off)) { ?>
			<div class="text-center footer" style="margin:10px 0; width:100%; text-align:center; word-break:break-all;">
				<?php  if(!empty($_W['page']['footer'])) { ?>
					<?php  echo $_W['page']['footer'];?>
				<?php  } else { ?>
					<?php  if(IMS_FAMILY != 'x') { ?>
					<?php  } ?>
				<?php  } ?>
				&nbsp;&nbsp;<?php  echo $_W['setting']['copyright']['statcode'];?>
			</div>
			<!--template2 footer-->
			<div class="line"></div>
			<div id="footer">
				<div class="layout fix user-info">
					<div class="user-name fl" id="footerUserName">
						当前用户: <font class="blue"><?php if(!empty($buyer)){echo $buyer;}else{echo "未登陆";}?></font>
					</div>
					<div class="fr"><a id="backTop" href="javascript:window.scrollTo(0,0);">回顶部</a></div>
				</div>
				<ul class="list-ui-a">
					<li>
						<div class="w user-login">
							<?php if(empty($buyer)){?>
								<a href="<?php echo home_url().'/wp-content/themes/ReeooV3/wesite/common/vip_login.php?gweid='.$_W['gweidv'].'&redirect_url='.urlencode('http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"]); ?>">登录</a>
								<a href="<?php echo home_url().'/wp-content/themes/ReeooV3/wesite/common/vip_register.php?gweid='.$_W['gweidv'].'&redirect_url='.urlencode('http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"]); ?>">注册</a>
							<?php }?>
							<!--<a href="<?php echo $this->createMobileUrl('mycart', array('gweid' => $gweid))?>">购物车</a>-->
							<a href="<?php echo $this->createMobileUrl('myorder', array('gweid' => $gweid))?>">我的订单</a>
						</div>
					</li>
				</ul>
			</div>
		<?php  } ?>
		<?php  if(!empty($_W['quickmenu']['menus']) && empty($_W['quickmenu']['disabled'])) { ?>
			<?php include_once template($_W['quickmenu']['template'], TEMPLATE_INCLUDEPATH);?>
		<?php  } ?>
	</div>
	<style>
		h5{color:#555;}
	</style>
	<script type="text/javascript">
	    //对分享时的数据处理
		function _removeHTMLTag(str) {
			str = str.replace(/<script[^>]*?>[\s\S]*?<\/script>/g,'');
			str = str.replace(/<style[^>]*?>[\s\S]*?<\/style>/g,'');
			str = str.replace(/<\/?[^>]*>/g,'');
			str = str.replace(/\s+/g,'');
			str = str.replace(/&nbsp;/ig,'');
			return str;
		}
	</script>
	<?php 
	$gweidname = get_bloginfo('name','display');
	$sharelink = 'http://'.$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] . '&wxref=mp.weixin.qq.com';
	if(empty($_share['link']))
		$_share['link']= $sharelink;
	share_page_in_wechat($_GET['gweid'], $_share);
?>
</body>
</html>
