<?php defined('IN_IA') or exit('Access Denied');?><?php  $bootstrap_type = 3;?>
<?php include $this -> template('header');?>
<?php include $this -> template('common');?>
<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory'); ?>/wechat/weshopping/css/base.css?v=<?php echo time();?>">
<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory'); ?>/wechat/weshopping/css/base2013.css"> <!--use css from another template-->
<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory'); ?>/wechat/weshopping/css/index.css">
<style>
	body{
	    font-size:15px;
	}
</style>
<div id="top">
	<div class="back-ui-a">
	<a href="javascript:history.back(1)">返回</a>
	</div>
	<div class="header-title">商品分类</div>
	<div class="site-nav">
		<ul class="fix">
			<li class="home"><a href="<?php echo $this->createMobileUrl('list', array('gweid' => $gweid))?>">首页</a></li>
			<li class="mysn"><a href="<?php echo $this->createMobileUrl('contactUs', array('gweid' => $gweid))?>">小店介绍</a></li>
			<li class="mycart"><a href="<?php echo $this->createMobileUrl('mycart', array('gweid' => $gweid))?>">购物车</a></li>
			<li class="allcate"><a href="<?php  echo $this->createMobileUrl('allcategories', array('gweid' => $gweid))?>">商品分类</a></li>
		</ul>
	</div>
</div>
<div class="line"></div>

<div class="cate-list">
	<ul class="fix">
		<?php  if(is_array($category)) { foreach($category as $row) { ?>
		<li>
			<dl>
				<dt class="list-ui-div"><a href="<?php  echo $this->createMobileUrl('list2', array('pcate' => $row['id'], 'gweid' => $gweid))?>"><?php  echo $row['name'];?></a></dt>
				<dd>
					<ul class="fix">	
                    <?php  if(is_array($children[$row['id']])) { foreach($children[$row['id']] as $row) { ?>						
						<li><a href="<?php  echo $this->createMobileUrl('list2', array('ccate' => $row['id'], 'gweid' => $gweid))?>"><?php  echo $row['name'];?></a></li>
					<?php  } } ?>
					</ul>
				</dd>	
			</dl>
		</li>
		<?php  } } ?>
	</ul>
</div>
<?php include $this -> template('footer');?>