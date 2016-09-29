<?php defined('IN_IA') or exit('Access Denied');?><!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="format-detection" content="telephone=no">
<title>填写资料</title>
<link type="text/css" rel="stylesheet" href="<?php echo home_url() ?>/wp-content/themes/ReeooV3/wechat/vote/style/vote.css" />

</head>
<body>
<div class="wrapper">
	<img class="bg" src="./source/modules/vote/style/images/bg.jpg">
	<div class="inner-cont">
		<div class="qtitle">请先填写您的资料：</div>
		<div class="field-contain">
			<label for="username" class="input-labe">请输入您的名称:</label>
			<input type="text" name="username" id="username" class="input-text" value="">
		</div>
		<div class="field-contain">
			<label for="phone" class="input-labe">请输入您的手机号码:</label>
			<input type="tel" name="phone" id="phone" class="input-text" value="">
			<span class="tip">*请务必填写正确，此手机号将作为您以后领奖的依据</span>
		</div>
		<div class="btn-wrapper">
			<button id="save-btn" class="next-btn">开始投票</button>
		</div>
	</div>
 	<p class="page-url">
		<a href="" target="_blank" class="page-url-link"></a>
	</p>
</div>
</body>
</html>
