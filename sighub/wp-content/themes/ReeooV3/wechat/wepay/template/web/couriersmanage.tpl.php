<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css" />
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/gradeclass.css" />
		<title>物流公司</title>
	</head>
	<div class="main_auto">
		<div class="main-title">
			<div class="title-1">当前位置：<a href="<?php echo $this->createWebUrl('index',array());?>">微支付</a> > <font class="fontpurple">物流公司</font></div>
		</div>
		<form name ="content" onSubmit="return validateform()" action="" method="post" enctype="multipart/form-data">
			<div class="panel panel-default" style="margin-right:30px; margin-top:25px">
				<div class="panel-heading">物流公司</div>
				<table width="400" height="150" border="0" cellpadding="20px" style="margin-left: 23%; margin-top:15px;" id="table2">
					<tr>
					<?php foreach($couriers as $courier){
								$courier_name=$courier->courier_name;
					}?>
						<td><label for="couriers_name">物流公司名称: </label></td>
						<td><input type="text" id="courier_name" class="form-control" name="courier_name" value="<?php echo $courier_name?>" > </td>
					</tr>
					
				</table>
				<div style="margin-top:2%;margin-bottom:2%;margin-left:36%;">
					<input type="hidden" name="courier_id" value="<?php echo $courier->id ?>"/>
					<input type="submit" class="btn btn-primary" value="提交" id="checkinfo" style="width:70px">
					<input type="button" class="btn btn-default" onclick="closew()" value="关闭" id="sub3" style="width:70px; margin-left:20px;">
				</div>
			</div>
		</form>
	</div>
	<script language='javascript'>
		function validateform(){
			if(document.getElementById('courier_name').value == ""){
				alert("物流公司名称不能为空");
				return false;
			}
			return true;
		}
		function closew(){
			setTimeout('self.close()',0);	
			
		}		
	</script>
</html>