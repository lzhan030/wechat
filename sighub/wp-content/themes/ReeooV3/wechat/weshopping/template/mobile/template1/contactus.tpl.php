<?php defined('IN_IA') or exit('Access Denied');?><?php  $bootstrap_type = 3;?>
<?php include $this->template('header');?>
<?php include $this->template('common');?>
<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/wechat/weshopping/images/style.css">
<style>
	img{max-width: 100%;}
</style>
<div class="head">
	<a href="javascript:history.back();" class="bn pull-left"><i class="fa fa-angle-left"></i></a>
	<span class="title">小店介绍</span>
	<a href="<?php  echo $this->createMobileUrl('list')?>" class="bn pull-right"><i class="fa fa-home"></i></a>
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
<?php include $this->template('footer');?>
<?php include $this->template('footerbar');?>