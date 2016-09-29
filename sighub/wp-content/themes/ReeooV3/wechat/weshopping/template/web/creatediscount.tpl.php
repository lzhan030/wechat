<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css" />
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/gradeclass.css" />
		<title>优惠信息</title>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/calendar.js"></script>
	</head>
	<div class="main_auto">
		<div class="main-title">
			<div class="title-1">当前位置：<a href="<?php echo $this->createWebUrl('index',array());?>">订单详情</a> > <font class="fontpurple">优惠处理</font></div>
		</div>
		<form name ="content" onSubmit="return validateform()" action="" method="post" enctype="multipart/form-data">
			<div class="panel panel-default" style="margin-right:30px; margin-top:25px">
				<div class="panel-heading">添加优惠信息</div>
				<table width="400" height="250" border="0" cellpadding="20px" style="margin-left: 23%; margin-top:15px;" id="table2">
					<tr>
						<td><label for="orderid">订单编号: </label></td>
						<td><?php echo $orderid?></td>
					</tr>
					<tr>
						<td><label for="discount_type">优惠类型: </label></td>		
						<td>
							<select id="discount_type" name="discount_type" class="form-control">
								<option value=''>请选择</option>
								<option value='MANUAL' <?php if($discountinfo->discount_type == 'MANUAL') echo 'selected="selected"'; ?>>人工优惠</option>
								<option value='SCRATCHCARD'<?php if($discountinfo->discount_type == 'SCRATCHCARD') echo 'selected="selected"'; ?>>刮刮卡</option>
								<option value='OTHER' <?php if($discountinfo->discount_type == 'OTHER') echo 'selected="selected"'; ?>>其他原因</option>
							</select>
						</td>
					</tr>
					<tr>
						<td><label for="discount_price">优惠金额: </label></td>
						<td><input type="text" id="discount_price"  class="form-control" name="discount_price" value="<?php echo $discountinfo->discount_price; ?>" > 最多可优惠<?php echo number_format ( $max_discount_fee , 2 , '.' , '' );?>元</td>
					</tr>
				</table>
				<div style="margin-top:2%;margin-bottom:2%;margin-left:36%;">
					<input type="submit" class="btn btn-primary" value="提交" id="checkinfo" style="width:70px">
					<input type="button" class="btn btn-default" onclick="closew()" value="关闭" id="sub3" style="width:70px; margin-left:20px;">
				</div>
			</div>
		</form>
	</div>
	<script language='javascript'>
		function validateform(){
			if($('#discount_type').val() == ""){
				alert("请选择优惠类型");
				return false;
			}else if($('#discount_price').val() == ""){
				alert("优惠金额不能为空");
				return false;
			}else if($('#discount_price').val()==0){
				alert("优惠金额不能等于0");
				return false;
			}else if (!/^\d+[.]?\d*$/.test($('#discount_price').val())){
				alert("请填写正确的金额");
				return false;
			}else if($('#discount_price').val()>=<?php echo number_format ( $max_discount_fee , 2 , '.' , '' );?>){
				alert("优惠金额不能高于<?php echo number_format ( $max_discount_fee , 2 , '.' , '' );?>元!");
				return false;
			}else if(!(/^([1-9]\d+|\d)(\.\d{1,2})?$/).test($('#discount_price').val())){
				alert("金额最多只能保留小数点后两位");
				return false;
			}
			return true;
		}
		function closew(){
			//window.opener=null;
			setTimeout('self.close()',0);	
			
		}
				
	</script>
</html>