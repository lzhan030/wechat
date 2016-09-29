<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">		
	<title>订单详情</title>		
</head>
<div class="main_auto">
	<div class="main-title">
		<div class="title-1">当前位置：<a href="<?php echo $this->createWebUrl('index',array());?>">微商城</a> > <a href="<?php echo $this->createWebUrl('ordermanage',array());?>">订单管理</a> > <font class="fontpurple">订单详情</font></div>
	</div>
	<div class="panel panel-default" style="margin-right:30px; margin-top:25px;height:269px">
		<div class="panel-heading">订单详情</div>
		<table width="450" height="200" border="0" cellpadding="10px" style="margin-left: 23%; margin-top:15px;" id="table1">
			<tr>
				<td style="width:80px;"><label for="orderid">订单编号: </label></td>
				<td><?php echo $orderid?></td>
			</tr>
			<tr>
				<td><label for="order_payment_type">付款方式: </label></td>
				<td><?php 	if($order_payment_type=="0"){
								echo "货到付款";
							}else if($order_payment_type=="1"){
								echo "微信支付";
							}else if($order_payment_type=="2"){
								echo "自提";
							}
					?>
				</td>
			</tr>
			<tr>
				<td><label for="order_time_start">下单时间: </label></td>	
				<td><?php echo $order_time_start?></td>
			</tr>
			<tr>
				<td><label for="order_time_end">付款时间: </label></td>		
				<td><?php echo $order_time_end; ?></td>
			</tr>
			<!--<tr>
				<td><label for="order_time_expire">关闭时间: </label></td>		
				<td><?php $order_time_expire; ?></td>
			</tr>
			<tr>
				<td><label for="order_trade_type">交易类型: </label></td>		
				<td><?php echo $order_trade_type?></td>
			</tr>-->
			<!--<tr>
				<td><label for="order_fee_type">货币种类: </label></td>		
				<td><?php echo $order_fee_type?></td>
			</tr>
			<tr>
				<td><label for="order_bank_type">付款银行: </label></td>		
				<td><?php echo $this -> BANK_TYPE[$order_bank_type]; ?></td>
			</tr>-->
			<!--<tr>
				<td><label for="order_coupon_fee">现金券金额: </label></td>		
				<td><?php echo $order_coupon_fee?></td>
			</tr>
			<tr>
				<td><label for="order_send_type">配送方式: </label></td>		
				<td><?php echo  $this -> SEND_TYPE[$order_send_type]; ?>
				</td>
			</tr>-->
			<tr>
				<td><label for="order_trade_state">交易状态: </label></td>		
				<td><p style="border:0;font-size:16px;font-weight:bold;color:#f15a28;">
						<?php echo $this -> TRADE_STATE[$order_trade_state]; ?>
					</P>
				</td>
			</tr>
			<tr style="<?php if($order_iserror!=1){?> display:none <?php } ?>">
				<td><label for="order_error_description">错误原因: </label></td>		
				<td><?php echo $order_error_description; ?></td>
			</tr>
		</table>
	</div>
	<div class="panel panel-default" style="margin-right:30px; margin-top: -22px;height: auto;">
		<div class="panel-heading">买家信息</div>
		<table width="400" height="70" border="0" cellpadding="10px" style="margin-left: 23%; margin-top:15px;" id="table2">
			<tr>
				<td width="185"><label for="openid">OpenId:</label></td>		
				<td><?php echo $order_openid;?></td>
			</tr>
			<tr>
				<td width="185"><label for="nickname">微信昵称:</label></td>		
				<td><?php echo $order_openid_name;?></td>
			</tr>
			<tr>
				<td width="185"><label for="nickname">会员编号:</label></td>		
				<td><?php echo $order_mid;?></td>
			</tr>
			<!--买家留言weshopping-->
			<?php if(!empty($order_remark)){?>
			<tr>
				<td width="185"><label for="nickname">买家留言:</label></td>		
				<td>
					<pre style="padding-left:0px;border:none;background:#fff;font-family:'microsoft yahei',Verdana,Arial,Helvetica,sans-serif" class="mobile-content"><?php echo $order_remark;?></pre>
				</td>
			</tr>
			<?php }?>
		</table>
	</div>
	<!--订单中没有需要发货的商品和原生商品二维码支付不显示收货地址-->
	<div class="panel panel-default" style="margin-right:30px; margin-top: -22px;height: 185px;<?php if($needdelivery==1||empty($ordergoodsinfos)){ echo 'display:none';} ?>">
		<div class="panel-heading">收货地址
		<input type="button" class="btn btn-sm btn-warning" onclick="addressUpdate(<?php echo $order->out_trade_no?>)"  value="修改收货地址" <?php if(($delivery_status == '1')||($delivery_status == '2')||($order_trade_state=='CLOSED')){ ?> disabled="disabled" <?php } ?>  style="float: right;padding:4px 14px;">
		</div>
		<table width="470" height="100" border="0" cellpadding="10px" style="margin-left: 23%;margin-top:15px;" id="table3">
			<tr>
				<td><label for="">收货人姓名: </label></td>		
				<td><?php echo $order_address['username'];?></td>
			</tr>
			<tr>
				<td><label for="">联系电话: </label></td>		
				<td><?php echo $order_address['telnumber'];?></td>
			</tr>
			<tr>
				<td><label for="">邮编: </label></td>		
				<td><?php echo $order_address['postalcode'];?></td>
			</tr>
			<tr>
				<td><label for="">地区: </label></td>		
				<td><?php echo $order_address['stagename'];?></td>
			</tr>
			<tr>
				<td><label for="">详细地址: </label></td>		
				<td><?php echo $order_address['detailinfo'];?></td>
			</tr>
		</table>
	</div>
	<!--订单中没有需要发货的商品和原生商品二维码支付不显示发货信息-->
	<div class="panel panel-default" style="margin-right:30px; margin-top: -22px;height: auto;<?php if($needdelivery==1||empty($ordergoodsinfos)){ echo 'display:none';} ?>">
		<div class="panel-heading">发货信息
			<!--<input type="button" class="btn btn-sm btn-default" onclick="location.href='<?php echo $this->createWebUrl('Delivery',array('orderid' => $order->out_trade_no));?>'" name="delivery_order" id="delivery_order" value="发货" style="float: right;">-->
			<input type="button" class="btn btn-sm btn-warning" onclick="deliveryUpdate(<?php echo $order->out_trade_no?>)"  value="发货" <?php if(($delivery_status == '2')||($order_trade_state=='CLOSED')){ ?> disabled="disabled" <?php } ?> style="float: right;padding:4px 14px;">
		</div>
		<table width="450" height="100" border="0" cellpadding="10px" style="margin-left: 23%;margin-top:15px;" id="table4">
			<tr <?php if(($delivery_status == "0")||(!isset($delivery_status))){?> style='display:none'  <?php }?> >
				<td width="133"><label for="delivery_timestamp">发货时间: </label></td>
				<td><?php echo $delivery_timestamp?></td>
			</tr>
			<tr>
				<td width="133"><label for="delivery_compid"><?php if($order_isshopping == 0){?>快递公司: <?php }else{?>配送方式: <?php }?></label></td>		
				<td><?php echo $delivery_compid?></td>
			</tr>
			<tr>
				<td width="133"><label for="delivery_sn">物流速递单号: </label></td>
				<td><?php echo $delivery_sn?></td>
			</tr>
			<tr>
				<td width="133"><label for="delivery_status">发货状态: </label></td>
				<td>
					<?php if($delivery_status == "1") {
							echo "已发货";
						}else if($delivery_status == "2") {
							echo "收货已确认";
						}else{
							echo "未发货";
						}
					?>
				</td>
			</tr>
			<tr>
				<td width="133"><label for="delivery_msg">发货状态信息: </label></td>
				<td><?php echo $delivery_msg?></td>
			</tr> 
			<!--配送方式weshopping-->
			<?php if(!empty($order_dispatchname)){?>
			<tr>
				<td width="133"><label for="delivery_msg">期望配送方式: </label></td>
				<td>
				<?php if($order_isdispatchpoint!=-2){ ?>
				<?php echo $order_dispatchname."(<font color='red'>运费:".$order_dispatchprice."元</font>)";?>
				<?php }else{ ?>
				<?php echo $order_dispatchname."(<font color='red'>运费:".intval($order_dispatchprice)."积分</font>)";?><!--point update-->
				<?php } ?>
				</td>
			</tr>
			<?php }?>
			<?php if(!empty($remark)){?>
			<tr>
				<td width="133"><label for="delivery_msg">备注: </label></td>
				<td><pre style="padding-left:0px;border:none;background:#fff;font-family:'microsoft yahei',Verdana,Arial,Helvetica,sans-serif" class="mobile-content"><?php echo $remark;?></pre>
				</td>
			</tr> 
			<?php }?>
		</table>
	</div>
	<!--订单中全为手动输入金额的商品和原生商品二维码支付不显示优惠金额-->
	<div class="panel panel-default" style="margin-right:30px; margin-top: -22px;<?php if($needdiscount==0||empty($ordergoodsinfos)||($order_trade_type=="NATIVE_ORDER")||($order_trade_type=="NATIVE_PRODUCT")){ echo 'display:none';} ?>">
		<div class="panel-heading">优惠信息 
			<input type="button" class="btn btn-sm btn-warning" onclick="createDiscount('<?php echo $order->out_trade_no;?>')" name="order_discount" id="order_discount" value="添加优惠信息"  <?php if(($order_trade_state != "PAYING")||  (strtotime("+1 year",strtotime($order_time_start)) < time()) ){ ?> disabled="disabled" <?php } ?> style="float: right;padding:4px 14px;">
		</div>
		<?php
					if(!empty($discount_list)){?>
		<table class="table table-striped" width="800" border="0" align="center">
			<tbody>
				<tr>
					<td scope="col" width="50" align="center" style="font-weight:bold">优惠类型</td>
					<td scope="col" width="50" align="center" style="font-weight:bold">优惠价格</td>
					<td scope="col" width="100" align="center" style="font-weight:bold">操作</td>
				</tr>
					<?php
						foreach($discount_list as $discount_element){
					 ?>
				<tr>
					<td align="center"><?php echo $this -> DISCOUNT_TYPE[$discount_element->discount_type]; ?></td>
					<td align="center"><?php echo $discount_element->discount_price; ?> </td>
					<td class="row" align="center">
						<input type="button" class="btn btn-sm btn-warning" <?php if(($order_trade_state != "PAYING")||  (strtotime("+1 year",strtotime($order_time_start)) < time()) ){ ?> disabled="disabled"<?php } ?>  onclick="updateDiscount('<?php echo $discount_element->id; ?>','<?php echo $orderid; ?>')" name="updateDiscount" id="updateDiscount" value="更新">
						<input type="button" class="btn btn-sm btn-info" <?php if(($order_trade_state != "PAYING")||  (strtotime("+1 year",strtotime($order_time_start)) < time()) ){ ?> disabled="disabled"<?php } ?>  onclick="delDiscount('<?php echo $discount_element->id; ?>','<?php echo $orderid; ?>')" name="delDiscount" id="delDiscount" value="删除">
					</td>
				</tr>
				<?php }?>
			</tbody>
		</table>
		<?php } else {?>
		<div class="panel-body">该订单无优惠记录！</div>
		<?php }?>
	</div>
	<div class="panel panel-default" style="margin-right:30px; margin-top: -22px;<?php if(($order_trade_state=="SELFDELIVERY")||($order_trade_state=="CASHONDELIVERY")){ echo 'display:none';} ?>">
		<div class="panel-heading">退款信息 
			<input type="button" class="btn btn-sm btn-warning" onclick="createRefund('<?php echo $order->out_trade_no;?>')" name="refund_order" id="refund_order" value="退款" <?php if(($order_trade_state != "SUCCESS" && $order_trade_state != "REFUND")||  (strtotime("+1 year",strtotime($order_time_start)) < time()) ){ ?> disabled="disabled"<?php } ?> style="float: right;padding:4px 14px;">
		</div>
		<?php
					if(is_array($refund_list) && !empty($refund_list)){?>
		<table class="table table-striped" width="800" border="0" align="center">
			<tbody>
				<tr>
					<td scope="col" width="50" align="center" style="font-weight:bold">退款时间</td>
					<td scope="col" width="50" align="center" style="font-weight:bold">退款编号</td>
					<td scope="col" width="50" align="center" style="font-weight:bold">退款原因</td>
					<td scope="col" width="50" align="center" style="font-weight:bold">退款金额（元）</td>
					<td scope="col" width="50" align="center" style="font-weight:bold">退款状态</td>
				</tr>
					<?php
						foreach($refund_list as $refund_element){
					 ?>
				<tr>
					<td align="center"><?php echo $refund_element['time_start']; ?></td>
					<td align="center"><?php echo $refund_element['out_refund_no']; ?> </td>
					<td align="center"><?php echo $this -> REFUND_REASON[$refund_element['reason']]; ?></td>
					<td align="center"><?php echo number_format($refund_element['refund_fee'],2,'.',''); ?></td>
					<td align="center"><?php echo $this -> REFUND_STATUS[$refund_element['refund_status']]; ?><?php if(in_array($element['refund_status'],array('NOTSURE','CREATEFAIL'))){?><a href="javascript:retry_create_refund('<?php echo $element['out_refund_no']; ?>')">[重试]</a><?php }?></td>
				</tr>
				<?php }?>
			</tbody>
		</table>
		<?php } else {?>
		<div class="panel-body">该订单无退款记录！</div>
		<?php }?>
	</div>
	<div class="main panel panel-default" style="height: auto;margin-right:30px; margin-top: -22px;width:96.7%">
		<div class="panel-heading">商品信息</div>
		<div class="panel-body table-responsive">
			<table class="table table-hover" style="min-width:800px;">
			<tr>
				<td scope="col" width="100" align="center" style="font-weight:bold">编号</td>
				<td scope="col" width="100" align="center" style="font-weight:bold">名称</td>
				<td scope="col" width="100" align="center" style="font-weight:bold">价格(元)</td>
				<td scope="col" width="100" align="center" style="font-weight:bold">积分</td><!--point update-->
				<td scope="col" width="100" align="center" style="font-weight:bold">数量</td>
				<td scope="col" width="100" align="center" style="font-weight:bold">总价</td>
			</tr>			
			<?php 
			if(!empty($ordergoodsinfos)){
				foreach($ordergoodsinfos as $ordergoodsinfo){
				?>
				<tr align='center'>
				
					<td>
					<a href="<?php echo $this->createWebUrl('goods', array('op' => 'post','id'=>$ordergoodsinfo->goods_id))?>">
						<?php echo $ordergoodsinfo->goods_id; ?>
					</a>
					</td>
					<td <?php if(!empty($ordergoodsinfo->optionname)){ echo "title='[".$ordergoodsinfo->optionname."]".$ordergoodsinfo->goods_title."'";}else{echo "title='".$ordergoodsinfo->goods_title."'";} ?>><?php if(!empty($ordergoodsinfo->optionname)){ echo "[".$ordergoodsinfo->optionname."]".$ordergoodsinfo->goods_title;}else{echo $ordergoodsinfo->goods_title;} ?></td>
					<td><?php if(empty($ordergoodsinfo->total)){echo $ordergoodsinfo->total_price; }else{echo $ordergoodsinfo->goods_price;} ?></td>
					<!--point update-->
					<td>
					<?php if($ordergoodsinfo->point==0 ){
						echo "";
					}else{
						if($ordergoodsinfo->ispoint!=0){
							echo $ordergoodsinfo->point."(使用积分)";
						
						}else{
							echo $ordergoodsinfo->point;
						}
					}
					?>
					</td>
					<!--point update end-->
					<td><?php if(empty($ordergoodsinfo->total)){echo '1'; }else{echo $ordergoodsinfo->total;} ?></td>
					<td>
						<?php 
							if($ordergoodsinfo->point!=0 && $ordergoodsinfo->ispoint!=0){
								echo $ordergoodsinfo->total_point."分";
							}else{
								echo $ordergoodsinfo->total_price."元";
							
							}
						?>
					</td>
				</tr>					
			<?php	
				}
			}else{
			?>
				<tr align='center'>
					<td><?php echo $orderid; ?></td>
					<td><?php echo $order_description; ?></td>
					<td><?php echo $order_fee; ?></td>
					<td><?php echo "1"; ?></td>
					<td><?php echo $order_fee; ?></td>
				</tr>	
			<?php	
			}
			?>			
        </table>
		</div>
	</div>
	<!--<div style="margin-top:3%; margin-left:16%;">
		<label for="usernote">用户备注: </label>
		<input type="text" value="" id="note" style="width:70px">
	</div>-->
	<div style="float:right;margin-top:3%; margin-right:30px;">
		<tr>
			<label style="align:center;" for="order_fee">实付款: <font size="24"><?php echo $order_fee ?></font>元</label>
		</tr>		
	</div>
	<!--point update-->
	<div style="clear:both;float:right;margin-right:30px;">
		<tr>
			<label style="align:center;" for="order_fee">积分:<?php echo intval($order_pointall); ?>分</label>
		</tr>		
	</div>
	<!--point update end-->
	<!--订单中全为手动输入金额的商品和原生商品二维码支付不显示优惠金额-->
	<div style="clear:both;float:right;margin-right:30px;<?php if($needdiscount==0||empty($ordergoodsinfos)||($order_trade_type=="NATIVE_ORDER")||($order_trade_type=="NATIVE_PRODUCT")){ echo 'display:none';} ?>">
		<tr>
			<label style="align:center;" for="discount_fee">共优惠: <?php echo $discount_fee ?>元</label>
		</tr>
	</div>
	<div style="margin-top:2%;margin-bottom:2%;margin-left:85%; ">
		<a href="<?php echo $this->createWebUrl('ordermanage',array());?>"><input type="button" class="btn btn-sm btn-default" value="返回" id="sub3" style="width:70px; margin-left:20px;"></a>
	</div>		
</div>
 <script language="javascript" type="text/javascript">
	function deliveryUpdate(id){  	
		window.open('<?php echo $this->createWebUrl('Delivery',array());?>'+'&orderid='+id,'_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')
		xmlHttp.onreadystatechange = function(){
			window.location.reload();
		}	
	}
	function createDiscount(orderid){
		window.open('<?php echo $this->createWebUrl('createDiscount',array());?>'+'&orderid='+orderid,'_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')
		xmlHttp.onreadystatechange = function(){
			window.location.reload();
		}
	}
	function updateDiscount(discountid,orderid){
		window.open('<?php echo $this->createWebUrl('createDiscount',array());?>'+'&discountid='+discountid+'&orderid='+orderid,'_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')
		xmlHttp.onreadystatechange = function(){
			window.location.reload();
		}
	}
	function createRefund(id){
		window.open('<?php echo $this->createModuleWebUrl('wepay','createRefund',array());?>'+'&orderid='+id,'_blank','height=620,width=700,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')
		xmlHttp.onreadystatechange = function(){
			window.location.reload();
		}
	}
	function addressUpdate(id){  	
		window.open('<?php echo $this->createWebUrl('Addressupdate',array());?>'+'&orderid='+id,'_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')
		xmlHttp.onreadystatechange = function(){
			window.location.reload();
		}
	}
	function delDiscount(discountid,orderid){
		if(confirm("确定删除吗？")){
			$.ajax({
				url:window.location.href, 
				type: "POST",
				data:{'discountdel':'isDel','discountid':discountid,'orderid':orderid},
				success: function(data){
					if (data.status == 'error'){
						alert(data.message);
					}else if (data.status == 'success'){
						alert(data.message);
						window.location.reload();
					}
				},
				 error: function(data){
					alert("出现错误");
				},
				dataType: 'json'
			});
		}		
	}
	function retry_create_refund(refund_id){
		jQuery.post(
			"<?php echo $this->createWebUrl('RetryCreateRefund',array()); ?>",
			{'out_refund_no':refund_id},
			function(data){
				if(data.status == 'SUCCESS'){
					alert("重新发起退款成功！");
					window.location.reload();
				}
				if(data.status == 'FAIL'){
					msg = "重新发起退款失败！";
					if(data.error_msg)
						msg+="失败原因为："+data.error_msg+"。";
					alert(msg);
					}
			},
			'json'
		);
	}
</script>
</html>