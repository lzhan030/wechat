<?php defined('IN_IA') or exit('Access Denied');?><div class="list-item img-rounded">
	<div>
		<a href="<?php  echo $this->createMobileUrl('detail', array('id' => $item['id']))?>">
			<img src="<?php  echo $item['thumb']?$_W['attachurl'].$item['thumb']:'';?>">
		</a>
		<span class="title"><a href="<?php  echo $this->createMobileUrl('detail', array('id' => $item['id']))?>"><?php  echo $item['title'];?></a><?php  if($item['type'] == '2') { ?>(虚拟)<?php  } ?></span>
	</div>
	<span class="sold">
		<!--买家输入的不需要显示已售出多少件-->
		<?php if($item['ismanual']=='0'){ ?>
		<span class="soldnum pull-left">已售<?php echo $item['sales'];?>件</span>
		<?php } ?>
		<?php if($item['ismanual']=='1'){ ?>
		<span class="price pull-right">自定义金额</span>
		<?php }else{ ?>
		<span class="price pull-right"><?php  echo $item['market_price'];?>元</span>
		<?php } ?>

		<!--<span class="price pull-right"><?php  echo $item['marketprice'];?>元 <?php  if($item['unit']) { ?> / <?php  echo $item['unit'];?><?php  } ?></span>-->
	</span>
	<!--<div class="add-cart" onclick="order.add(<?php  echo $item['id'];?>)"><i class="fa fa-shopping-cart"></i> 添加到购物车</div>-->
</div>