<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css" />
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/gradeclass.css" />
		<title>生成商品二维码</title>
	</head>
<div>
	<div class="main-title" style="margin-left:30px;">
		<div class="title-1">当前位置：<a href="<?php echo $this->createWebUrl('index',array());?>">微支付</a> > <a href="<?php echo $this->createWebUrl('qrcodemanage',array());?>">原生支付商品列表</a> > <font class="fontpurple">创建新原生支付商品 </font>
		</div>
	</div>
	<div class="bgimg"></div>
		<form id="createproqr" action="" method="post" onsubmit="return checkinputinfo();">
		<table width="400" height="300" border="0" cellpadding="20px" style="margin-left: 10%; margin-top:15px;" id="table1">
			<tr>
				<td><label for="proname">商品名称: </label></td>		
				<td><input type="text" id="product_name" class="form-control" name="product_name"  > </td>
			</tr>
			<tr>
				<td><label for="proprice">商品价格(元): </label></td>	
				<td><input type="text" id="product_price" class="form-control"  onblur="return ValidateNumber(this,value)" name="product_price" > </td>
			</tr>
			<tr>
				<td><label for="prodescription">商品描述: </label></td>		
				<td>
					<textarea type="text" id="product_description" class="form-control" name="product_description" placeholder="商品描述必填"></textarea>
				</td>
			</tr>
			<tr>
				<td><label for="pronotes">商品备注:</label></td>	
				<td><textarea type="text" id="product_notes" class="form-control" name="product_notes" placeholder="请在此输入您添加的商品型号"></textarea>				
				</td>
			</tr>
        </table>
	<div style="margin-top:3%; margin-left:25%;">
	    <input type="submit"  class="btn btn-primary" value="提交" id="sub1" style="width:70px">
		<a href="<?php echo $this->createWebUrl('qrcodemanage',array());?>"><input type="button" class="btn btn-default" value="取消" id="sub3" style="width:70px; margin-left:20px;"></a>
	</div>
	</form>
</div>
<script>
		function checkinputinfo()
		{
			if($('#product_name').val()==""){
			alert("商品名称不能为空!");
			return false;
			}
			if($('#product_price').val()==""){
			alert("商品价格不能为空!");
			return false;
			}
			if($('#product_description').val()==""){
			alert("商品描述不能为空!");
			return false;
			}
			if(isNaN($('#product_price').val())){
			alert("商品价格不能为非数字");
			return false;
			}
			if(!(/^([1-9]\d+|\d)(\.\d{1,2})?$/).test($('#product_price').val())){
			alert("商品价格最多只能保留小数点后两位");
			return false;
			}
			if( $('#product_price').val()<=0){
			alert("商品价格不能等于0.00");
			return false;
			}
			 if($('#product_price').val()><?php echo WEPAY_MAX_TOTAL_FEE;?>){
			alert("商品价格不能大于<?php echo WEPAY_MAX_TOTAL_FEE;?>");
			return false;
			}
		}
			function ValidateNumber(e, pnumber)
			{
				if(pnumber=='')
					return false;
				if (!(/^([1-9]\d+|\d)(\.\d{1,2})?$/).test(pnumber))
				{
					e.value = /^\d+[.]?\d*/.exec(e.value);
					alert("商品价格不能为非数字，数值最多只能保留小数点后两位");
				}
				
				return false;
			}
</script>
</html>