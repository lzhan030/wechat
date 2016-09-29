<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/button.css" />
<style>
	.button-circle{font-size:14px;font-family:"微软雅黑";}
</style>
<div class="main_auto">
	<div class="main-title">
		<div class="title-1">当前位置：<a href="<?php echo $this->createWebUrl('index',array());?>">微商城</a> > <a href="<?php echo $this->createWebUrl('weshoptemselect',array('gweid' => $gweid));?>">微商城基本设置</a> > <font class="fontpurple">微商城高级设置</font>
		</div>
	</div>
	<div style="margin-top:50px;">
		<article id="main" style="border-bottom-color: #ffffff;padding-left:0px;">
			<article>
			<?php  if ($slide == 1 || $slides == 1) { ?> 
				<section class="post-1301 post type-post status-publish format-standard sticky hentry">
					<article class="thumb">
						<a href="<?php echo $this->createWebUrl('Adv',array('gweid' => $gweid));?>" class="button button-circle button-flat-caution"><span class="glyphicon glyphicon-film"></span>&nbsp幻灯片设置</a>
					</article>
				</section>
			<?php  } ?> 
				<section class="post-1301 post type-post status-publish format-standard sticky hentry">
					<a href="<?php echo $this->createWebUrl('Shopdescription',array('gweid' => $gweid));?>" class="button button-circle button-flat-primary"><span class="glyphicon glyphicon-inbox"></span>&nbsp小店介绍</a>
				</section>
			</article>
		</article>	
	</div>
</div>
