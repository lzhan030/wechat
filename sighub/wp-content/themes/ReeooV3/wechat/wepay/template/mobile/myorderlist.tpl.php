<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
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
		<title>我的订单</title>		
	</head>
	<body>		
		<div class="head">
			<span class="title">我的订单</span>
		</div>
		<div class="research" style="margin-top:46px;margin-bottom:46px">		
			<?php foreach($orderarray as $k=>$val){	 ?>
			<div class="mobile-div img-rounded">
				<div class="mobile-hd">订单编号：<?php echo $val['out_trade_no'];?>
					<span  color="#f15a28" style="height:14ps;margin-right: 4%;float:right;font-size:12px" width="20ps" style="width:70px"><?php echo $val['trade_state_display']; ?></span>
				</div>	
			<!--网页支付START-->
			<?php
			$needdelivery=1;//如果购买的商品中有需要发货的，则显示发货信息和收货信息
			$needdiscount=0;//是否显示优惠信息(如果购买的商品是自己手动输入金额的，则不显示优惠信息)
			if(!empty($val['ordergoods'])){	
				foreach($val['ordergoods'] as $kg=>$valg){
					$isdelivery=$valg->isdelivery;
					$ismanual=$valg->ismanual;
					if($isdelivery==0){
						$needdelivery=0;
					}
					if($ismanual==0){//只要有不是人工输入金额的就显示优惠信息
						$needdiscount=1;
					}
					
				?>			
					<div style=" float:left;width:145px; height:153px;" name="goodsimg">
						<?php $upload =wp_upload_dir();
						if((empty($valg->goods_thumb))||(stristr($valg->goods_thumb,"http")!==false)){
							$goodsthumb=$valg->goods_thumb;
						}else{
							$goodsthumb=$upload['baseurl'].$valg->goods_thumb;	
						}
						if(!empty($goodsthumb)) {
						?>
							<img class="research-thumb" style="width:100%;height:100%" src="<?php echo $goodsthumb; ?>">	
						<?php } else {?>
							<div class="thumbnail" style="height:150px;"><p>没有图片</p></div>
						<?php } ?>
					</div>
					<div style="height:auto;overflow:hidden;" name="goodsinfo">
						<div class="mobile-content" name="goodstitle">
							<h4 style="color:#34495e;"><?php echo $valg->goods_title; ?></h4>
						</div>
						<!--ismanual=1表示手动输入金额，不显示购买价格和数量-->
						<div style="<?php if($valg->ismanual=='1'){ echo 'display:none';} ?>" id="goodsprice" class="mobile-content" name="goodsprice">
							购买价格：￥<?php echo number_format($valg->goods_price,2,".",""); ?>
						</div>
						<div style="<?php if($valg->ismanual=='1'){ echo 'display:none';} ?>" id="goodstotal"  class="mobile-content" name="goodstotal">
							购买数量：<?php echo $valg->total; ?>
						</div>
						<div id="goodstotalprice"  class="mobile-content" name="goodstotalprice">
							合计：￥<?php echo $valg->total_price; ?>
						</div>					
					</div>
					<hr size="1" width="100%" color="black" noshade="noshade" style="margin-top:0px;margin-bottom:0px"/>
					<?php 
					
				}
			}else{ ?>
			<!--网页支付END-->
			<!--扫描支付START-->
				<div style=" float:left;width:145px; height:153px;" name="goodsimg">
					<img class="research-thumb" style="width:100%;height:100%" src="<?php bloginfo('template_directory'); ?>/images/qrcode.png">					
				</div>
				<div style="height:auto;overflow:hidden;" name="goodsinfo">
					<div class="mobile-content" name="goodstitle">
						<h4 style="color:#34495e;"><?php echo $val['description']; ?></h4>
					</div>
					<!--原生商品二维码支付默认显示价格和数量，并且数量为1-->
					<div id="goodsprice" class="mobile-content" name="goodsprice">
						购买价格：￥<?php echo number_format($val['fee'],2,".",""); ?>
					</div>
					<div id="goodstotal"  class="mobile-content" name="goodstotal">
						购买数量：<?php echo "1"; ?>
					</div>
					<div id="goodstotalprice"  class="mobile-content" name="goodstotalprice">
						合计：￥<?php echo $val['fee']; ?>
					</div>					
				</div>
				<hr size="1" width="100%" color="black" noshade="noshade" style="margin-top:0px;margin-bottom:0px"/>
			<?php } ?>
			<!--扫描支付END-->
				<div>
					<!--原生商品二维码支付不显示优惠金额-->
					<div style="<?php if(($needdiscount==0)||(empty($val['ordergoods']))||($val['wepay']['trade_type'] == 'NATIVE_ORDER')){ echo 'display:none';} ?>" id="goodsdiscount"  class="mobile-content" name="goodsdiscount">
						优惠金额：￥<?php echo $val['discount']; ?>
					</div>
					<div  class="mobile-content" name="goodstotalfee">
						支付金额：<font style="font-size:16px;font-weight:bold;" color="#f15a28" >￥</font>
						<input id="goodstotalfee" name="goodstotalfee" style="border:0;font-size:16px;font-weight:bold;color:#f15a28;width:151px;background-color:#fff" disabled="disabled" value="<?php echo number_format($val['fee'],2,".","");?>"></input>
					</div>
					<!--原生商品二维码支付不显示发货-->
					<div style="<?php if(($needdelivery==1)||(empty($val['ordergoods']))){echo 'display:none'; } ?>" class="mobile-content" >
						发货状态：<?php if($val['deliveryinfo']->delivery_status=='1'){echo "已发货";}else if($val['deliveryinfo']->delivery_status=='2'){echo "已确认收货";}else{echo "未发货";} ?>
					</div>
					<div style="margin-bottom:45px;">		
				<?php 
				//获得交易完成时间，并转换为时间戳
				$endtime=$val['time_end'];
				$enddate = strtotime($endtime);
				//获得申请维权时间与之前交易完成时间的时间间隔
				$days=round(($currentdate-$enddate)/3600/24);
				//判断申请维权时间与之前交易完成时间的时间间隔是否小于45天
				if($val['trade_state']=="SUCCESS"){ 
					if($days<45 && !$val['rightscounts']) {?>
					<a onclick="href='<?php echo $this->createMobileUrl('rightsdetails',array('gweid' => $gweid,'out_trade_no' => $val['out_trade_no']));?>'" style="margin-right:3px;" class="btn btn-success pull-right btn-sm">维权</a>
					<?php }if($days<45 && $val['rightscounts']){?>
					<a onclick="href='<?php echo $this->createMobileUrl('rightsdetails',array('gweid' => $gweid,'out_trade_no' => $val['out_trade_no']));?>'" style="margin-right:3px;" class="btn btn-success pull-right btn-sm" disabled="disabled">正在维权</a>
					<?php }
				}else{ if(($val['deliveryinfo']->delivery_status!='1')&&($val['deliveryinfo']->delivery_status!='2')){?>
					<!--没有支付成功，并且不是已发货和确认收货的就可以取消订单-->
					<a onclick="orderdel('<?php echo $val['out_trade_no']; ?>')" style="margin-right:3px;" class="btn btn-success pull-right btn-sm">取消订单</a>
				<?php } ?>		
					<!--没有支付成功显示付款-->
					<?php if($val['wepay']['trade_type'] == 'NATIVE_PRODUCT'){?>
					<a style="margin-right:3px;" class="btn btn-default pull-right btn-sm" disabled="disabled">请重新扫码付款</a>
					<?php }else{?>
					<a onClick="callpayconfirm('<?php echo $val['out_trade_no']; ?>',this)" style="margin-right:3px;" class="btn btn-success pull-right btn-sm">付款</a>
					<?php }?>
				<?php }
				if($val['deliveryinfo']->delivery_status=='1'){
				?><!--如果已发货则显示确认收货-->
				<a onclick="orderconfirmed('<?php echo $val['out_trade_no']; ?>')" style="margin-right:3px;" class="btn btn-success pull-right btn-sm">确认收货</a>
				<?php } ?>	
					<a onclick="href='<?php echo $this->createMobileUrl('orderdetail',array('gweid' => $gweid,'orderid' => $val['out_trade_no'],'goodsgid'=>$goodsgid)); ?>'" style="margin-right:3px;" class="btn btn-success pull-right btn-sm">订单详情</a>
					</div>
				</div>				
			
			</div><?php } ?>		
		</div>
		<div class="footerbar">
			<a style="width:33%;padding-top:15px" onclick="href='<?php echo $this->createMobileUrl('goodsinfo',array('gweid'=>$gweid,'goodsgid' => $goodsgid));?>'">首页</a>
			<a style="width:33%;padding-top:15px" onclick="href='<?php echo $this->createMobileUrl('myorderlist',array('gweid' => $gweid,'goodsgid' => $goodsgid)); ?>'">我的订单</a>
			<a style="width:33%;padding-top:15px" onclick="href='<?php echo $this->createMobileUrl('rightslists',array('gweid' => $gweid,'goodsgid' => $goodsgid));?>'">我的维权</a>
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
							$(obj).parent().before('<div><div class="mobile-hd">支付二维码链接</div>'+'<div class="mobile-content" style="word-break:break-all;"><input class="form-control" readonly="readonly" value="'+data.data.code_url+'" ><div>注:复制该链接到微信任意聊天窗口,点击进入后即可进行支付</div></div></div>');
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
			$(obj).parent().before('<div><div class="mobile-hd">支付二维码链接</div>'+'<div class="mobile-content" style="word-break:break-all;"><input class="form-control" readonly="readonly" value="'+code_url+'" ><div>注:复制该链接到公众号聊天窗口,点击进入后即可进行支付</div></div></div>');
			$(obj).css('display','none');
		}
		</script>
<?php  include $this -> template('footer');?>