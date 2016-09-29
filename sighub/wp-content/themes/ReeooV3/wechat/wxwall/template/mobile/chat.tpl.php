<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<?php defined('IN_IA') or exit('Access Denied');?>
<head profile="http://gmpg.org/xfn/11">
	<meta charset="UTF-8" />
	<title><?php bloginfo('name'); ?></title>
	<!--新添加为适应手机的 -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=yes"> 
	<!-- Mobile Devices Support @begin -->
	<meta content="application/xhtml+xml;charset=UTF-8" http-equiv="Content-Type">
	<meta content="no-cache,must-revalidate" http-equiv="Cache-Control">
	<meta content="no-cache" http-equiv="pragma">
	<meta content="0" http-equiv="expires">
	<meta content="telephone=no, address=no" name="format-detection">
	<meta content="width=device-width, initial-scale=1.0" name="viewport">
	<meta name="apple-mobile-web-app-capable" content="yes" /> <!-- apple devices fullscreen -->
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
	
	<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" />
	<!--讨论区滚动条begin-->
	<link rel="stylesheet" type="text/css" href="<?php echo home_url();?>/wp-content/themes/ReeooV3/wechat/wxwall/template/mobile/chat.css?t=<?php echo TIMESTAMP;?>" />
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js"></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.nicescroll.min.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/jquery.mousewheel.js"></script>
	<!-- the jScrollPane script -->
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/jquery.jscrollpane.min.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/jquery.md5.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/scroll-startstop.events.jquery.js"></script>
	<style>
	.add_face{background-image: url(<?php echo home_url();?>/wp-content/themes/ReeooV3/wechat/wxwall/template/mobile/images/add_emoticons.png);}
	body{background-image: url(<?php echo home_url();?>/wp-content/themes/ReeooV3/wechat/wxwall/template/mobile/images/graybg.jpg);}
	.talk .talk_word .order {background-image: url(<?php echo home_url();?>/wp-content/themes/ReeooV3/wechat/wxwall/template/mobile/images/loop.png);}
	.talk .talk_word .loop {background-image: url(<?php echo home_url();?>/wp-content/themes/ReeooV3/wechat/wxwall/template/mobile/images/loop.png);}
	.talk .talk_word .single {background-image: url(<?php echo home_url();?>/wp-content/themes/ReeooV3/wechat/wxwall/template/mobile/images/loop.png);}
	.jp-container .talk_recordbox .talk_recordtextbg{background-image: url(<?php echo home_url();?>/wp-content/themes/ReeooV3/wechat/wxwall/template/mobile/images/talk_recordtext.png);}
	.jp-container .talk_recordboxme .talk_recordtextbg{background-image: url(<?php echo home_url();?>/wp-content/themes/ReeooV3/wechat/wxwall/template/mobile/images/talk_recordtextme.png);}
	</style>
</head>
<body>
<div class="talk">
	<div class="talk_title"><span>微信墙发言</span></div>
	<div id="talk_record" class="talk_record jp-container">
		<div id="talk_bottom" height="1px"></div>
	</div>
	
	<div class="talk_word" scroll="no">
		&nbsp;
		<input class="add_face" id="facial" type="button" title="添加表情" value="" style="display:none"/>
		<input id="talk_message" class="messages emotion" autocomplete="off" placeholder="在这里输入发言内容"/>
		<input id="talk_send" class="talk_send" type="button" title="发送" value="发送" />
	</div>
</div>
<script>
	var user_avatar = "";
	var wall_avatar = "";
	var nickname = "";
	function getAvatarUrl(type){
		if(type=="user")
			return "<?php echo home_url();?>/wp-content/themes/ReeooV3/wechat/wxwall/template/avatar/"+user_avatar+'.png';
		else
			return "<?php echo home_url();?>/wp-content/themes/ReeooV3/wechat/wxwall/template/avatar/avatar_"+wall_avatar+'.jpg';
	}
	
	function formatTimeZero(n){
			 return n < 10 ? '0' + n : '' + n;
	}
	function getCurrentDateTime(){
		currentDate = new Date();
		return currentDate.getFullYear()+ '-' +formatTimeZero(currentDate.getMonth()+1)+ '-' + formatTimeZero(currentDate.getDate())+' '+formatTimeZero(currentDate.getHours())+ ':' +formatTimeZero(currentDate.getMinutes())+ ':' +formatTimeZero(currentDate.getSeconds());
	}
	
	function addWallMessage(msg,askforNickname){
		$('#talk_bottom').before('<div class="talk_recordbox" data-askforNickname="'+askforNickname+'">'+
			'<div class="user"><img src="'+getAvatarUrl("wall")+'"/><p>微信墙</p></div>'+
			'<div class="talk_recordtextbg">&nbsp;</div>'+
			'<div class="talk_recordtext">'+
				'<h3>'+msg+'</h3>'+
				'<span class="talk_time">'+getCurrentDateTime()+'</span>'+
			'</div>'+
		'</div>');
		}
	function addUserMessage(msg){
		$('#talk_bottom').before('<div class="talk_recordboxme">'+
			'<div class="user"><img src="'+getAvatarUrl("user")+'"/><p>'+nickname+'</p></div>'+
			'<div class="talk_recordtextbg">&nbsp;</div>'+
			'<div class="talk_recordtext">'+
				'<h3>'+msg+'</h3>'+
				'<span class="talk_time">'+getCurrentDateTime()+'</span>'+
			'</div>'+
		'</div>');
	}
	$(function(){
		wall_avatar = Math.floor(Math.random()*11+1);
		addWallMessage("请输入昵称。","1");
		$('#talk_send').click(function(){
			message=$.trim($('#talk_message').val());
			$('#talk_message').val("");
			if($('.talk_recordbox:last').data('askfornickname')==1){
				if(message==""){
					alert("昵称不能为空，请重新输入");
					return false;
				}
				nickname = $.trim(message);
				user_avatar = parseInt($.md5(message).substring(0,4),16)%20;
				addUserMessage(message);
				addWallMessage("昵称已设置!","0");
			}else{
				if(message==""){
					alert("请输入发言内容");
					return false;
				}
				addUserMessage(message);
				jQuery.post(
					"<?php echo $this->createMobileUrl('UserMessage',array()); ?>",
					{'id':'<?php echo $_GET['id']?>','avatar':user_avatar,'nickname':nickname,'content':message},
					function(data){
						addWallMessage(data.message,"0");
						location.href="#talk_bottom";
					},
					'json'
				).fail(
					function(){
						addWallMessage("网络异常，请重试!","0");
						location.href="#talk_bottom";
					});
				}
			location.href="#talk_bottom";
			});
			
			$('#talk_message').keyup(function(event){
			  if(event.keyCode ==13){
				$('#talk_send').click();
			  }
			});
		
	});
	
</script>

<div style="text-align:center;margin:50px 0; font:normal 14px/24px 'MicroSoft YaHei';">
<?php 
	$gweidname = get_bloginfo('name','display');
	share_page_in_wechat($_GET['gweid'], array(
	'title' => $gweidname.'- 微信墙 ',
	'desc' => "点击参与 {$gweidname} 的微信墙活动!",
	'link' => 'http://'.$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] . '&wxref=mp.weixin.qq.com' ));
?>
</body>
</html>