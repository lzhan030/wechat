<?php defined('IN_IA') or exit('Access Denied');?>
<html>
<head>
	<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
	<title>正在发起微信支付</title>
	<script type="text/javascript">
		 wx.config({
		    appId: '<?php echo $signPackage["appId"]; ?>',
		    timestamp: <?php echo $signPackage["timestamp"];?>,
		    nonceStr: '<?php echo $signPackage["nonceStr"];?>',
		    signature: '<?php echo $signPackage["signature"];?>',
		    jsApiList: [
		      'chooseWXPay'
		    ]
		  });
		 wx.ready(function(){
		 	wx.chooseWXPay({
			    timestamp: <?php echo $jsapi_data['timeStamp']; ?>, // 支付签名时间戳，注意微信jssdk中的所有使用timestamp字段均为小写。但最新版的支付后台生成签名使用的timeStamp字段名需大写其中的S字符
			    nonceStr: '<?php echo $jsapi_data['nonceStr']; ?>', // 支付签名随机串，不长于 32 位
			    package: '<?php echo $jsapi_data['package']; ?>', // 统一支付接口返回的prepay_id参数值，提交格式如：prepay_id=***）
			    signType: 'MD5', // 签名方式，默认为'SHA1'，使用新版支付需传入'MD5'
			    paySign: '<?php echo $jsapi_data['paySign']; ?>', // 支付签名
			    success: function (res) {
			        window.location.href = "<?php echo $resultUrl ?>"; 
			    },
			    fail: function (res) {
			    	if((res.err_msg !="get_brand_wcpay_request:cancel" )&&(res.err_msg !="get_brand_wcpay_request:fail")){
						alert("微信错误:支付权限禁止");
					}
			        window.location.href = "<?php echo $resultUrl ?>"; 
			    },
			    cancel: function () {
			        window.location.href = "<?php echo $resultUrl ?>"; 
			    }
			});
    	
		});
	</script>
</head>
<body>
</body>
</html>