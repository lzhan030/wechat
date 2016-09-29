<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>

<?php if($scratchcard['maxlottery'] && $total < $scratchcard['maxlottery']) { ?>
<div class="cover">
	<?php 
	 if(empty($scrpicture)){
	?>
		<img src="<?php bloginfo('template_directory'); ?>/images/bg.png">
	 <?php }else{ ?>
		<img src="<?php echo $scrpicture; ?>">
	<?php } ?>
	<div id="prize"></div>
	<div id="scratchpad"></div>
</div>
<?php } else { ?>
<div class="alert-error alert mobile-alert">
	<h4><?php echo $message;?></h4>
</div>
<?php } ?>
<div class="mobile-div img-rounded" id="myaward">
	<div class="mobile-hd"><i class="icon-sort pull-right"></i>我的奖品</div>
	<div class="myaward">
		<?php if(!empty($myaward) || ($mycredit > 0)) { ?>
		<div class="mobile-li alert-error"><span class="pull-right"><?php echo $mycredit;?></span><span class="text-error">奖励积分</span></div>
		<?php if(is_array($myaward)) { foreach($myaward as $v) { ?>
		<div class="mobile-li" id="<?php echo $v['id'];?>" data-toggle="collapse" data-target="#myaward-<?php echo $v['id'];?>"><i class="icon-hand-up pull-right"></i><span class="pull-right myaward-status <?php if($v['status']==0) { ?>text-success">未领取<?php } else { ?> text-error">已领取<?php } ?></span><?php echo $v['award'];?></div>
		<div class="collapse" id="myaward-<?php echo $v['id'];?>"><div class="mobile-content"><?php echo $v['description'];?></div></div>
		<?php } } ?>
		<?php } else { ?>
		<div class="mobile-li alert-error"><span class="text-error">暂未中奖</span></div>
		<?php } ?>
	</div>
</div>
<div class="mobile-div img-rounded">
	<div class="mobile-hd">活动说明</div>
	<div class="mobile-content">
		<?php echo $scrrule;?>
	</div>
</div>
<div class="mobile-div img-rounded">
	<div class="mobile-hd" style="border-bottom:0;">奖项设置</div>
	<?php if(!empty($allaward)) { ?>
	<?php if(is_array($allaward)) { foreach($allaward as $v) { ?>
	<span class="mobile-li" data-toggle="collapse" data-target="#content-<?php echo $v['id'];?>">
		<i class="icon-hand-up pull-right"></i>
		<?php echo $v['title'];?>
	</span>
	<div class="collapse" id="content-<?php echo $v['id'];?>">
		<div class="mobile-content">
			<p>奖品名：<?php echo $v['title'];?></p>
			<p><?php echo $v['description'];?></p>
		</div>
	</div>
	<?php } } ?>
	<?php } ?>
	<?php if(!empty($scratchcard['hitcredit'])) { ?>
	<span class="mobile-li" data-toggle="collapse" data-target="#content-hitcredit">
		<i class="icon-hand-up pull-right"></i>
		中奖积分：<?php echo $scratchcard['hitcredit'];?>
	</span>
	<div class="collapse" id="content-hitcredit">
		<div class="mobile-content">
			<p>当用户未中奖时给予的积分</p>
		</div>
	</div>
	<?php } ?>
	<?php if(!empty($scratchcard['misscredit'])) { ?>
	<span class="mobile-li" data-toggle="collapse" data-target="#content-misscredit">
		<i class="icon-hand-up pull-right"></i>
		未中奖积分：<?php echo $scratchcard['misscredit'];?>
	</span>
	<div class="collapse" id="content-misscredit">
		<div class="mobile-content">
			<p>当用户未中奖时给予的积分</p>
		</div>
	</div>
	<?php } ?>
</div>

<script type="text/javascript">
var zjl = true;
var num = 0;
var goon = true;
$(function(){
	var useragent = window.navigator.userAgent.toLowerCase();
	var statu="enable";
	$("#scratchpad").wScratchPad({
		width : 187,
		height : 43,
		color : "#a9a9a7",
		scratchMove : function(){
			if(statu=="enable"){
				statu="diaable";
			}

			if (useragent.indexOf("android 4") > 0) {
				if ($("#scratchpad").css("color").indexOf("51") > 0) {
					$("#scratchpad").css("color", "rgb(50,50,50)");
				} else if($("#scratchpad").css("color").indexOf("50") > 0) {
					$("#scratchpad").css("color", "rgb(51,51,51)");
				}
			}

			num++;
			if (num == 2) {
				$.getJSON("<?php echo $this->createMobileUrl('getAward', array('id' => $id,'gweid' => $gweid))?>&r="+new Date().getTime(),function(res){
					if(res.message.status == -2) {
						alert(res.message.message);
						location.href=res.message.url;
					}
					if(res.message.status == 0) {
						$("#prize").html('<span style="color:red;">中奖了</span>');
					}
					if(res.message.status == -1) {
						$("#prize").html('<span style="">未中奖</span>');
					}
					var myaward = '';
					for(i=0; i<res.message.myaward.length; i++) {
						var a = '';
						if(res.message.myaward[i].status == 0) {
							a = '<span class="pull-right text-success myaward-status">未领取</span>';
						} else {
							a = '<span class="pull-right text-error myaward-status">已领取</span>';
						}
						myaward += '<div id="'+res.message.myaward[i].id+'" class="mobile-li" data-toggle="collapse" data-target="#content-js'+i+'"><i class="icon-hand-up pull-right"></i>'+ a + res.message.myaward[i].award+'</div>';
						myaward += '<div class="collapse" id="content-js'+i+'"><div class="mobile-content">'+res.message.myaward[i].description+'</div></div>';
					}
					$("#myaward .myaward").html(myaward);
					$("#myaward .myaward").prepend('<div class="mobile-li alert-error"><span class="pull-right">'+res.message.credit+'</span><span class="text-error">奖励积分</span></div>');
				});
			}
			if(zjl&&num>100&&goon){
				//alert('刮完了！');
			}
		}
	});
	$("#myaward").delegate(".mobile-li", "click", function(){
		var a = $(this).find('.myaward-status');
		var b = $(this).attr('id');
		/*if (a.length>0 && a.html() != '已领取') {
			if (confirm('是否确定领取使用？')) {
				$.getJSON("<?php echo $this->createMobileUrl('setStatus', array('id' => $id,'gweid' => $gweid))?>&awardid="+b+"&r="+new Date().getTime(),function(res){
					a.removeClass('text-success').addClass('text-error');
					a.html('已领取');
				});
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}*/
		return true;
	});
});
</script>
<?php  include $this -> template('footer');?>