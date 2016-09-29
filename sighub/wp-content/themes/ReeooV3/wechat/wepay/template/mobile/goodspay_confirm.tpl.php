<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html;charset=utf-8"/>
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.css">
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/font-awesome.min.css" />
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/common.mobile.css">
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/messenger.css">
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/messenger-theme-future.css">
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/goodspay.css" />
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/shopping.mobile.css" />
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/jquery-1.11.1.min.js"></script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/bootstrap.js"></script>
		<title>确认订单</title>		
	</head>
	<body>
		<!--head-->
		<div class="head">
			<span class="title">
				<?php if($tradetype->trade_type == 'JSAPI'){?>支付 <?php } if($tradetype->trade_type == 'NATIVE_ORDER') {?>支付二维码生成页面<?php }?>
			</span>
		</div>
		<!--head end-->
		<div class="research" style="margin-top:46px;margin-bottom:40px">
			<div class="mobile-div img-rounded">
				<?php if($tradetype->trade_type == 'JSAPI'){?>
				<div class="mobile-hd">订单编号<?php echo $out_trade_no;?>
					<a  onclick="href='<?php $this->createMobileUrl('goodspayConfirm',array('gweid' => $gweid,'orderid' => $out_trade_no)); ?>'" style="height:14ps;margin-right: 4%;float:right" width="20ps" style="width:70px"><span class="glyphicon glyphicon-refresh"></span></a>
				</div>
				<?php } if($tradetype->trade_type == 'NATIVE_ORDER') {?><div class="mobile-hd">订单编号<?php echo $out_trade_no;?>
				</div><?php }?>
				<?php foreach($goodsarray as $k=>$val){	?>							
				<div>
					<div style=" float:left;width:145px; height:153px;" name="goodsimg">
						<?php if(!empty($val['thumb'])) {?>
							<img class="research-thumb" style="width:100%;height:100%" src="<?php echo $val['thumb']; ?>">		
						<?php }else {?>
							<div class="thumbnail" style="height:150px;"><p>没有图片</p></div>
						<?php }?>
					</div>
					<div style="height:auto;overflow:hidden;" name="goodsinfo">
						<div class="mobile-content" name="goodstitle">
							<h4 style="color:#34495e;"><?php echo $val['title']; ?></h4>
						</div>
						<!--ismanual=1表示手动输入金额，不显示商品单价-->
						<div style="<?php if($val['ismanual']=='1'){ echo 'display:none';} ?>" id="goodsprice" class="mobile-content" name="goodsprice">						
							单价：￥<?php echo number_format($val['market_price'],2,".",""); ?>
						</div>
						<!--ismanual=1表示手动输入金额，不显示购买数量-->
						<div style="<?php if($val['ismanual']=='1'){ echo 'display:none';} ?>" id="goodstotal"  class="mobile-content" name="goodstotal">
							购买数量：<?php echo $ordergoodstotal; ?>
						</div>
						<div id="goodstotalprice"  class="mobile-content" name="goodstotalprice">
							合计：￥<?php echo $ordergoodstotal_price; ?>
						</div>
					</div>
				</div>
				<hr size="1" width="100%" color="black" noshade="noshade"  />
			<?php } ?>
				<!--address,$needdelivery==1表示商品中没有需要发货的，则不显示收货地址-->
				<div style="<?php if($needdelivery==1){ echo 'display:none';} ?>">				
					<div class="mobile-content">姓名：<?php echo $order_address['username'];?></div>
					<div class="mobile-content">电话：<?php echo $order_address['telnumber'];?></div>
					<div class="mobile-content">邮编：<?php echo $order_address['postalcode'];?></div>
					<div class="mobile-content">地区：<?php echo $order_address['stagename'];?></div>
					<div class="mobile-content">地址：<?php echo $order_address['detailinfo'];?></div>
				</div>
				<!--address end-->
				<div>
					<hr size="1" width="100%" color="black" noshade="noshade" style="<?php if($needdelivery==1){ echo 'display:none';} ?>"/>
					<!--$needdiscount==0表示不需要优惠金额-->
					<div style="<?php if(($needdiscount==0)||($tradetype->trade_type == 'NATIVE_ORDER')){ echo 'display:none';} ?>" id="goodsdiscount"  class="mobile-content" name="goodsdiscount">
						优惠金额：<?php echo $discount_fee; ?>
					</div>
					<div  class="mobile-content" name="goodstotalfee">
						支付金额：<font style="font-size:16px;font-weight:bold;" color="#f15a28" >￥</font>
						<input id="goodstotalfee" name="goodstotalfee" style="border:0;font-size:16px;font-weight:bold;color:#f15a28;width:150px;background-color:#fff" disabled="disabled" value="<?php echo number_format($totalfee,2,".","");?>"></input>
					</div>
				</div>
			</div>
			
			<div id="wepay" class="mobile-submit">
				<input type="hidden" name="token" value="" />
				<?php if($tradetype->trade_type == 'JSAPI'){ ?>
				<input type="button" class="btn btn-large btn-success" style="width:100%;" onClick="callpayconfirm('<?php echo number_format($totalfee,2,".","");?>')" value="微信支付"></button><br><br>
				<?php } if($tradetype->trade_type == 'NATIVE_ORDER') {?> 
					<input type="button" class="btn btn-large btn-success" style="width:100%;" onClick="callpayconfirm('<?php echo number_format($totalfee,2,".","");?>')" value="生成支付二维码"></button><br><br>
				<?php } ?>
			</div>			
		</div>
		<div class="footerbar">
			<a style="width:33%;padding-top:15px" onclick="href='<?php echo $this->createMobileUrl('goodsinfo',array('gweid'=>$gweid,'goodsgid' => $goodsgid));?>'">首页</a>
			<a style="width:33%;padding-top:15px" onclick="href='<?php echo $this->createMobileUrl('myorderlist',array('gweid' => $gweid,'goodsgid' => $goodsgid)); ?>'">我的订单</a>
			<a style="width:33%;padding-top:15px" onclick="href='<?php echo $this->createMobileUrl('rightslists',array('gweid' => $gweid,'goodsgid' => $goodsgid));?>'">我的维权</a>
		</div>
		<!--<div id="footer"></div>--> 
	</body>
	<script language='javascript'>
		isSubmitting = false;
		function callpayconfirm(totalfee){
			if(isSubmitting)
				return false;
				isSubmitting = true;
			$.ajax({
				async:false,
				url:window.location.href, 
				type: "POST",
				data:{'order_pay':'isPay'},
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
						// 当微信内置浏览器完成内部初始化后会触发WeixinJSBridgeReady事件。
						if(data.data.trade_type=='JSAPI'){
							WeixinJSBridge.invoke('getBrandWCPayRequest',{
								"appId" : data.jsapi_data.appId, 
								"timeStamp" : data.jsapi_data.timeStamp, //时间戳
								"nonceStr" : data.jsapi_data.nonceStr, //随机串
								"package" : data.jsapi_data.package,
								"signType" : "MD5", //微信签名方式
								"paySign" : data.jsapi_data.paySign//微信签名
							},function(res){
								if(res.err_msg == "get_brand_wcpay_request:ok" ) {
									//ok返回时，向商户后台询问是否收到交易成功的通知，若收到通知，前端展示交易成功的界面
									//若此时未收到通知，调用查询订单接口，查询订单的当前状态，并反馈给前端展示相应的界面
									window.location.href = data.data.resultUrl; 
								}else{
									//WeixinJSBridge.log(res.err_msg);
									//alert("errcode:"+res.err_code+"err_desc:"+res.err_desc+"err_msg:"+res.err_msg);
									if((res.err_msg !="get_brand_wcpay_request:cancel" )&&(res.err_msg !="get_brand_wcpay_request:fail")){
										alert("微信错误:支付权限禁止");
									}
									window.location.href = data.data.resultUrl;
								}			   
							});
						}else if(data.data.trade_type=='NATIVE_ORDER'){
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
								$('#wepay').html('<div class="mobile-div img-rounded"><div class="mobile-hd">支付二维码链接</div>'+'<div class="mobile-content" style="word-break:break-all;"><input class="form-control" readonly="readonly" value="'+data.data.code_url+'" ><div>注:复制该链接到微信任意聊天窗口,点击进入后即可进行支付</div></div></div>');
								$('#wepay').css('margin','0px');
							}
						}					
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
	</script>
<?php  include $this -> template('footer');?>