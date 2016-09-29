<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css" />
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/gradeclass.css" />
		<title>更新商品信息</title>
	</head>
<div style="width: 85%;">
	<form id="productinfoedit" action="" method="post" >
	
	<div class="main-title" style="margin-left:30px;">
		<div class="title-1">当前位置：<a href="<?php echo $this->createWebUrl('index',array());?>">微支付</a> > <a href="<?php echo $this->createWebUrl('qrcodemanage',array());?>">商品信息详情</a> > <font class="fontpurple">更新商品信息 </font>
		</div>
	</div>
	<div class="bgimg"></div>
	<div id="productqr" style="float:right;margin-top:20px;">
		<div><label for="pronotes">商品二维码:</label></div>	
		<div><input type="text" id="qr_code" class="form-control" readonly="readonly" name="qr_code" value="<?php echo $productlist['qr_code']?>" > </div>
		<div><img src='<?php echo $this->createWebUrl('showproductqr',array('product_id' => $productlist['product_id']));?>' width='250' height='250'></div>
		<div><a href="<?php echo $this->createWebUrl('showproductqr',array('product_id' =>  $productlist['product_id'],'download'=>1));?>"><input type="button" class="btn btn-primary"  value="下载图片"  style="position:absolute;margin-left:90px;"></a></div>
	</div>
		<?php
		if( isset($_POST['product_name'])){ ?>
			<script>
				alert('提交成功');
			</script>
		<?php } ?>
		<table width="400" height="400" border="0" cellpadding="20px" style="margin-left: 10%; margin-top:30px;" id="table2">
			<tr>
				<td><label for="proname">商品编号: </label></td>		
				<td><input type="text" id="product_id" class="form-control" readonly="readonly" name="product_id" value="<?php echo $productlist['product_id'];?>" > </td>
			</tr>
			<tr>
				<td><label for="proname">商品名称: </label></td>		
				<td><input type="text" id="product_name" class="form-control" name="product_name" value="<?php echo $productlist['product_name'];?>" > </td>
			</tr>
			<tr>
				<td><label for="proprice">商品价格(元): </label></td>	
				<td><input type="text" id="product_price" class="form-control" name="product_price" onblur="return ValidateNumber(this,value)" value="<?php echo $productlist['product_price'];?>" ></td>
			</tr>
			<tr>
				<td><label for="prodescription">商品描述: </label></td>		
				<td><textarea type="text" id="product_description" class="form-control" name="product_description" placeholder="商品描述必填"><?php echo $productlist['product_description'];?></textarea>
				</td>
			</tr>
			<tr>
				<td><label for="pronotes">商品备注:</label></td>	
				<td><textarea type="text" id="product_notes" class="form-control" name="product_notes" placeholder="请在此输入您添加的商品型号"><?php echo $productlist['product_notes'];?></textarea></td>
			</tr>
        </table>
	<div style="margin-left:30%;">
			<input type="button"  class="btn btn-primary"  onclick="checkproeditinfo();" value="更新" id="sub1" style="width:70px">
			<td><a href="<?php echo $this->createWebUrl('qrcodemanage',array());?>"><input type="button" class="btn btn-default" value="返回" id="sub3" style="width:70px; margin-left:20px;"></a></td>
	</div>
	</form>
</div>
	<script>
		    function checkproeditinfo()
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
				else
				{
					$('#productinfoedit').submit();  			
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