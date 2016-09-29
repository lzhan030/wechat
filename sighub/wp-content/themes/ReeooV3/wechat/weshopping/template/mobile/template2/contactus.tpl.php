<?php defined('IN_IA') or exit('Access Denied');?><?php  $bootstrap_type = 3;?>
<?php include $this -> template('header');?>
<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/wechat/weshopping/css/style.css">
<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory'); ?>/wechat/weshopping/css/base.css">
<style>
    body{font-size: 14px;}
	img{max-width: 100%;}
	ol li, ul li{list-style: inherit;}
</style>

<div id="top" style="margin-top: -45px;">
    <div class="header-title1">小店介绍</div>
	<div class="site-nav">
		<ul class="fix">
			<li class="home"><a href="<?php echo $this->createMobileUrl('list', array('gweid' => $gweid))?>">首页</a></li>
			<li class="mysn"><a href="<?php echo $this->createMobileUrl('contactUs', array('gweid' => $gweid))?>">小店介绍</a></li>
			<li class="mycart"><a href="<?php echo $this->createMobileUrl('mycart', array('gweid' => $gweid))?>">购物车</a></li>
			<li class="allcate"><a href="<?php  echo $this->createMobileUrl('allcategories', array('gweid' => $gweid))?>">商品分类</a></li>
		</ul>
	</div>
</div>
<div class="tabbable" style="padding-bottom:30px;">
	<div class="tab-content">
		<div class="tab-pane active" id="tab1">
			<div class="mobile-div img-rounded" style="text-align: center; padding:10px;font-weight:bold;overflow:hidden;word-break:break-all">
			<?php  if(!empty($img)) { ?>
				<img src="<?php  echo $img;?>" width="100%" />
			<?php  } ?>
			<?php  if(!empty($name)) { ?>
				<br/><br/><?php  echo $name;?>
			<?php  } else { ?>
				<br/><br/>小店介绍
			<?php  } ?>
			</div>
			<div class="mobile-div img-rounded" style="padding:0 15px;">
				<?php  if(!empty($phone)) { ?>
					<a href="tel:<?php  echo $phone;?>" class="mobile-li"><i class="fa fa-hand-up pull-right"></i>电话： <?php  echo $phone;?> (点击拨号)</a><br>
				<?php  } ?>
				<?php  if(!empty($address)) { ?>
					<a href="http://api.map.baidu.com/geocoder?address=<?php  echo $address;?>&output=html" class="mobile-li"><i class="fa fa-hand-up pull-right"></i>地址：<?php  echo $address;?> (点击查看地图)</a> <br>
				<?php  } ?>
				<?php  if(!empty($email)) { ?>
					<a href="mailto:<?php  echo $email;?>" class="mobile-li"><i class="fa fa-hand-up pull-right"></i>电子邮箱： <?php  echo $email;?> </a><br>
				<?php  } ?>
				<?php  if(!empty($site)) { ?>
					<a href="<?php  echo stripos($site, 'http')===FALSE?'http://'.$site:$site;?>" class="mobile-li"><i class="fa fa-hand-up pull-right"></i>官方网址： <?php  echo stripos($site, 'http')===FALSE?'http://'.$site:$site;?> </a><br>
				<?php  } ?>
			</div>
			<?php  if(!empty($description)) { ?>
			<div class="mobile-div img-rounded " style='overflow:hidden;word-break:break-all;padding:15px;'>
				<?php  echo $description;?>
			</div>
			<?php  } ?>
		</div>
	</div>
</div>
<?php include $this -> template('footer');?>