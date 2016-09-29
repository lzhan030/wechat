<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>

<div data-role="page" data-theme="a">
<div data-role="header" data-position="inline">
	<h1><?php  echo $title;?></h1>
</div>
<?php 
 if(!empty($scrpicture)){
?>
	<img src="<?php echo $scrpicture; ?>" width="100%" min-height="30">
<?php } ?>
<div data-role="popup" id="list_winner" data-overlay-theme="a" data-theme="c" data-dismissible="false" class="ui-corner-all">
	<div data-role="header" data-theme="c">
		<span style="color:red;font-weight:600;float:right;line-height:35px;padding-right:10px;">可划动 ^</span>
		<a href="#" data-rel="back" data-icon="delete" data-iconpos="notext">关闭</a>
		<h1>中奖名单</h1>
	</div>
	<div style="height:150px; overflow-y:scroll;">
		<table class="list_table">
			<thead>
				<tr>
					<th data-priority="persist" style="width:200px;">姓名</th>
					<th data-priority="persist" style="width:500px;">奖品</th>
				</tr>
			</thead>
			<tbody>
				<?php  if(is_array($otheraward)) { foreach($otheraward as $index => $row) { ?>
				<?php  if(strexists($row['award'], '兑换码')) { ?>
				<?php  $row['award'] = explode('兑换码', $row['award'])?>
				<?php  $row['award'] = $row['award']['0']?>
				<?php  } ?>
				<tr>
					<td><?php  echo $row['realname'];?></td>
					<td><?php  echo $row['award'];?></td>
				</tr>
				<?php  } } ?>
			</tbody>
		</table>
	</div>
</div>
<div data-role="popup" id="my_prize" data-overlay-theme="a" data-theme="c" data-dismissible="false" class="ui-corner-all">
	<div data-role="header" data-theme="c">
		<a href="#" data-rel="back" data-icon="delete" data-iconpos="notext">关闭</a>
		<h1>我的奖品</h1>
	</div>
	<div style="height:150px; overflow-y:scroll;">
		<table class="list_table">
			<thead>
				<tr>
					<th data-priority="persist" style="width:300px;">奖品</th>
					<th data-priority="persist" style="width:400px;">描述</th>
					<th data-priority="persist" style="width:300px;">状态</th>
				</tr>
			</thead>
			<tbody>
				<?php  if(is_array($myaward)) { foreach($myaward as $index => $row) { ?>
				<tr>
					<td><?php  echo $row['award'];?></td>
					<td><?php  echo $row['description'];?></td>
					<td><?php  if($row['status'] == 0){echo '未领取';}else{echo '已领取';}?></td>
				</tr>
				<?php  } } ?>
			</tbody>
		</table>
	</div>
</div>
<div class="main">
	<?php  if(($egg['maxlottery'] && $total >= $egg['maxlottery']) || empty($egg['maxlottery'])) { ?>
	<div class="ui-bar ui-bar-e" style="margin:10px 0;">
		<h3>您已经超过当日砸蛋次数！</h3>
	</div>
	<?php  } else { ?>
	<div class="egg">
		<ul class="eggList">
			<p class="hammer" id="hammer">锤子</p>
			<p class="resultTip" id="resultTip"><b id="result"></b></p>
			<li><span>1</span><sup></sup></li>
			<li><span>2</span><sup></sup></li>
			<li><span>3</span><sup></sup></li>
		</ul>
	</div>
	<?php  } ?>
	<div data-role="collapsible-set">
		<div data-role="collapsible" data-collapsed="true">
			<h3>游戏规则</h3>
			<?php  echo $eggrule;?>
		</div>
	</div>
	<div style="margin:30px 0;">
		<ul data-role="listview" data-inset="true" class="ui-icon-alt">
			<li><a href="#list_winner" data-rel="popup" data-transition="pop">中奖名单</a></li>
			<li><?php  if(!empty($member['mobilenumber']) && !empty($member['realname'])) { ?><a href="#my_prize" data-rel="popup" data-position-to="window" data-transition="pop">我的奖品</a><?php  } else { ?><a href="<?php  echo $this->createMobileUrl('register', array('id' => $id))?>">登记后领奖</a><?php  } ?></li>
		</ul>
	</div>
</div>

<?php  if($egg['maxlottery'] && $total < $egg['maxlottery']) { ?>
<script type="text/javascript">
jQuery(function($){
	$(".eggList li").click(function() {
		var posL = $(this).position().left + $(this).width()-30;
		$("#hammer").show().css('left', posL);
		$(this).children("span").hide();
		eggClick($(this));
	});
});
function eggClick(obj) {
	var _this = obj;
	var myaward = '';
	var otheraward = '';
	if(_this.hasClass("curr")){
		alert("蛋都碎了，别砸了！");
		_this.unbind('click');
		return false;
	}
	$.getJSON("<?php  echo $this->createMobileUrl('getaward', array('id' => $id,'gweid' => $gweid))?>&r="+new Date().getTime(),function(res){
		$(".hammer").css({"top":_this.position().top-55,"left":_this.position().left+185});
		$(".hammer").animate({
			"top":_this.position().top-25,
			"left":_this.position().left+125
			},30,function(){
				_this.addClass("curr");
				_this.find("sup").show();
				$(".hammer").hide();
				$('.resultTip').css({display:'block',top:'100px',opacity:0}).animate({top: '0px',opacity:1},300,function(){
					if(res.message.status==0){
						$("#result").html(res.message.message);
					}else{
						$("#result").html(res.message.message);
					}
				});
			}
		);
		for(i=0; i<res.message.myaward.length; i++) {
		    if(res.message.myaward[i].status == 0){
				myaward += '<tr><td>'+res.message.myaward[i].award+'</td><td>'+res.message.myaward[i].description+'</td><td>未领取</td></tr>';
		    }else{
				myaward += '<tr><td>'+res.message.myaward[i].award+'</td><td>'+res.message.myaward[i].description+'</td><td>已领取</td></tr>';
			}
		}
		$("#my_prize tbody").html(myaward); 
	});
	
	$.getJSON("<?php  echo $this->createMobileUrl('getOtherAward', array('id' => $id,'gweid' => $gweid))?>&r="+new Date().getTime(),function(res){
		
		for(i=0; i<res.message.otheraward.length; i++) {
			//"中奖名单" also need update after getaward
			otheraward += '<tr><td>'+res.message.otheraward[i].realname+'</td><td>'+res.message.otheraward[i].award+'</td></tr>';
		}
		$("#list_winner tbody").html(otheraward); 
	});
}
</script>
<?php  } ?>
<?php  include $this -> template('footer');?>