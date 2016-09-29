<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/uploadexcel.css">
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/init.css">
<style>
	.panel{border-radius: 0px; -webkit-box-shadow: 0 0px 0px rgba(0,0,0,0.05); box-shadow: 0 0px 0px rgba(0,0,0,0.05);}
	.sltfield{height: 34px;padding: 6px 12px;font-size: 14px;line-height: 1.428571429;color: #555;vertical-align: middle;background-color: #fff;background-image: none;border: 1px solid #ccc;border-radius: 4px;-webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);-webkit-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;}
</style>

<div class="main_auto">
	<div class="main-title">
	<div class="title-1">当前位置：<a href="<?php echo $this->createWebUrl('index',array());?>">微支付</a> > <font class="fontpurple">退款管理</font></div>
</div>
<div class="panel panel-default" style="margin-right:50px; margin-top:20px">
	<div class="panel-heading">
		<form name ="content" onSubmit="return validateform()" action="" method="get" enctype="multipart/form-data">	
			 <select id="range" name="range" class="sltfield" style="margin-right:3px">
				<option value="">请选择
				<option value="all">全部</option>
				<option value="refund_id">退款编号</option>
				<option value="order_id">订单编号</option>
				<option value="refund_reason">退款原因</option>
				<option value="refund_state">退款状态</option>
			 </select>
			<input type="hidden" id="module" name="module" value="<?php echo $_GET['module'];?>" />
			<input type="hidden" id="do" name="do" value="<?php echo $_GET['do'];?>" />
			<input type="hidden" id="gweid" name="gweid" value="<?php echo $_GET['gweid'];?>" />
			<input id="indata" class="sltfield" name="indata" value="<?php echo $_GET['indata'];?>" />
			<select id="indata_state" name="indata_state" class="sltfield" style="margin-right:3px">
				<option value="">请选择</option>
				<?php foreach($this->REFUND_STATUS as $status_key => $status_value){?>
				<option value="<?php echo $status_key;?>"><?php echo $status_value;?></option>
				<?php }?>
			 </select>
			 <select id="indata_reason" name="indata_reason" class="sltfield" style="margin-right:3px">
				<option value="">请选择</option>
				<?php foreach($this->REFUND_REASON as $status_key => $status_value){?>
				<option value="<?php echo $status_key;?>"><?php echo $status_value;?></option>
				<?php }?>
			 </select>
			<input type="hidden" name="beIframe" value="1">
			<input class="btn btn-info btn-sm" type="submit" value="查询"/>
		</form>
	</div>
	<table class="table table-striped" width="800" border="0" align="center">
		<tbody>
			<tr>
				<td scope="col" width="100" align="center" style="font-weight:bold">退款编号</td>
				<td scope="col" width="100" align="center" style="font-weight:bold">订单编号</td>
				<td scope="col" width="100"  align="center" style="font-weight:bold">退款原因</td>
				<td scope="col" width="100" align="center" style="font-weight:bold">退款金额</td>
				<td scope="col" width="100" align="center" style="font-weight:bold">退款时间</td>
				<td scope="col" width="100" align="center" style="font-weight:bold">退款状态</td>
			</tr>
			<?php
				if(is_array($list) && !empty($list)){
					foreach($list as $element){
				 ?>
			<tr>
				<td align="center"><?php echo $element['out_refund_no']; ?> </td>
				<td align="center"><a href="<?php echo $this -> createWebUrl('orderinfo',array('orderid' => $element['out_trade_no']));?>"><?php echo $element['out_trade_no']; ?></a></td>
				<td align="center"><?php echo $this -> REFUND_REASON[$element['reason']]; ?></td>
				<td align="center">￥<?php echo number_format($element['refund_fee'],2,'.',''); ?></td>
				<td align="center"><?php echo $element['time_start']; ?></td>
				<td align="center"><?php echo $this -> REFUND_STATUS[$element['refund_status']]; ?><?php if(in_array($element['refund_status'],array('NOTSURE','CREATEFAIL'))){?><a href="javascript:retry_create_refund('<?php echo $element['out_refund_no']; ?>')"><p>[重试]</a><?php }?></td>
			</tr>
			<?php }}?>
		</tbody>
	</table>
	
</div>
<?php echo $pager;?>
 <script language="javascript">
 	$(function(){
		if( $('#range').val() == 'all')
			$("#indata").hide();
		$('#range').change( function(){
				if($(this).val() == 'all'){
					$("#indata").hide();//隐藏
					$("#indata").val("");  
					$("#indata_state").hide();
					$("#indata_reason").hide();
				}else if($(this).val() == 'refund_state'){
					$("#indata").hide();//隐藏
					$("#indata").val("");  
					$("#indata_state").show();
					$("#indata_reason").hide();
				}else if($(this).val() == 'refund_reason'){
					$("#indata").hide();//隐藏
					$("#indata").val("");  
					$("#indata_state").hide();
					$("#indata_reason").show();
				}else{
					$("#indata").show();//显示
					$("#indata_state").hide();	
					$("#indata_reason").hide();					
				}
				
			})
		}
	);
	$('#range').val('<?php echo $_GET['range'];?>');

	<?php if(!empty($search_condition)&&$search_condition=='refund_state'){?>
	$("#indata_state").show();
	$("#indata").hide();
	$('#indata_state').val('<?php echo $_GET['indata_state'];?>');
	$("#indata_reason").hide();
	<?php }elseif(!empty($search_condition)&&$search_condition=='refund_reason'){?>
	$("#indata_state").hide();
	$("#indata").hide();
	$("#indata_reason").show();
	$('#indata_reason').val('<?php echo $_GET['indata_state'];?>');
	<?php }else{ ?>
	$("#indata_reason").hide();
	$("#indata_state").hide();
	$("#indata").show();
	<?php } ?>
function validateform()
{
	var range = $('#range').val();
	var data = "";
	if(range == "all")
		return true;
		
	if(range == ""){
		alert("请选择查询条件！");
		return false;
	}
	
	if(range == "refund_reason")
		data = $('#indata_reason').val();
	else if(range == "refund_state")
		data = $('#indata_state').val();
	else
		data = $('#indata').val();
		
	if(data==""){
		alert("请输入查询内容");
		return false;
	}
	return true;
	  //alert(value);
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
	<?php if(empty($list)&&!empty($search_condition)){?>
	alert('没有符合该条件的查询结果');
	<?php } ?>
</script>