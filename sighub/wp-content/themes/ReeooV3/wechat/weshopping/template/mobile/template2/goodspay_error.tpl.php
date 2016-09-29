<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
<html>
	<head>
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.css">
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/font-awesome.min.css" />
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/common.mobile.css">
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/messenger.css">
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/messenger-theme-future.css">
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/goodspay.css" />
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/jquery-1.11.1.min.js"></script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/bootstrap.js"></script>
		<title>支付失败</title>				
	</head>
	<body>		
		<div class="research">
			<div style="margin-top:45px">
				<div style=" float:left; width: 84px; height:84px; margin-left: 40px;" name="goodsimg">
					<img class="research-thumb" style="width:83%;height:83%" src="<?php bloginfo('template_directory'); ?>/images/error.gif">
				</div>	
				<div style="height:84px;overflow:hidden;" name="goodsinfo">
					<div style="margin-top:24px;margin-left:10px">
						<font style="font-size:25px;font-weight:bold;" color="#CC3366" >订单<?php echo $errorhint; ?>！</font>
					</div>
				</div>
			</div>
			<div style="margin-left: 51px;margin-top: 3px;">
				<div class="mobile-content">
					<font style="font-family:Microsoft YaHei" color="#666">交易订单号：<?php echo $out_trade_no;?></font>
				</div>
			</div>
		</div>
		<div id="footer"></div> 
	<?php  
	$shoppingtitle='支付失败'; 
	include $this -> template('footer');?>