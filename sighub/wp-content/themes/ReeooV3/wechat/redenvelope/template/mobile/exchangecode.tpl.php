<?php defined('IN_IA') or exit('Access Denied');?>
<?php //include $this -> template('header');?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
	<meta charset="UTF-8" />
	<title><?php bloginfo('name'); ?><?php wp_title(); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" />
	<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="http://feeds.feedburner.com/reeoo" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css">
    <link rel="stylesheet" href="<?php echo home_url();?>/wp-content/themes/ReeooV3/wechat/scratchcard/template/css/common.css">
	<?php 
		if ( is_singular() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );
		wp_head();
	?>
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery-1.10.2.min.js"></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.form.js" type="text/javascript" ></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?php echo home_url();?>/wp-content/themes/ReeooV3/wechat/scratchcard/template/js/common.js"></script>	

	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/init.css">
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css" />
	<style>
		.alert-warning{
			border: 1px #FC7C7C solid;
			background: rgba(255, 255, 255, 0.3) none repeat scroll 0 0 !important;
			box-shadow: 0 3px 5px rgba(0,0,0,.13);
		}

	</style>
</head>
<body class="code">
	<div class="main_auto" style="padding-left:0px;">
		<div style="width:100%;text-align:center;margin-top:8%;font-size: 46px;font-weight: bold;color: yellow;">
			<p>红包活动兑换</p>
		</div>
		<div style="width:100%;text-align:center;">
			<form name ="mysetting" id="mysetting" onSubmit="return validateform();" action="<?php echo $this -> createWebUrl('scratchcardEdit',array( 'gweid' => $gweid))?>" method="post" enctype="multipart/form-data">
				<input type="hidden" name="id" value="<?php echo $reply['id'];?>" />
				<div style="width:690px; margin-left:25%;margin-top:60px; color:black; height:300px;" class="alert alert-warning" role="alert">
					<div style="margin-left: 50px; margin-top:30px;" id="table2">
						
						<div class="ai-line">
							<div class="ai-label" style="line-height: 30px;font-size:16px;color:#FFF;"><label>请输入兑换码:</label></div>
							<div class="ai-left"><input style="width:280px;" type="text" value="" class="form-control" id="exchangecode" name="exchangecode"></div>
							<div class="ai-left" style="line-height: 30px;"><input style="margin-left:20px;width:120px;" class="btn btn-warning"  type="button" onclick="javascript:getresults()" name="apply" id="buttondel" value="查看"></div>
						</div>	
						<!--result display-->
						<div class="ai-line" style="text-align: center; padding-top: 35px; margin-left: -50px; ">
							<span id="result"></span>
						</div>
						
						<!--<div id="confirmcancel" class="ai-line" style="display:none; margin-left: 150px; margin-top: 15px;">
							<div class="ai-left"><input style="margin-left:20px;font-size:15px;" class="btn btn-sm btn-info"  type="button" onclick="javascript:confirma()" name="confirmaward" id="confirmaward" value="确认领奖"></div>
							<div class="ai-left" style=""><input style="margin-left:20px;font-size:15px;" class="btn btn-sm btn-default"  type="button" onclick="javascript:cancela()" name="cancelaward" id="cancelaward" value="取消领奖"></div>
						</div>	-->
						
						<div id="confirmcancel" class="ai-line" style="display:none; margin-left: 215px; margin-top: 15px;">
							<div class="ai-left"><input style="margin-left:20px;font-size:15px;" class="btn btn-sm btn-info"  type="button" onclick="javascript:confirma()" name="confirmaward" id="confirmaward" value="确认领奖"></div>
						</div>	
						
					</div>	
				</div>
			</form>
		</div>
	</div>
</body>
<script type="text/html" id="scratchcard-form-html">
</script>
<script type="text/javascript">
$(function($){
	$(".eggList li").click(function() {
		var posL = $(this).position().left + $(this).width()-30;
		$("#hammer").show().css('left', posL);
		$(this).children("span").hide();
		eggClick($(this));
	});
});
$(function (){ 
	$('.code').css("background-image","url(<?php echo home_url().'/wp-content/themes/ReeooV3/wechat/redenvelope/template/images/exchangecode.png'?>)"); 
	$(".code").css("padding","0px");
	$(".code").css("margin","0px");
	$(".code").css("background-size","cover");
	$(".code").css("background-color","#F9F9F9");
	
}); 
var sendid;
function getresults(){
	if($("#exchangecode").val() == ""){
		alert("请先输入兑换码!");
	}else{
	    //兑换码的获取
		$.ajax({
			url: window.location.href, 
			type: "POST",
			data:{'exchangecode_submit':'exchangecode','exchangecode':$("#exchangecode").val(), 'id':<?php echo $id;?>},
			cache: false, 
			async: false,             //这个属性指的是，执行完 $.ajax() 中的所有内容之后，再往下继续执行
			success: function(data){
				if (data.status == 'error'){
					//alert(data.message);
					$('#confirmcancel').css("display","none");
					$('#result').html('<font color="#FFF;" size=3>尊敬的用户：'+data.membername+'，您好，很抱歉！<br/></font><font color="#FFF;" size=3>'+data.message+'(兑换金额:<font  color="Red" size=6 style="font-weight: bold;margin-left:5px;">'+data.amount+'￥</font>)</font>');
				}else if (data.status == 'errorcode'){
					//alert(data.message);
					$('#confirmcancel').css("display","none");
					$('#result').html('<font color="#FFF;" size=3>尊敬的用户，很抱歉！<br/></font><font color="#FFF;" size=3>'+data.message+'</font>');
				}else if (data.status == 'expire'){
					//alert(data.message);
					$('#confirmcancel').css("display","none");
					$('#result').html('<font color="#FFF;" size=3>尊敬的用户：'+data.membername+'，很抱歉！<br/></font><font color="#FFF;" size=3>您的领奖日期已截止，逾期不能领奖</font><font  color="#FFF;" size=3>。</font><br/><font color="#FFF;" size=3>谢谢参与！</font>');
				}else if (data.status == 'success'){
				    sendid = data.sendid;
				    if(data.awardstatus == 0){
						$('#result').html('<font color="#FFF;" size=3>尊敬的用户：'+data.membername+'，恭喜您！<br/></font><font color="#FFF;" size=3>'+data.message+'</font><font  color="Red" size=6 style="font-weight: bold;margin-left:5px;">'+data.amount+'￥</font><label class="statusaward">(未领取)</label>');
						$('#confirmcancel').css("display","block");
					}else{
						$('#result').html('<font color="#FFF;" size=3>尊敬的用户：'+data.membername+'，恭喜您！<br/></font><font color="#FFF;" size=3>'+data.message+'</font><font  color="Red" size=6 style="font-weight: bold;margin-left:5px;">'+data.amount+'￥</font><label class="statusaward">(已领取)</label>');
						$('#confirmcancel').css("display","none");
					}
				}			
			},
			 error: function(data){
				yes = false;
				alert("出现错误");
			},
			dataType: 'json'
		});	
	}
}
function confirma(){
    //alert("here");
	//alert($("#exchangecode").val());
	 //确认领奖
	$.ajax({
		url: window.location.href, 
		type: "POST",
		data:{'confirm_award':'confirmaward','exchangecode':$("#exchangecode").val(), 'id':<?php echo $id;?>, 'sendid':sendid},
		cache: false, 
		async: false,             //这个属性指的是，执行完 $.ajax() 中的所有内容之后，再往下继续执行
		success: function(data){
			if (data.status == 'error'){
				alert(data.message);
			}else if (data.status == 'success'){
				alert(data.message);
				$('#confirmcancel').css("display","none");
				$(".statusaward").html("(已领取)");
				
			}			
		},
		 error: function(data){
			yes = false;
			alert("出现错误");
		},
		dataType: 'json'
	});	

}
function cancela(){
	 //取消领奖
	if(confirm("确定要取消领奖吗？")){
		$.ajax({
			url: window.location.href, 
			type: "POST",
			data:{'cancel_award':'cancelaward','exchangecode':$("#exchangecode").val(), 'id':<?php echo $id;?>},
			cache: false, 
			async: false,             //这个属性指的是，执行完 $.ajax() 中的所有内容之后，再往下继续执行
			success: function(data){
			    //alert(data);
				if (data.status == 'error'){
					alert(data.message);
				}else if (data.status == 'success'){
					alert(data.message);
					$('#confirmcancel').css("display","none");
					$('#result').empty();
					$('#result').html('<font color="#c09853;" size=3>该兑换码已取消领奖!</font>');
				}			
			},
			 error: function(data){
				yes = false;
				alert("出现错误");
			},
			dataType: 'json'
		});	
	}
}

</script>
