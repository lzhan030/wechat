<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
 <script language="javascript">
	top.weorderreminder();	
</script>
<style>
	.sltfield{height: 34px;padding: 6px 12px;font-size: 14px;line-height: 1.428571429;color: #555;vertical-align: middle;background-color: #fff;background-image: none;border: 1px solid #ccc;border-radius: 4px;-webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);-webkit-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;}
	.panel{border-radius: 0px; -webkit-box-shadow: 0 0px 0px rgba(0,0,0,0.05); box-shadow: 0 0px 0px rgba(0,0,0,0.05);}
</style>
	<div class="main-title">
		<div class="title-1">当前位置：<a href="<?php echo $this->createWebUrl('index',array());?>">微商城</a> > <font class="fontpurple">订单管理</font></div>
	</div>
	<div class="shop_content">
	<form name ="content" onSubmit="return validateform()" action="<?php echo $this->createWebUrl('orderlist',array('gweid' => $gweid));?>" method="get" enctype="multipart/form-data">	
	<div class="main panel panel-default" style="height: auto;">
		<div class="panel-heading">订单列表</div>
		<div class="panel-body table-responsive">
			<table class="table table-hover" style="min-width:800px;">
				<tr>
					<td colspan=9 scope="col" width="100" align="left" >
						<select id="range" name="range" class="sltfield" style="margin-right:3px">
							<option value="">请选择
							<option value="all">全部</option>
							<option value="out_trade_no">订单编号</option>
							<option value="openid">OpenId</option>
							<option value="openid_name">微信昵称</option>
							<option value="trade_state">交易状态</option>
							<option value="address_name">收货人</option>
							<option value="delivery_status">发货状态</option>
						</select>
						<input type="hidden" id="module" name="module" value="<?php echo $_GET['module'];?>" />
						<input type="hidden" id="do" name="do" value="<?php echo $_GET['do'];?>" />
						<input type="hidden" id="gweid" name="gweid" value="<?php echo $_GET['gweid'];?>" />
						<input id="indata" class="sltfield" name="indata" value="<?php echo $_GET['indata'];?>" />
						<select id="indata_state" name="indata_state" class="sltfield" style="margin-right:3px">
							<option value="">请选择</option>
							<?php foreach($this->TRADE_STATE as $status_key => $status_value){?>
							<option value="<?php echo $status_key;?>"><?php echo $status_value;?></option>
							<?php }?>
						</select>
						<input type="hidden" name="beIframe" value="1">
						<input id="search1" class="btn btn-sm btn-warning" type="submit" value="查询" />
					</td>
				</tr>
				<tr>
					<td scope="col" width="90" style="font-weight:bold">订单编号</td>
					<td scope="col" width="50" style="font-weight:bold">OpenId</td>
					<td scope="col" width="10" style="font-weight:bold">微信昵称</td>
					<td scope="col" width="90" style="font-weight:bold">会员编号</td>
					<td scope="col" width="90" style="font-weight:bold">金额(元)</td>
					<td scope="col" width="120" style="font-weight:bold">交易状态</td>
					<td scope="col" width="90" style="font-weight:bold">收货人</td>
					<td scope="col" width="120" style="font-weight:bold">发货状态</td>
					<td scope="col" width="100" style="font-weight:bold">操作</td>
				</tr>
				<tbody>
			<?php
				if(is_array($list) && !empty($list)){
					foreach($list as $order){
				 ?>
				<tr>
					<td title='<?php echo $order->out_trade_no; ?>'><?php if($order->read == 0){ ?><span style="color: red;">[新]</span><?php } ?><?php echo $order->out_trade_no; ?></td>
					<td title='<?php echo $order->openid; ?>'>
					<?php  
						echo $order->openid;
					?>
					</td>
					<td>
					<?php  
						
						//如果微信昵称为空，则查看下mid是否为空，不为空的情况下，通过mid搜索到
						if(empty($order->openid_name)){
							if(!empty($order->mid)){
								$sql = $wpdb -> prepare("SELECT nickname FROM  {$wpdb->prefix}wechat_member WHERE mid=%s",$order->mid);
								$order_openid_name = $wpdb->get_var($sql);
								echo $order_openid_name;
							}else{
							    echo $order->openid_name;
							}
						}else{
							echo $order->openid_name;
						}
					?>
					</td>
					<td>
					<?php  
						//echo $order->openid_name;
						echo $order->mid;
					?>
					</td>
					<td>
						<?php 
							echo $order->fee;
						?>
					</td>
					<td>
						<?php echo $this -> TRADE_STATE[$order->trade_state]; ?>
					</td>
					<td>
						<?php 
							if(!empty($order->address)){
								$order_address = json_decode($order->address,true);
								echo $order_address['username'];
							}						
						?>
					</td>
					<td>
						<?php 
							$needdelivery=1;//是否需要发货
							foreach($orderarray[$order->out_trade_no]['ordgoodsinfo'] as $ordergoodsinfo){
								$isdelivery=$ordergoodsinfo->isdelivery;
								if($isdelivery==0){
									$needdelivery=0;
								}
							}
							if($needdelivery==1||empty($orderarray[$order->out_trade_no]['ordgoodsinfo'])){
								echo '无需发货';
							}else{
								if(!empty($orderarray[$order->out_trade_no]['deliverys'])){
									foreach($orderarray[$order->out_trade_no]['deliverys'] as $delivery ){	
										$delivery_status = $delivery->delivery_status;
										if($delivery_status == "1") {echo '已发货';}else if($delivery_status == "2"){echo "收货已确认";}else{echo '未发货';}
									}
								}else{echo '未发货';}
							}
						?>
					</td>
					<td class="row">
						<input type="button" class="btn btn-sm btn-info" onclick="location.href='<?php echo $this->createWebUrl('orderinfo',array('orderid' => $order->out_trade_no));?>'" name="detailinfo" id="buttoninfo" value="订单管理">
					</td>		
				</tr>
			<?php
			}}
			?>
			</tbody>
		</table>
		<?php echo $pager;?>
		</div>
	</div>
</div>
	</form>
	

		
 <script language='javascript'>
	//window.parent.scrollTo(0, 0);//滚动条置顶
	
	$(function(){
		if( $('#range').val() == 'all')
			$("#indata").hide();
		$('#range').change( function(){
				if($(this).val() == 'all'){
					$("#indata").hide();//隐藏
					$("#indata").val("");  
					$("#indata_state").hide();
					
				}else if($(this).val() == 'trade_state'){
					$("#indata").hide();//隐藏
					$("#indata").val("");  
					$("#indata_state").show();
					
				}else{
					$("#indata").show();//显示
					$("#indata_state").hide();	
								
				}
				
			})
		}
	);
	
	$('#range').val('<?php echo $_GET['range'];?>');

	<?php if(!empty($search_condition)&&$search_condition=='trade_state'){?>
	$("#indata_state").show();
	$("#indata").hide();
	$('#indata_state').val('<?php echo $_GET['indata_state'];?>');
	
	<?php }else{ ?>
	
	$("#indata_state").hide();
	$("#indata").show();
	<?php } ?>
	
	function checknull(obj, warning){
		if (obj.value == "") {
			alert(warning);
			obj.focus();
			return true;
		}
		return false;
	}

	function validateform(){
		var selectone = document.getElementById("range"); 
		var index = selectone.selectedIndex;
		var value = selectone.options[index].value;

		var selecttwo = document.getElementById("indata_state"); 
		var indexstate = selecttwo.selectedIndex;
		var valuestate = selecttwo.options[indexstate].value;
		
		if((value != "all")&&(value != "trade_state")){
			
			if (checknull(document.content.indata, "请输入查询内容!") == true) {
				return false;
			}
			return true; 
		}else if(value == "trade_state"){
			if (checknull(selecttwo.options[indexstate], "请选择查询条件!") == true) {
				return false;
			}
			return true;
		}else{
			return true;
		}
	}
	<?php if(empty($list)&&!empty($search_condition)){?>
	alert('没有符合该条件的查询结果');
	<?php } ?>
</script>