<?php defined('IN_IA') or exit('Access Denied');?>
<?php  
	$bootstrap_type = 3;
	$upload =wp_upload_dir();
?>
<?php include $this -> template('header');?>

<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/wechat/weshopping/css/style.css?v=<?php echo time();?>">
<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory'); ?>/wechat/weshopping/css/base.css?v=<?php echo time();?>">
<style type='text/css'>
	body{font-size:14px;}
	.sel {background:#FF5858; color:#fff;}
	.nosel {background:#fff;color:#000;}
	.price {color:#222;}
</style>
<div id="top" style="margin-top: -45px;">
    <div class="header-title1">我的订单</div>
	<div class="site-nav">
		<ul class="fix">
			<li class="home"><a href="<?php echo $this->createMobileUrl('list', array('gweid' => $gweid))?>">首页</a></li>
			<li class="mysn"><a href="<?php echo $this->createMobileUrl('contactUs', array('gweid' => $gweid))?>">小店介绍</a></li>
			<li class="mycart"><a href="<?php echo $this->createMobileUrl('mycart', array('gweid' => $gweid))?>">购物车</a></li>
			<li class="allcate"><a href="<?php  echo $this->createMobileUrl('allcategories', array('gweid' => $gweid))?>">商品分类</a></li>
		</ul>
	</div>
</div>
<div class="myoder img-rounded" style='text-align:center;color:#aaa;padding:5px;'>
	<div style='float:left;height:23px;margin:auto;width:100%;'>
		<div <?php  if($status=="PAYING" || $status=="NOTPAY") { ?>class='sel'<?php  } else { ?>class="nosel"<?php  } ?> style='border-top-left-radius: 5px;border-bottom-left-radius:5px;border:1px solid #FF5858;text-align: center;float:left;width:33.3%;height: 23px;padding-top: 2px;' onclick="location.href='<?php  echo $this->createMobileUrl('myorder',array('status'=>"PAYING"))?>'">
			待支付
		</div>
		<div <?php  if(($status=="SUCCESS" && $delivery_status == "1") || $status=="SELFDELIVERY" || $status=="CASHONDELIVERY") { ?>class='sel'<?php  } else { ?>class="nosel"<?php  } ?> style='border:1px solid #FF5858;margin-left:-1px;float:left;width:33.3%;text-align: center;height: 23px;padding-top: 2px;' onclick="location.href='<?php  echo $this->createMobileUrl('myorder',array('status'=>"SUCCESS", "delivery_status"=>"1"))?>'">
			待收货
		</div>
		<div <?php  if($status=="SUCCESS" && $delivery_status == "2") { ?>class='sel'<?php  } else { ?>class="nosel"<?php  } ?> style='border-top-right-radius: 5px;margin-left:-1px;border-bottom-right-radius:5px;text-align: center;border:1px solid #FF5858;float:left;width:33.3%;height: 23px;padding-top: 2px;' onclick="location.href='<?php  echo $this->createMobileUrl('myorder',array('status'=>"SUCCESS", "delivery_status"=>"2"))?>'">
			已完成
		</div>
	</div>
</div>

<?php  if(count($list)<=0) { ?>
<div class="myoder img-rounded" style='text-align:center;color:#aaa;padding:30px;'>
	您暂时没有任何订单!
</div>
<?php  } ?>
<div style='margin-bottom:40px;'>
<?php  if(is_array($list)) { foreach($list as $item) { ?>
<div class="myoder img-rounded">
	<div class="myoder-hd">
		<span class="pull-left">订单编号：<?php  echo $item['out_trade_no'];?></span>
		<span class="pull-right"><?php  echo $item['time_start'];?>
		<?php  if($item['payment_type'] == 3) { ?>
			<?php  if($item['trade_state'] == "CLOSED") { ?>
			<span class="text-muted">订单取消</span>
			<?php  } else if($item['trade_state'] = "PAYING") { ?>
			<span class="text-danger">未付款</span>
			<?php  } else if($item['trade_state'] == "NOTPAY") { ?>
			<span class="text-warning">未支付</span>
			<?php  }else if($item['trade_state'] == "SUCCESS" && $item['delivery_status'] == "1" ) { ?>
			<span class="text-warning">已发货</span>
			<?php  } else if($item['trade_state'] == 'SELFDELIVERY') { ?>
			<span class="text-warning">自提</span>
			<?php  } else if($item['trade_state'] == 'CASHONDELIVERY') { ?>
			<span class="text-warning">货到付款</span>
			<?php  } else { ?>
			<span class="text-success">已完成</span>
			<?php  } ?>
		<?php  } else { ?>
			<?php  if($item['trade_state'] == "CLOSED") { ?>
			<span class="text-muted">订单取消</span>
			<?php  } else if($item['trade_state'] == "PAYING") { ?>
			<span class="text-danger">未付款</span>
			<?php  } else if($item['trade_state'] == "NOTPAY") { ?>
			<span class="text-warning">未支付</span>
			<?php  } else if($item['trade_state'] == "SUCCESS" && $item['delivery_status'] == "1" ) { ?>
			<span class="text-warning">已发货</span>
			<?php  } else if($item['trade_state'] == 'SELFDELIVERY') { ?>
			<span class="text-warning">自提</span>
			<?php  } else if($item['trade_state'] == 'CASHONDELIVERY') { ?>
			<span class="text-warning">货到付款</span>
			<?php  } else { ?>
			<span class="text-success">已完成</span>
			<?php  } ?>
		<?php  } ?>
		</span>
	</div>
	<?php  if(is_array($item['goods'])) { foreach($item['goods'] as $goods) { ?>
	<div class="myoder-detail">
		<a href="<?php  echo $this->createMobileUrl('detail', array('id' => $goods['id']))?>"><img src="<?php if(empty($goods['thumb'])|| (stristr($goods['thumb'],"http")!==false)){ echo $goods['thumb'];}else{echo $upload['baseurl'].$goods['thumb'];} ?>" width="160"></a>
		<div class="pull-left">
			<div class="name"><a href="<?php  echo $this->createMobileUrl('detail', array('id' => $goods['id']))?>"><?php  echo $goods['title'];?></a></div>
			<div class="price">
				<span style="width:100%">单价：<?php  echo $goods['market_price'];?> 元<?php  if($goods['unit'] && ($goods['ismanual'] == 0)) { ?> / <?php  echo $goods['unit'];?><?php  } ?></span>
			</div>
			<!-- point update -->
			<div class="price" style="<?php if($goods['point']!=0 && $goods['ismanual'] !=1){}else{?> display:none; <?php } ?>">
				<span style="width:100%">积分：<?php  echo $goods['point'];?> 分<?php  if($goods['unit'] && ($goods['ismanual'] == 0)) { ?> / <?php  echo $goods['unit'];?><?php  } ?></span>
			</div>
			<!-- point update end-->
			<div class="price">	
				<?php if($goods['ismanual'] == 0){?><span style="width:100%">数量：<?php  echo $goods['total'];?><?php  if($goods['unit']) { ?> <?php  echo $goods['unit'];?><?php  } ?></span><?php }?>
			</div>
		</div>
	</div>
	<?php  } } ?>
	<div class="myoder-total" style="height:81px;"><!--point update-->
		<span>
			共计：<span class="false">
				<?php  if($item['dispatchprice']<=0) { ?>
				<?php  echo $item['fee'];?> 元
				<?php  } else { ?>
				<?php  echo $item['fee'];?> 元 <?php if($item['isdispatchpoint']!=-2){?> (运费 <?php  echo $item['dispatchprice'];?> 元) <?php } ?>
				<?php  } ?></span><!-- point update -->
		</span>
		<br/>
		<span>
			积分：<span class="false">
				<?php  if($item['dispatchprice']<=0) { ?>
				<?php  echo intval($item['pointall']);?> 分
				<?php  } else { ?>
				<?php  echo intval($item['pointall']);?> 分 <?php if($item['isdispatchpoint']==-2){?> (运费积分 <?php  echo intval($item['dispatchprice']);?> 分)<?php } ?>
				<?php  } ?></span>
				<!-- point update END-->
		</span>
		<br/>
		<div style="height:30px;">
			<a href="<?php  echo $this->createMobileUrl('myorder', array('orderid' => $item['out_trade_no'], 'op' => 'detail'))?>" class="btn btn-success pull-right btn-sm" >订单详情</a>
			<?php 
			//获得交易完成时间，并转换为时间戳
			$endtime=$item['time_end'];
			$enddate = strtotime($endtime);
			//获得申请维权时间与之前交易完成时间的时间间隔
			$days=round(($currentdate-$enddate)/3600/24);
			//判断申请维权时间与之前交易完成时间的时间间隔是否小于45天
			if($item['trade_state']=="SUCCESS"){ 
				if($days<45 && !$item['rightscounts']) {?>
				<a onclick="href='<?php echo $this->createMobileUrl('rightsdetails',array('gweid' => $gweid,'out_trade_no' => $item['out_trade_no']));?>'" style="margin-right:3px;" class="btn btn-success pull-right btn-sm">维权</a>
				<?php }if($days<45 && $item['rightscounts']){?>
				<a onclick="href='<?php echo $this->createMobileUrl('rightsdetails',array('gweid' => $gweid,'out_trade_no' => $item['out_trade_no']));?>'" style="margin-right:3px;" class="btn btn-success pull-right btn-sm" disabled="disabled">正在维权</a>
				<?php }
			} ?>
		</div>
	</div>
</div>
<?php  } } ?></div>
<?php include $this -> template('footer');?>