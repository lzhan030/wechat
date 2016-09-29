<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css" />
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/gradeclass.css" />
		<title>发货</title>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/calendar.js"></script>
	</head>
	<div class="main_auto">
		<div class="main-title">
			<div class="title-1">当前位置：<a href="<?php echo $this->createWebUrl('index',array());?>">订单详情</a> > <font class="fontpurple">发货处理</font></div>
		</div>
		<form name ="content" onSubmit="return validateform('<?php echo $trade_state ?>')" action="" method="post" enctype="multipart/form-data">
			<div class="panel panel-default" style="margin-right:30px; margin-top:25px">
				<div class="panel-heading">发货</div>
				<table width="400" height="250" border="0" cellpadding="20px" style="margin-left: 23%; margin-top:15px;" id="table2">
					<tr>
						<td width="150"><label for="orderid">订单编号: </label></td>
						<td><?php echo $orderid?></td>
					</tr>
					<tr <?php if(($delivery_status == "0")||(!isset($delivery_status))){?> style='display:none'  <?php }?> >
						<td width="150"><label for="delivery_timestamp">发货时间: </label></td>
						<td><?php echo $delivery_timestamp?></td>
					</tr>
					<tr>
						<td width="150"><label for="delivery_compid"><?php if($order_isshopping == 1){?>配送方式:<?php }else{?>快递公司:<?php }?> </label></td>		
						<td>
							<?php if($order_isshopping == 1){?> <!--如果是微商城的订单,则列出所有的配送方式-->
							<select id="delivery_compid" name="delivery_compid" class="form-control" size="1"  value="" style="height:35px ;maxlength:20;" readonly="true">
								<!--<option value="0">请选择</option>
								<option optionid="-1" value="-1" <?php if($order_dispatchid == -1) echo 'selected="selected"';?>>其它</option>-->
								<?php if(!empty($dispatchs)){
										foreach($dispatchs as $dispatch ){	
											$dispatch_id = $dispatch->id;	
											$dispatch_name = $dispatch->dispatchname;	
									?>
									<option optionid="<?php echo $dispatch_id;?>" value="<?php echo $dispatch_name;?>" <?php if($order_dispatchid == $dispatch_id) {echo 'selected="selected"';}else{ echo 'disabled'; }?>><?php echo $dispatch_name;?></option>
									
								<?php }}?>					
							</select>
							<!--用于传递当前选择的下拉列表项对应的id值<input type="hidden" id="dispatch_newid" class="form-control" name="dispatch_newid" value="" >-->
							<?php }else{?>
							<input type="text" id="delivery_compid" class="form-control" name="delivery_compid" value="<?php echo $delivery_compid?>" >
							<?php }?>
						</td>
					</tr>
					<!--<tr id="newdispatch" style="display:none;">	
						<td width="150"><label for="delivery_compid">输入新的配送方式：</label></td>		
						<td>	
							<input type="text" id="new_dispatch" class="form-control" name="new_dispatch" value="<?php echo $delivery_compid?>" >
						</td>	
					</tr>-->
					<tr>
						<td width="150"><label for="delivery_sn">物流速递单号: </label></td>
						<td><input type="text" id="delivery_sn" class="form-control" name="delivery_sn" value="<?php echo $delivery_sn?>" > </td>
					</tr>
					<tr>
						<td width="150"><label for="delivery_status">发货状态: </label></td>
						<td>
							<select id="delivery_status" name="delivery_status" class="form-control" size="1"  value="" style="height:35px ;maxlength:20" >
								<option value="0" <?php if($delivery_status == "0") echo 'selected="selected"';?>><?php echo "未发货";?></option>
								<option value="1" <?php if($delivery_status == "1") echo 'selected="selected"';?>><?php echo "已发货";?></option>				
							</select>
						</td>
					</tr>
					<tr>
						<td width="150"><label for="delivery_msg">发货状态信息: </label></td>
						<td><input type="text" id="delivery_msg" class="form-control" name="delivery_msg" value="<?php echo $delivery_msg?>" > </td>
					</tr>
					<!--如果商家修改快递，可以在备注中填写-->
					<tr>
					    <td width="150"><label for="remark">备注: </label></td>
					    <td><textarea type="text" id="remark" class="form-control" name="remark"><?php echo $remark?></textarea>
						</td>
					</tr>
				</table>
				<div style="margin-top:2%;margin-bottom:2%;margin-left:36%;">
					<input type="hidden" value="<?php echo $orderid; ?>" name="orderid">
					<input type="submit" class="btn btn-primary" value="提交" id="checkinfo" style="width:70px">
					<input type="button" class="btn btn-default" onclick="closew()" value="关闭" id="sub3" style="width:70px; margin-left:20px;">
					<!--<a href="<?php echo $this->createWebUrl('orderinfo',array('orderid' => $orderid));?>"><input type="button" class="btn btn-default"  value="取消" id="sub3" style="width:70px; margin-left:20px;"></a>-->
				</div>
			</div>
		</form>
	</div>
	<script language='javascript'>
		function validateform(trade_state){
			if(document.getElementById('delivery_status').value == "1"){
				//如果是微商城的订单，这里还需另外判断
				<?php if($order_isshopping == 1){?>
				    /* if($('#delivery_compid').val() == "-1"){
						if(document.getElementById('new_dispatch').value == ""){
							alert("请填写新的快递公司");
							return false;
						}
					}else{ */
					if(document.getElementById('delivery_sn').value == ""){
						alert("请填写物流单号");
						return false;
					}else if(trade_state!='SUCCESS'){
							if(confirm("该订单并非支付成功状态，确定发货吗?")){
								return true;
							}
							return false;
					}
					/* } */
				<?php }else{?>
					if(document.getElementById('delivery_compid').value == ""){
						alert("请填写快递公司");
						return false;
					}else if(document.getElementById('delivery_sn').value == ""){
						alert("请填写物流单号");
						return false;
					}else if(trade_state!='SUCCESS'){
							if(confirm("该订单并非支付成功状态，确定发货吗?")){
								return true;
							}
							return false;
					}
				<?php }?>
			}
			return true;
		}
		function closew(){
			//window.opener=null;
			setTimeout('self.close()',0);	
			
		}	
		//如果配送方式中选择“其他”		
		/* $('#delivery_compid').change(function(){
		    var dispatchid = $("#delivery_compid").find("option:selected").attr("optionid");
			$("#dispatch_newid").val(dispatchid);
			if($(this).val() == "-1"){    
				document.getElementById("newdispatch").style.display = "";
				$("#new_dispatch").val("");
			}else{
				document.getElementById("newdispatch").style.display = "none";
			}
		}); */
		/* $(function() { 
			//如果是微商城的订单，这里还需另外判断
			<?php if($order_isshopping == 1){?>
			if($('#delivery_compid').val() == "-1"){
				document.getElementById("newdispatch").style.display = "";
			}
			<?php }?>
		}); */ 
	</script>
</html>