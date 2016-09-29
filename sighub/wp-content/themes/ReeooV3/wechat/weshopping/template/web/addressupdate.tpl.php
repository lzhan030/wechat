<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css" />
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/gradeclass.css" />
		<title>收货地址</title>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/calendar.js"></script>
	</head>
	<div class="main_auto">
		<div class="main-title">
			<div class="title-1">当前位置：<a href="<?php echo $this->createWebUrl('index',array());?>">订单详情</a> > <font class="fontpurple">修改收货地址</font></div>
		</div>
		<form name ="content" onSubmit="return validateform()" action="" method="post" enctype="multipart/form-data">
			<div class="panel panel-default" style="margin-right:30px; margin-top:25px">
				<div class="panel-heading">收货地址</div>
				<table width="400" height="250" border="0" cellpadding="20px" style="margin-left: 23%; margin-top:15px;" id="table2">
					<tr>
						<td><label for="username">收货人姓名: </label></td>
						<td><input type="text" id="username" class="form-control" name="username" value="<?php echo $order_address['username'];?>" > </td>
					</tr>
					<tr>
						<td><label for="telnumber">联系电话: </label></td>
						<td><input type="text" id="telnumber" class="form-control" name="telnumber" value="<?php echo $order_address['telnumber'];?>" > </td>
					</tr>
					<tr>
						<td><label for="postalcode">邮编: </label></td>
						<td><input type="text" id="postalcode" class="form-control" name="postalcode" value="<?php echo $order_address['postalcode']?>" > </td>
					</tr>
					<tr>
						<td><label for="stagename">地区: </label></td>
						<td><input type="text" id="stagename" class="form-control" name="stagename" value="<?php echo $order_address['stagename']?>" > </td>
					</tr>
					<tr>
						<td><label for="detailinfo">详细地址: </label></td>
						<td><input type="text" id="detailinfo" class="form-control" name="detailinfo" value="<?php echo $order_address['detailinfo']?>" > </td>
					</tr>
				</table>
				<div style="margin-top:2%;margin-bottom:2%;margin-left:36%;">
					<input type="hidden"  value="<?php echo $orderid ?>" name="orderid" style="width:70px">
					<input type="submit" class="btn btn-primary" value="提交" id="checkinfo" style="width:70px">
					<input type="button" class="btn btn-default" onclick="closew()" value="关闭" id="sub3" style="width:70px; margin-left:20px;">
				</div>
			</div>
		</form>
	</div>
	<script language='javascript'>
		function validateform(){
			var re= /^[1-9][0-9]{5}$/;//邮编格式
			if(document.getElementById('username').value == ""){
				alert("收货人姓名不能为空");
				return false;
			}else if(document.getElementById('telnumber').value == ""){
				alert("联系电话不能为空");
				return false;
			}else if(document.getElementById('postalcode').value == ""){
				alert("邮编不能为空");
				return false;
			}else if(!re.test(document.getElementById('postalcode').value)){
				alert("邮编格式不正确");
				return false;
			}else if(document.getElementById('stagename').value == ""){
				alert("地区不能为空");
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