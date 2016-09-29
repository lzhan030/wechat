<?php defined('IN_IA') or exit('Access Denied');?><?php  $upload =wp_upload_dir();
$bootstrap_type = 3;?>
<?php include $this->template('header');?>
<style type='text/css'>
	.sel { background:#e9342a; color:#fff;}
	.nosel { background:#fff;color:#000}
</style>
<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/wechat/weshopping/images/style.css">
<div class="head">
	<a href="javascript:history.back();" class="bn pull-left"><i class="fa fa-angle-left"></i></a>
	<span class="title">我的订单</span>
	<a href="<?php  echo $this->createMobileUrl('mycart')?>" class="bn pull-right"><i class="fa fa-shopping-cart"></i></a>
</div>
 <div class="myoder img-rounded" style='text-align:center;color:#aaa;padding:5px;'>
	<div style='float:left;height:23px;margin:auto;width:100%;'>
		<div <?php  if($status=="PAYING" || $status=="NOTPAY") { ?>class='sel'<?php  } else { ?>class="nosel"<?php  } ?> style='border-top-left-radius: 5px;border-bottom-left-radius:5px;border:1px solid #e9342a;text-align: center;float:left;width:33.3%' onclick="location.href='<?php  echo $this->createMobileUrl('myorder',array('status'=>"PAYING"))?>'">
			待支付
		</div>
		<div <?php  if(($status=="SUCCESS" && $delivery_status == "1") || $status=="SELFDELIVERY" || $status=="CASHONDELIVERY") { ?>class='sel'<?php  } else { ?>class="nosel"<?php  } ?> style='border:1px solid #e9342a;margin-left:-1px;float:left;width:33.3%;text-align: center;' onclick="location.href='<?php  echo $this->createMobileUrl('myorder',array('status'=>"SUCCESS", "delivery_status"=>"1"))?>'">
			待收货
		</div>
		<div <?php  if($status=="SUCCESS" && $delivery_status == "2") { ?>class='sel'<?php  } else { ?>class="nosel"<?php  } ?> style='border-top-right-radius: 5px;margin-left:-1px;border-bottom-right-radius:5px;text-align: center;border:1px solid #e9342a;float:left;width:33.3%' onclick="location.href='<?php  echo $this->createMobileUrl('myorder',array('status'=>"SUCCESS", "delivery_status"=>"2"))?>'">
			已完成
		</div>
	</div>
	<!--<a style='float:right; display:inline-block; height:23px; line-height:23px;' href="<?php  echo $this->createMobileUrl('address',array('from'=>'confirm'))?>">管理收货地址</a>-->
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
			<?php  }else if($item['trade_state'] == "SUCCESS" && $item['delivery_status'] == "0" ) { ?>
			<span class="text-warning">已付款</span>
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
			<?php  }else if($item['trade_state'] == "SUCCESS" ) { ?>
			<span class="text-warning">已付款</span>
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
<?php include $this->template('footer');?>
<?php include $this->template('footerbar');?>