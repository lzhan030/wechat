<?php defined('IN_IA') or exit('Access Denied'); ?>
<?php include $this -> template('header'); ?>
<html>
	<head>
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.css">
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/font-awesome.min.css" />
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/common.mobile.css">
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/messenger.css">
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/messenger-theme-future.css">
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/goodspay.css" />
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/shopping.mobile.css" />
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/jquery-1.11.1.min.js"></script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/bootstrap.js"></script>
		<title>订单信息</title>		
	</head>
	<body>
		<div class="head">
			<span class="title">订单详情</span>
		</div>
		<div class="research" style="margin-top:46px;margin-bottom:46px">
			<div class="mobile-div img-rounded">
				<div class="mobile-hd">订单编号<?php echo $orderid; ?></div>	
			<!--网页支付START-->	
				<?php
				$needdelivery=1;//如果购买的商品中有需要发货的，则显示发货信息和收货信息
				$needdiscount=0;//是否显示优惠信息(如果购买的商品是自己手动输入金额的，则不显示优惠信息)
			if(!empty($ordergoodsinfos)){	
				foreach($ordergoodsinfos as $ordergoodsinfo){
					$isdelivery=$ordergoodsinfo->isdelivery;
					$ismanual=$ordergoodsinfo->ismanual;
					if($isdelivery==0){
						$needdelivery=0;
					}
					if($ismanual==0){//只要有不是人工输入金额的就显示优惠信息
						$needdiscount=1;
					}
				?>
				<div>
					<div style=" float:left;width:145px; height:153px;" name="goodsimg">
						<?php $upload =wp_upload_dir();
						if((empty($ordergoodsinfo->goods_thumb))||(stristr($ordergoodsinfo->goods_thumb,"http")!==false)){
								$goodsthumb=$ordergoodsinfo->goods_thumb;
							}else{
								$goodsthumb=$upload['baseurl'].$ordergoodsinfo->goods_thumb;	
							}
						if(!empty($goodsthumb)) {?>
							<img class="research-thumb" style="width:100%;height:100%" src="<?php echo $goodsthumb; ?>">
						<?php }else {?>
							<div class="thumbnail" style="height:150px;"><p>没有图片</p></div>
						<?php }?>
					</div>
					<div style="height:auto;overflow:hidden;" name="goodsinfo">
						<div class="mobile-content" name="goodstitle">
							<h4 style="color:#34495e;"><?php echo $ordergoodsinfo->goods_title; ?></h4>
						</div>
						<!--ismanual=1表示手动输入金额，不显示购买价格和数量-->
						<div style="<?php if($ordergoodsinfo->ismanual=='1'){ echo 'display:none';} ?>" id="goodsprice" class="mobile-content" name="goodsprice">
							购买价格：￥<?php echo number_format($ordergoodsinfo->goods_price,2,".",""); ?>
						</div>
						<div style="<?php if($ordergoodsinfo->ismanual=='1'){ echo 'display:none';} ?>" id="goodstotal"  class="mobile-content" name="goodstotal">
							购买数量：<?php echo $ordergoodsinfo->total; ?>
						</div>
						<div id="goodstotalprice"  class="mobile-content" name="goodstotalprice">
							合计：￥<?php echo $ordergoodsinfo->total_price; ?>
						</div>
					</div>
				</div>
				<hr size="1" width="100%" color="black" style="margin:0px 0" noshade="noshade" />
				<?php } }else{ ?>
				<!--网页支付END-->
				<!--扫描支付START-->
				<div>
					<div style=" float:left;width:145px; height:153px;" name="goodsimg">
						<img class="research-thumb" style="width:100%;height:100%" src="<?php bloginfo('template_directory'); ?>/images/qrcode.png">					
					</div>
					<div style="height:auto;overflow:hidden;" name="goodsinfo">
						<div class="mobile-content" name="goodstitle">
							<h4 style="color:#34495e;"><?php echo $order_description; ?></h4>
						</div>
						<!--原生商品二维码支付默认显示价格和数量，并且数量为1-->
						<div id="goodsprice" class="mobile-content" name="goodsprice">
							购买价格：￥<?php echo number_format($order_fee,2,".",""); ?>
						</div>
						<div style="<?php if($ordergoodsinfo->ismanual=='1'){ echo 'display:none';} ?>" id="goodstotal"  class="mobile-content" name="goodstotal">
							购买数量：<?php echo "1"; ?>
						</div>
						<div id="goodstotalprice"  class="mobile-content" name="goodstotalprice">
							合计：￥<?php echo $order_fee; ?>
						</div>
					</div>
				</div>
				
				<?php } ?>
				<!--扫描支付END-->
				<!--订单中没有发货的商品和原生商品二维码支付不显示收货地址-->
				<div style="<?php if(($needdelivery==1)||(empty($ordergoodsinfos))){ echo 'display:none';} ?>">			
					<div class="mobile-content">姓名：<?php echo $order_address['username']; ?></div>
					<div class="mobile-content">电话：<?php echo $order_address['telnumber']; ?></div>
					<div class="mobile-content">邮编：<?php echo $order_address['postalcode']; ?></div>
					<div class="mobile-content">地区：<?php echo $order_address['stagename']; ?></div>
					<div class="mobile-content">地址：<?php echo $order_address['detailinfo']; ?></div>
				</div>
				<div>
					<hr size="1" width="100%" color="black" noshade="noshade" style="<?php if($needdelivery==1||empty($ordergoodsinfos)){ echo 'display:none';} ?>"/>
					<div id="order_trade_state" class="mobile-content" name="order_trade_state">
							交易状态：
							<font style="font-size:16px;font-weight:bold;" color="#f15a28" >
							<?php echo $this -> TRADE_STATE[$order_trade_state]; ?>
							</font>
					</div>
					<!--订单中全为手动输入金额的商品和原生商品二维码支付不显示优惠金额-->
					<div style="<?php if(($needdiscount==0)||(empty($ordergoodsinfos))||($order_trade_type=="NATIVE_ORDER")||($order_trade_type=="NATIVE_PRODUCT")){ echo 'display:none';} ?>" id="goodsdiscount"  class="mobile-content" name="goodsdiscount">
						优惠金额：￥<?php echo $discount_fee; ?>
					</div>
					<div  class="mobile-content" name="goodstotalfee">
						支付金额：<font style="font-size:16px;font-weight:bold;" color="#f15a28" >￥</font>
						<input id="goodstotalfee" name="goodstotalfee" style="border:0;font-size:16px;font-weight:bold;color:#f15a28;width:150px;background-color:#fff" disabled="disabled" value="<?php echo number_format($order_fee,2,".",""); ?>"></input>
					</div>
				</div>
				<div>
					<hr size="1" width="100%" color="black" noshade="noshade" />
					<div id="order_payment_type"  class="mobile-content" name="order_payment_type">
						付款方式：
					<?php if($order_payment_type=="0"){
								echo "货到付款";
							}else if($order_payment_type=="1"){
								echo "微信支付";
							} 
					?>
					</div>
					<div id="order_time_start"  class="mobile-content" name="order_time_start">
						下单时间：<?php echo $order_time_start; ?>
					</div>
					<div id="order_time_end"  class="mobile-content" name="order_time_end">
						付款时间：<?php echo $order_time_end; ?>
					</div>
					<!--<div id="order_time_expire"  class="mobile-content" name="order_time_expire">
						关闭时间：<?php echo  $order_time_expire; ?>
					</div>
					<div id="order_bank_type"  class="mobile-content" name="order_bank_type">
						付款银行：<?php echo $this -> BANK_TYPE[$order_bank_type]; ?>
					</div>-->
				</div>
				<!--订单中没有需要发货的商品和原生商品二维码支付不显示发货信息-->
				<div style="<?php if(($needdelivery==1)||(empty($ordergoodsinfos))){ echo 'display:none';} ?>">
					<hr size="1" width="100%" color="black" noshade="noshade" />
					<?php if(($delivery_status == "0")||(!isset($delivery_status))){//没有发货记录?>
						<div style="<?php if($needdelivery==1||empty($ordergoodsinfos)){ echo 'display:none';} ?>" class="mobile-content" >没有发货记录！</div>
					<?php }else{ ?>
					<div id="delivery_timestamp"  class="mobile-content" name="delivery_timestamp">
						发货时间：<?php echo $delivery_timestamp; ?>
					</div>
					<div id="delivery_compid"  class="mobile-content" name="delivery_compid">
						快递公司：<?php echo $delivery_compid; ?>
					</div>
					<div id="delivery_sn"  class="mobile-content" name="delivery_sn">
						物流速递单号：<?php echo $delivery_sn; ?>
					</div>
					<div id="delivery_status"  class="mobile-content" name="delivery_status">
						发货状态：<?php if($delivery_status == "1") {
							echo "已发货";
						}else if($delivery_status == "2"){
							echo "已确认收货";
						}else{
							echo "未发货";
						}?>
					</div>
					<?php if(!empty($delivery_msg)){?>
					<div id="delivery_msg"  class="mobile-content" name="delivery_msg">
						发货状态信息：
						<pre style="border:none;background:#fff;font-family:'microsoft yahei',Verdana,Arial,Helvetica,sans-serif" class="mobile-content"><?php echo $delivery_msg;?></pre>
					</div>
					<?php }} ?>
				</div>
				<!--订单中全为手动输入金额的商品和原生商品二维码支付不显示优惠金额-->
				<div style="<?php if(($needdiscount==0)||(empty($ordergoodsinfos))||($order_trade_type=="NATIVE_ORDER")||($order_trade_type=="NATIVE_PRODUCT")){ echo 'display:none';} ?>" >
					<hr size="1" width="100%" color="black" noshade="noshade" />
					
					<?php
					if(!empty($discount_list)){
						foreach($discount_list as $discount_element){ ?>
						<div class="mobile-content">
						<?php echo $this -> DISCOUNT_TYPE[$discount_element->discount_type]; ?>:￥<?php echo $discount_element->discount_price; ?>
						</div>
					<?php }
					} else { ?>
						<div style="<?php if($needdiscount==0||empty($ordergoodsinfos)||($order_trade_type=="NATIVE_ORDER")||($order_trade_type=="NATIVE_PRODUCT")){ echo 'display:none';} ?>" class="mobile-content" >该订单无优惠记录！</div>
				<?php } ?>
				</div>
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
				<div style="margin-bottom:55px;<?php if($order_trade_state=="CLOSE"){?> display:none <?php } ?> ">
					<hr size="1" width="100%" color="black" noshade="noshade" />
				<?php  
				//获得交易完成时间，并转换为时间戳
				$enddate = strtotime($order_time_end);
				//获得申请维权时间与之前交易完成时间的时间间隔
				$days=round(($currentdate-$enddate)/3600/24);
				//判断申请维权时间与之前交易完成时间的时间间隔是小于45天
				if($order_trade_state=="SUCCESS"){ 
					if($days<45 && !$order_rightscounts){?>
					<a onclick="href='<?php echo $this->createMobileUrl('rightsdetails',array('gweid' => $gweid,'out_trade_no' => $orderid,'goodsgid'=>$goodsgid));?>'" style="margin-right:3px;" class="btn btn-success pull-right btn-sm">维权</a>
					<?php }if($days<45 && $order_rightscounts){?>
					<a onclick="href='<?php echo $this->createMobileUrl('rightsdetails',array('gweid' => $gweid,'out_trade_no' => $orderid,'goodsgid'=>$goodsgid));?>'" style="margin-right:3px;" class="btn btn-success pull-right btn-sm" disabled="disabled">正在维权</a>
					<?php }
				}else{ if(($delivery_status!='1')&&($delivery_status!='2')){?>
					<!--没有支付成功，并且不是已发货和确认收货的就可以取消订单-->
					<a onclick="orderdel('<?php echo $orderid; ?>')" style="margin-right:3px;" class="btn btn-success pull-right btn-sm">取消订单</a>
				<?php } ?>	
					<?php if($order_trade_type == 'NATIVE_PRODUCT'){?>
					<a style="margin-right:3px;" class="btn btn-default pull-right btn-sm" disabled="disabled">请重新扫码付款</a>
					<?php }else{?>
					<a onClick="callpayconfirm('<?php echo $orderid; ?>',this)" style="margin-right:3px;" class="btn btn-success pull-right btn-sm">付款</a>
					<?php }?>
					<!--没有支付成功显示付款-->
					
				<?php } 
				if($delivery_status=='1'){
				?><!--如何已发货则显示确认收货-->
				<a onclick="orderconfirmed('<?php echo $orderid; ?>')" style="margin-right:3px;" class="btn btn-success pull-right btn-sm">确认收货</a>
				<?php } ?>
				</div>
			</div>	
		</div>
		<div class="footerbar">
			<a style="width:33%;padding-top:15px" onclick="href='<?php echo $this->createMobileUrl('goodsinfo',array('gweid'=>$gweid,'goodsgid' => $goodsgid));?>'">首页</a>
			<a style="width:33%;padding-top:15px" onclick="href='<?php echo $this->createMobileUrl('myorderlist',array('gweid' => $gweid,'goodsgid' => $goodsgid)); ?>'">我的订单</a>
			<a style="width:33%;padding-top:15px" onclick="href='<?php echo $this->createMobileUrl('rightslists',array('gweid' => $gweid,'goodsgid'=>$goodsgid));?>'">我的维权</a>
		</div>
		<!--<div id="footer"></div>--> 
		<script language="javascript" type="text/javascript"> 
		isSubmitting = false;
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
		function orderconfirmed(out_trade_no){
			
			if(isSubmitting)
				return false;
				isSubmitting = true;
			if(confirm("确定确认收货吗？")){
				$.ajax({
					async:false,
					url:window.location.href, 
					type: "POST",
					data:{'order_confirmed':'isConfirmed','out_trade_no':out_trade_no},
					success: function(data){
						if (data.status == 'error'){
							alert(data.message);
						}else if (data.status == 'success'){
							alert(data.message);						
							location.reload();
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
		function callpayconfirm(orderid,obj){
			if(isSubmitting)
				return false;
				isSubmitting = true;
			$.ajax({
				async:false,
				url:window.location.href, 
				type: "POST",
				data:{'order_pay':'isPay','orderid':orderid},
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
									window.location.href = data.data.resultUrl; 
								}else{
									if((res.err_msg !="get_brand_wcpay_request:cancel" )&&(res.err_msg !="get_brand_wcpay_request:fail")){
										alert("微信错误:支付权限禁止");
									}
									window.location.href = data.data.resultUrl;
								}			   
							});
						}else if(data.data.trade_type=='NATIVE_ORDER'){
							$(obj).parent().before('<div><hr size="1" width="100%" color="black" noshade="noshade" /><div class="mobile-content">支付二维码链接</div>'+'<div  style="word-break:break-all;" class="mobile-content"><input class="form-control mobile-content" readonly="readonly" value="'+data.data.code_url+'" style="width:94%"><div>注:复制该链接到微信任意聊天窗口,点击进入后即可进行支付</div></div></div>');
							$(obj).css('display','none');
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
		function show_code_url(code_url,obj){
			$(obj).parent().before('<div><hr size="1" width="100%" color="black" noshade="noshade" /><div class="mobile-content">支付二维码链接</div>'+'<div  style="word-break:break-all;" class="mobile-content"><input class="form-control mobile-content" readonly="readonly" value="'+code_url+'" style="width:94%"><div>注:复制该链接到微信任意公众号聊天窗口,点击进入后即可进行支付</div></div></div>');
			$(obj).css('display','none');
		}
		</script> 
	<?php  include $this -> template('footer');?>