<?php defined('IN_IA') or exit('Access Denied');?>
<style>
	body{padding-bottom:50px;}
</style>
<?php include $this -> template('header');?>
<?php include $this -> template('common');?>
<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/wechat/weshopping/css/style.css">
<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory'); ?>/wechat/weshopping/css/base.css">

<div id="top" style="margin-top: -45px;">
    <div class="header-title1">确认订单</div>
	<div class="site-nav">
		<ul class="fix">
			<li class="home"><a href="<?php echo $this->createMobileUrl('list', array('gweid' => $gweid))?>">首页</a></li>
			<li class="mysn"><a href="<?php echo $this->createMobileUrl('contactUs', array('gweid' => $gweid))?>">小店介绍</a></li>
			<li class="mycart"><a href="<?php echo $this->createMobileUrl('mycart', array('gweid' => $gweid))?>">购物车</a></li>
			<li class="allcate"><a href="<?php  echo $this->createMobileUrl('allcategories', array('gweid' => $gweid))?>">商品分类</a></li>
		</ul>
	</div>
</div>

<div class="add-address img-rounded" id="addAddressPanel" >
	<div class="add-address-main" style="font-size: 14;margin-top: 10px;">
		<div class="form-group">
			<label for="" class="col-sm-3 control-label">订单号：</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" id="username" value="<?php echo $orderid;?>" disabled="disabled">
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-3 control-label">商家名称：</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" id="telnumber" value="<?php bloginfo('name'); ?>" disabled="disabled"  >
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-3 control-label">支付金额(￥)：</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" id="postalcode" value="<?php echo $totalfee; ?>" disabled="disabled">
			</div>
		</div>
		<div class="form-group" style="<?php if(!empty($mid)){?> <?php }else{?> display:none; <?php }?> ">
			<label for="" class="col-sm-3 control-label">花费积分：</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" id="postalcode" value="<?php echo $totalpoint; ?>" disabled="disabled">
			</div>
		</div>
		<!--$needdiscount==0表示不需要优惠金额-->
		<div class="form-group" style="<?php if($needdiscount==0){ echo 'display:none';} ?>">
			<label for="" class="col-sm-3 control-label">优惠金额(￥)：</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" id="postalcode" value="<?php echo $discount_fee; ?>" disabled="disabled">
			</div>
		</div>
	</div>
	
</div>
<div id="wepay" class="mobile-submit">
	<input type="hidden" name="token" value="" />
	<?php if($orderdispatchtype == 0){?>
	<input type="button" class="btn btn-large btn-success" style="width:100%;font-size: 15px;" onClick="callpayconfirm('<?php echo number_format($totalfee,2,".","");?>')" value="微信支付"></button><br><br>
	<?php }elseif($orderdispatchtype == 1){?>
	<span style="font-size: 14px; margin-left: 15px;"> 您选择了货到付款的方式，请等待快递方联系您</span>
	<?php }else{?>
	<span style="font-size: 14px; margin-left: 15px;"> 您选择了自提的方式，请等待卖家或者快递方联系您</span>
	<?php }?>
</div>	

<script language='javascript'>
	isSubmitting = false;
	function callpayconfirm(totalfee){
		if(isSubmitting)
			return false;
			isSubmitting = true;
		$.ajax({
			async:false,
			url:'<?php echo $this -> createModuleMobileUrl('wepay','JSAPIPayOrder',array('gweid' => $_GET['gweid']));?>', 
			type: "POST",
			data:{'order_pay':'isPay','orderid':<?php echo $orderid;?>,'openid':"<?php echo $_W['fans']['from_user'] ?>"},
			success: function(data){
				if (data.status == 'error'){
					if(data.message=='invalid total_fee'){
						alert("金额过大,暂不支持支付");
					}else{
						alert(data.message);
						alert("支付失败,请稍后重试");
					}
				}else if (data.status == 'errordec'){
						alert(data.message);
				}else if (data.status == 'success'){
					location.href = '<?php echo $this -> createModuleMobileUrl('wepay','WeShoppingJSAPIPayOrder',array('gweid' => $_GET['gweid'],'orderid' => $orderid ));?>&prepay_id='+data.prepay_id;					
				}
				isSubmitting = false;
			},
			 error: function(data){
				alert("出现错误,请重试");
				isSubmitting = false;
			},
			dataType: 'json'
		});
	}
</script>
<?php include $this -> template('footer');?>