<?php defined('IN_IA') or exit('Access Denied');?>
<?php  
	$bootstrap_type = 3;
	$upload =wp_upload_dir();
?>
<?php include $this->template('header');?>
<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/wechat/weshopping/images/style.css?1">
<div class="head">
	<a href="javascript:history.back();" class="bn pull-left"><i class="fa fa-angle-left"></i></a>
	<span class="title">订单详情</span>
	<a href="<?php  echo $this->createMobileUrl('mycart')?>" class="bn pull-right"><i class="fa fa-shopping-cart"></i></a>
</div>

<div class="myoder img-rounded">
	<div class="myoder-hd">
		<span class="pull-left">订单编号：<?php  echo $item['out_trade_no'];?></span>
		<span class="pull-right"><?php  echo $item['time_start'];?></span>
	</div>
	<?php  if(is_array($goods)) { foreach($goods as $g) { ?>
	<div class="myoder-detail">
		<a href="<?php  echo $this->createMobileUrl('detail', array('id' => $g['id']))?>"><img src="<?php if(empty($g['thumb'])|| (stristr($g['thumb'],"http")!==false)){ echo $g['thumb'];}else{echo $upload['baseurl'].$g['thumb'];} ?>" width="160"></a>
		<div class="pull-left">
			<div class="name"><a href="<?php  echo $this->createMobileUrl('detail', array('id' => $g['id']))?>"><?php  echo $g['title'];?></a></div>
			<div class="price">
				<span style="width:100%">单价：<?php  echo $g['market_price'];?> 元<?php  if($g['unit'] && ($g['ismanual'] == 0)) { ?> / <?php  echo $g['unit'];?><?php  } ?></span>
				<!-- point update -->
				<span style="width:100%;<?php if($g['point']!=0 && $g['ismanual'] !=1){ }else{?> display:none; <?php } ?>">
					<span style="width:100%">积分：<?php  echo $g['point'];?> 分<?php  if($g['unit'] && ($g['ismanual'] == 0)) { ?> / <?php  echo $g['unit'];?><?php  } ?><?php if($g['ispoint']!=0 && $g['ismanual']!= 1){ echo "(使用积分购买)"; } ?></span>
				</span>
				<!-- point update end-->
				<?php  //echo $g['id'];?>
				<?php if($g['ismanual'] == 0){?><br/><span class="num" style="width:100%">数量：<?php  echo $g['total'];?>  <?php  if($g['unit']) { ?> <?php  echo $g['unit'];?><?php  } ?></span><?php }?>
			</div>
		</div>
	</div>
	<?php  } } ?>
	<div class="myoder-express">
		<span class="express-company">状态</span>
		<span class="express-num">
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
	<div class="myoder-express">
		<span class="express-company">配送方式</span>
		<span class="express-num"><?php  echo $dispatch['dispatchname'];?></span>
	</div>
	<?php  if(($delivery_status=='1') && !empty($delivery_sn)) { ?>
	<div class="myoder-express">
		<span class="express-company">快递: <?php  echo $delivery_compid;?></span>
		<span class="express-num">
			单号: <?php  echo $delivery_sn;?>
		</span>
	</div>
	<div class="myoder-total" style="display:none;">
		<a href="http://m.kuaidi100.com/index_all.html?type=<?php  echo $item['express'];?>&postid=<?php  echo $delivery_sn;?>#input" class="btn btn-success pull-right btn-sm" >查看快递</a>
	</div>
	<?php  } ?>
	<?php  if(!empty($remark)) { ?>
	<div class="myoder-express" style='margin-top:10px;'>
		<span class="express-company">快递备注</span>
	</div>
	<div style='float:left;margin:10px;overflow:hidden;word-break:break-all;width:100%;'>
	   <pre style="border:none;color:#b9b9b9;background:#FFF;font-weight: bolder;font-family:'Microsoft YaHei UI', '微软雅黑', '宋体'" class="mobile-content"><?php echo $remark;?></pre>
	</div>
	<?php  } ?>
	<?php  if(!empty($item['remark'])) { ?>
		<div class="myoder-express">
			<span class="express-company">留言</span>
			<div style='float:left;margin:10px;overflow:hidden;word-break:break-all;width:90%;color:#b9b9b9;'>
				<pre style="border:none;color:#b9b9b9;background:#FFF;font-weight: bolder;font-family:'Microsoft YaHei UI', '微软雅黑', '宋体'" class="mobile-content"><?php echo $item['remark'];?></pre>
			</div>
		</div>
	<?php  } ?>	
	<div class="myoder-total">
		<span>共计：<span class="false">
					<?php  if($item['dispatchprice']<=0) { ?>
						<?php  echo $item['fee'];?> 元
					<?php  } else { ?>
						<?php  echo $item['fee'];?> 元 <?php if($item['isdispatchpoint']!=-2){?>(含运费 <?php  echo $item['dispatchprice'];?> 元) <?php } ?> 
					<?php  } ?>
					</span><!-- point update -->
		</span>
		<br/>
		<span>
			积分：<span class="false">
				<!-- point update -->
				<?php  if($item['dispatchprice']<=0) { ?>
				<?php  echo intval($item['pointall']);?> 分
				<?php  } else { ?>
				<?php  echo intval($item['pointall']);?> 分 <?php if($item['isdispatchpoint']==-2){?> (运费积分 <?php  echo intval($item['dispatchprice']);?> 分)<?php } ?>
				<?php  } ?></span>
				<!-- point update END-->
		</span>
		<br/>
		<!--订单中全为手动输入金额的商品不显示优惠金额-->
		<span style="<?php if(($needdiscount==0)||(empty($goodsid))){ echo 'display:none';} ?>">优惠：
			<span class="false">
				<?php echo $discount_fee; ?>元
			</span>
		</span>
		<!--退款信息-->
		<div>
			<hr size="1" width="100%" color="black" noshade="noshade" />
			
			<?php
			if(is_array($refund_list) && !empty($refund_list)){
				foreach($refund_list as $refund_element){ ?>
				<div class="mobile-content" >
				<?php echo $this -> REFUND_REASON[$refund_element['reason']]; ?>:
				￥<?php echo number_format($refund_element['refund_fee'],2,'.',''); ?>:
				<?php echo $this -> REFUND_STATUS[$refund_element['refund_status']]; ?>
				</div>
			<?php }
			} else { ?>
				<div class="mobile-content" >该订单无退款记录！</div>
		<?php } ?>
		</div>
		<div style="height:30px;">
		<?php  if($item['payment_type'] != 3 && $item['status'] == 0 && !in_array($item['trade_state'], array('CASHONDELIVERY','SELFDELIVERY','SUCCESS'))) { ?>
		<!--<a href="<?php  echo $this->createMobileUrl('pay', array('orderid' => $item['id']))?>" class="btn btn-danger pull-right btn-sm">立即支付</a>-->
		<a onClick="callpayconfirm('<?php echo $item['out_trade_no']; ?>',this)" style="margin-bottom:10px;" class="btn btn-danger pull-right btn-sm">立即支付</a>
		<?php  } ?>
		<?php  //if($item['status'] == 2 || ($item['payment_type'] == 3 && $item['status'] < 3)) { ?>
		<?php if($delivery_status=='1'){?>
		<a href="<?php  echo $this->createMobileUrl('myorder', array('orderid' => $item['out_trade_no'], 'op' => 'confirm'))?>" class="btn btn-success pull-right btn-sm" style="margin-bottom:10px;" onclick="return confirm('点击确认收货前，请确认您的商品已经收到。确定收货吗？'); ">确认收货</a>
		<?php  } ?>
		<?php 
		//获得交易完成时间，并转换为时间戳
		$endtime=$item['time_end'];
		$enddate = strtotime($endtime);
		//获得申请维权时间与之前交易完成时间的时间间隔
		$days=round(($currentdate-$enddate)/3600/24);
		//判断申请维权时间与之前交易完成时间的时间间隔是否小于45天
		//if(in_array($item['trade_state'], array("SUCCESS","SELFDELIVERY","CASHONDELIVERY"))){ 
		if(in_array($item['trade_state'], array("SUCCESS"))){ 
			if($days<45 && !$order_rightscounts) {?>
			<a onclick="href='<?php echo $this->createMobileUrl('rightsdetails',array('gweid' => $gweid,'out_trade_no' => $item['out_trade_no']));?>'" style="margin-right:3px;margin-bottom:10px;" class="btn btn-success pull-right btn-sm">维权</a>
			<?php }if($days<45 && $order_rightscounts){?>
			<a onclick="href='<?php echo $this->createMobileUrl('rightsdetails',array('gweid' => $gweid,'out_trade_no' => $item['out_trade_no']));?>'" style="margin-right:3px;margin-bottom:10px;" class="btn btn-success pull-right btn-sm" disabled="disabled">正在维权</a>
			<?php }
		} else{ if(($delivery_status!='1')&&($delivery_status!='2')){?>
					<!--没有支付成功，并且不是已发货和确认收货的就可以取消订单-->
					<a onclick="orderdel('<?php echo $item['out_trade_no']; ?>')" style="margin-right:3px;margin-bottom:10px" class="btn btn-success pull-right btn-sm">取消订单</a>
				<?php } ?>		
		<?php } ?>
		</div>
	</div>
</div>
<script>
document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
	WeixinJSBridge.call('hideOptionMenu');
});
isSubmitting = false;
function callpayconfirm(orderid,obj){
	if(isSubmitting)
		return false;
		isSubmitting = true;
	$.ajax({
		async:false,
		url:'<?php echo $this -> createModuleMobileUrl('wepay','JSAPIPayOrder',array('gweid' => $_GET['gweid']));?>', 
		type: "POST",
		data:{'order_pay':'isPay','orderid':<?php echo $orderid;?>,'openid':"<?php echo $_W['fans']['from_user'] ?>"},
		success: function(data){
			if (data.status == 'error'){
				if(data.message=='invalid total_fee'){
					alert("金额过大,暂不支持支付");
				}else{
					//alert(data.message);
					alert("支付失败,请稍后重试");
				}
			}else if (data.status == 'errordec'){
					alert(data.message);
			}else if (data.status == 'success'){
				
				location.href = '<?php echo $this -> createModuleMobileUrl('wepay','WeShoppingJSAPIPayOrder',array('gweid' => $_GET['gweid'],'orderid' => $orderid ));?>&prepay_id='+data.prepay_id;				
			}
			isSubmitting = false;
		},
		 error: function(data){
			alert("出现错误,请重试");
			isSubmitting = false;
		},
		dataType: 'json'
	});
}
function orderdel(out_trade_no){
	if(isSubmitting)
		return false;
		isSubmitting = true;
	if(confirm("确定取消订单吗？")){
		$.ajax({
			async:false,
			url:window.location.href, 
			type: "POST",
			data:{'order_del':'isDel','out_trade_no':out_trade_no},
			success: function(data){
				if (data.status == 'error'){
					alert(data.message);
				}else if (data.status == 'success'){
					location.href=data.url;
				}
				isSubmitting = false;
			},
			 error: function(data){
				alert("出现错误,请重试");
				isSubmitting = false;
			},
			dataType: 'json'
		});	
	}else{
		isSubmitting = false;
	}
}
</script>
<?php include $this->template('footer');?>
<?php include $this->template('footerbar');?>