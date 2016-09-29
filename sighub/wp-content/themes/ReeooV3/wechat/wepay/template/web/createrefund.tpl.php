<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
</script>
<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js"></script>
<script src="<?php bloginfo('template_directory'); ?>/js/bootstrap.min.js"></script>
<script src="<?php bloginfo('template_directory'); ?>/js/calendar.js"></script>
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css" />
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/gradeclass.css" />
<div>
	<form id="create_refund" action="" method="post" onsubmit="return checkinputinfo();">
	
	<div class="main-titlenew">
		<div class="title-1"> 创建退款  <font class="fontpurple">  </font>
		</div>
	</div>
	<div class="bgimg"></div>
	<div class="panel panel-default" style="margin-right:30px; margin-left: 30px; ">
		<div class="panel-heading">退款信息 </div>
		<?php
					if(is_array($list) && !empty($list)){?>
		<table class="table table-striped" width="800" border="0" align="center">
			<tbody>
				<tr>
					<td scope="col" width="50" align="center" style="font-weight:bold">退款时间</td>
					<td scope="col" width="50" align="center" style="font-weight:bold">退款编号</td>
					<td scope="col" width="50" align="center" style="font-weight:bold">退款原因</td>
					<td scope="col" width="50" align="center" style="font-weight:bold">退款金额</td>
					<td scope="col" width="50" align="center" style="font-weight:bold">退款状态</td>
				</tr>
					<?php
						foreach($list as $element){
					 ?>
				<tr>
					<td align="center"><?php echo $element['time_start']; ?></td>
					<td align="center"><?php echo $element['out_refund_no']; ?> </td>
					<td align="center"><?php echo $this -> REFUND_REASON[$element['reason']]; ?></td>
					<td align="center">￥<?php echo number_format($element['refund_fee'],2,'.',''); ?></td>
					<td align="center"><?php echo $this -> REFUND_STATUS[$element['refund_status']];?><?php if(in_array($element['refund_status'],array('NOTSURE','CREATEFAIL'))){?><a href="javascript:retry_create_refund('<?php echo $element['out_refund_no']; ?>')"><p>[重试]</a><?php }?></td>
				</tr>
				<?php }?>
			</tbody>
		</table>
		<?php } else {?>
		<div class="panel-body">该订单无退款记录！</div>
		<?php }?>
	</div>
	<table width="380" height="300" border="0" cellpadding="20px" style="margin-left: 150px; margin-top:30px;" id="table2">
		<tbody>
			<tr>
				<td><label for="stunumber">订单编号: </label></td>
				<td width="280"><input type="text" value="<?php echo $orderid; ?>" class="form-control" id="order_id" name="order_id"></td>
				<td></td>
			</tr>
			<tr>
				<td><label for="content">退款原因:</label></td>		
                 <td>
				 <div id="contain">
				 <select id="reason" name="reason" class="form-control">
					<option value=''>请选择</option>
					<option value='SHIPPING_FAILED'>发货失败</option>
					<option value='WRONG_GOODS'>商品错误</option>
					<option value='CONSENSUS'>协商一致</option>
					<option value='WRONG_SHIPPING_FEE'>运费错误</option>
					<option value='OTHER'>其他原因</option>
				</select>
				</div>
				</td>							
			</tr>
			<tr>
				<td><label for="stuvericode">退款金额: </label></td>	
				<td><input type="text" value="" class="form-control" id="refund_fee" name="refund_fee" style="width: 90%;display: inline;"><span style="display: inline;">元</span> 
				精确到分，还可退<?php echo number_format ( $max_refund_fee , 2 , '.' , '' );?>元</td>
			</tr>
		</tbody>
	</table>
	
	
	<div style="margin-top:3%; margin-left:275px;">
	    <input type="submit" class="btn btn-primary" value="提交" id="sub3" style="width:70px">
		<!--<input type="button" class="personentry btn btn-primary" onclick="checkinputinfo()" title="<?php //echo $this->createWebUrl('addstudent');?>" value="保存" id="sub3" style="width:70px">-->
	    <input type="button" onclick="closew()" class="btn btn-default" value="关闭" id="sub3" style="width:70px; margin-left:20px;">
	</div>
	</form>
</div>
<script language="javascript">
	function closew(){
		//window.opener=null;
		setTimeout('self.close()',0);
		<?php if(!$_GET['norefresh']){ ?>
		opener.location.reload();
		<?php } ?>
	}	
	function checkinputinfo(){
		if($('#refund_fee').val()><?php echo number_format ( $max_refund_fee , 2 , '.' , '' );?>){
			alert("退款金额不能高于<?php echo number_format ( $max_refund_fee , 2 , '.' , '' );?>元!");
			return false;
		}
		if(isNaN($('#refund_fee').val()) || $('#refund_fee').val()<=0|| !(/^([1-9]\d+|\d)(\.\d{1,2})?$/).test($('#refund_fee').val())){
			alert("请输入合法的退款金额");
			return false;
		}
		
		if($('#refund_fee').val()==""){
			alert("退款金额不能为空！");
			return false;
		}
		if($('#reason').val()==""){
			alert("请选择退款理由！");
			return false;
		}
		return confirm('您确定对该笔订单退款'+$('#refund_fee').val()+'元吗？');
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
	window.onunload = closew;
</script>