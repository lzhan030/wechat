<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
<style>
    .fixtoTop{position:absolute; left:0; bottom:0}
	.resultnotes{display:none; position:absolute; z-index:1001; width:60%;height:100px;	filter: alpha(opacity=80); opacity: 0.6; overflow: hidden;background-color:#000;left: 50%;margin-left: -30%;margin-top:30px;border-radius: 5px; -moz-border-radius: 5px;-webkit-border-radius: 5px;}
	.btn{width:100%;height:45px;background-image:url(<?php echo home_url().'/wp-content/themes/ReeooV3/wechat/redenvelope/template/images/clickhongbao.png'; ?>);background-repeat: no-repeat;background-position: center;background-color:#FFE219; border: 1px solid #ec971f;}
	.btn:hover, btn:active{background-color:#E5CB16;border:2px solid #FF0404;}
</style>
<!--<div data-role="page" data-theme="a" style="position:relative;">-->
<div data-role="page" class="lotterypage" style="">
    <div class="resultnotes" style="">
	</div>
<?php if(!empty($scrpicture)){?>
	<img src="<?php echo $scrpicture; ?>" width="100%" min-height="30">
<?php } ?>
<div data-role="popup" id="my_rule" data-overlay-theme="a" data-theme="c" data-dismissible="false" class="ui-corner-all" style="left:0px;">
	<div data-role="header" data-theme="c">
		<a href="#" data-rel="back" data-icon="delete" data-iconpos="notext">关闭</a>
		<h1>活动规则</h1>
	</div>
	<div style="height:150px; overflow-y:scroll;">
		<div style="margin-left: 10px; min-width:300px; margin-top: 10px; margin-right: 10px;text-indent: 1em;">  
			<?php echo $redenveloperule;?>
		</div>
	</div>
</div>
<div data-role="popup" id="list_winner" data-overlay-theme="a" data-theme="c" data-dismissible="false" class="ui-corner-all">
	<div data-role="header" data-theme="c">
		<a href="#" data-rel="back" data-icon="delete" data-iconpos="notext">关闭</a>
		<h1>中奖名单</h1>
	</div>
	<div style="height:300px; overflow-y:scroll; ">
		<table class="list_table">
			<thead>
				<tr>
					<th data-priority="persist" style="width:200px;">姓名</th>
					<th data-priority="persist" style="width:500px;">奖品</th>
				</tr>
			</thead>
			<tbody>
				<?php  if(is_array($otheraward)) { foreach($otheraward as $index => $row) {
						//if(!empty($row['code'])){ 兑换码有可能为0
						if($row['code'] != ""){
							echo '<tr><td>'.$row['realname'].'</td>';
							echo '<td>中奖金额:'.$row['amount'].'￥</td></tr>';
							if($row['credit'] != 0){
								echo '<tr><td>'.$row['realname'].'</td>';
								echo '<td>中奖励积分:'.$row['credit'].'分</td></tr>';
							}	
						}else{
						    if($row['credit'] != 0){
								echo '<tr><td>'.$row['realname'].'</td>';
								echo '<td>未中奖励积分:'.$row['credit'].'分</td></tr>';
							}
						}
					} 
				}
				?>
			</tbody>
		</table>
	</div>
</div>
<div data-role="popup" id="my_prize" data-overlay-theme="a" data-theme="c" data-dismissible="false" class="ui-corner-all">
	<div data-role="header" data-theme="c">
		<a href="#" data-rel="back" data-icon="delete" data-iconpos="notext">关闭</a>
		<h1>我的奖品</h1>
	</div>
	<div style="height:300px; overflow-y:scroll; overflow-x:scroll;">
		<table class="list_table">
			<thead>
				<tr>
					<th data-priority="persist" style="width:250px;">奖品</th>
					<th data-priority="persist" style="width:150px;">描述</th>
					<th data-priority="persist" style="width:250px;">状态</th>
					<th data-priority="persist" style="width:350px;">截止日期</th>
				</tr>
			</thead>
			<tbody>
				<?php  if(is_array($myaward)) { foreach($myaward as $index => $row) {
                            if($row['winexpire'] == 0){
								$expiredate = "无期限";
							}else{									
								$expiredate = date('Y-m-d', $row['winexpire']);	
								
							}				
							//if(!empty($row['code'])){   兑换码有可能为0
							if($row['code'] != ""){
							    if(($row['winexpire'] != 0 && strtotime(date('Y-m-d', $row['winexpire'])." 23:59:59") < time()) && ($row['status'] == 0)){    //如果兑换码过期则不显示
									echo '<tr><td>兑换码:已过期</td>';
								}else{
									echo '<tr><td>兑换码:'.$row['code'].'</td>';
								}
								//echo '<td>'.$row['amount'].'￥</td>';
								if($row['status'] == 0){
								    if($row['winexpire'] != 0 && strtotime(date('Y-m-d', $row['winexpire'])." 23:59:59") < time() && ($row['status'] == 0)){	//过期了就不再提示兑换码说明
										echo '<td>'.$row['amount'].'￥<br></td>';
									}else{
										echo '<td>'.$row['amount'].'￥<br>'.$row['des'].'</td>';
									}
									echo '<td>未领取</td><td>'.$expiredate.'</td></tr>';
								}else{
									echo '<td>'.$row['amount'].'￥</td>';
									echo '<td>已领取</td><td>'.$expiredate.'</td></tr>';
								}
								if($row['credit'] != 0){
									echo '<tr><td>中奖励积分</td>';
									echo '<td>'.$row['credit'].'分</td>';
									echo '<td>已领取</td><td></td></tr>';	
								}
							}
							//if(empty($row['code']) && $row['credit'] != 0){
							if($row['code'] == "" && $row['credit'] != 0){
								echo '<tr><td>未中奖励积分</td>';
								echo '<td>'.$row['credit'].'分</td>';
								echo '<td>已领取</td><td></td></tr>';
							} 
						} 
					} ?>
			</tbody>
		</table>
	</div>
</div>
<div class="main" style="padding-bottom:55px;">
	<div style="position:relative;text-align: center;">
	    <?php if(!empty($redenvelopepicture)){?>
			<img src="<?php echo $redenvelopepicture; ?>" id="hongbao" style="width:100%;">
		<?php }?>	
	</div>
	<div class="renevlope" style="text-align: center; position: relative; display: block; z-index: 1000; margin-top:19%;">
		<img src="<?php echo home_url().'/wp-content/themes/ReeooV3/wechat/redenvelope/template/images/hongbao.png'; ?>" id="hongbaored" style="width:100%;">
	</div>
	<div style="text-align:center; position:relative;">
		<input onclick="getRedenvelope()" id="hongbao" class="btn" type="button" data-role="none" />
	</div>
	<?php if(!empty($redenveloperule)) {?>
		<div style="background-color: rgba(188, 238, 88, 0.3);margin:12% 0;padding:5px 5px;font-family:'微软雅黑';min-height:100px;">
			<div style="color:#222;text-align:center;">
				<div style="font-size:16px;font-weight:bold;">活动规则</div>
				<div style="width:100%;"><hr style="margin:2px 0 5px 0;"></div>
			</div>
			<div style="text-align:left;color:#222;padding:5px;"><?php echo $redenveloperule;?></div>
		</div>
	<?php }?>
	<div class="result" style="margin-top: 20px;">
	</div>
</div>
</div>
<script>
jQuery(function($){
    //加背景图片

	 <?php if(!empty($redenvelopebacpicture)){?>
		//$(".ui-body-a, .ui-overlay-a").css("background-image","url(<?php echo $redenvelopebacpicture;?>)");
	<?php }?>
	/* $(".ui-body-a, .ui-overlay-a").css("padding","0px");
	$(".ui-body-a, .ui-overlay-a").css("margin","0px");
	$(".ui-body-a, .ui-overlay-a").css("background-size","cover");
	$(".ui-body-a, .ui-overlay-a").css("background-color","#F9F9F9"); */
	
	 <?php if(!empty($redenvelopebacpicture)){?>
		$(".lotterypage").css("background-image","url(<?php echo $redenvelopebacpicture;?>)");
	<?php }?>
	$(".lotterypage").css("padding","0px");
	$(".lotterypage").css("margin","0px");
	$(".lotterypage").css("background-size","cover");
	$(".lotterypage").css("background-color","#F9F9F9");
	
});
</script>
<?php  //if($redenvelope['maxlottery'] && $total < $redenvelope['maxlottery']) { ?>
<?php  if($redenvelope['maxlottery'] && $total < $redenvelope['maxlottery'] && (strtotime($redenvelope['startdate']) < time())) { ?>
<script type="text/javascript">
function getRedenvelope() {
   
	var myaward = '';
	var otheraward = '';
	$.getJSON("<?php  echo $this->createMobileUrl('getaward', array('id' => $id,'gweid' => $gweid))?>&r="+new Date().getTime(),function(res){
		if(res.message.status==0){
			$('.resultnotes').css("display","block");
			$('.resultnotes').empty();
			$('.resultnotes').css("height","80px");
			//$('.resultnotes').append('<div style="text-align:center;margin-top: 5px;font-size:16px;color:white;text-indent: 1em;">'+res.message.message+res.message.codedes+'</div>'); remove code description
			$('.resultnotes').append('<div style="text-align:center;margin-top: 5px;font-size:16px;color:white;text-indent: 1em;">'+res.message.message+'</div>'); 
			setTimeout("$('.resultnotes').css('display','none')",6500);  //延时消失
		}else{
			var des = res.message.message;
			if(des.indexOf("兑换码库存不足") >=0){	
				$('.resultnotes').css("display","block");
				$('.resultnotes').empty();
				$('.resultnotes').css("height","45px");
				$('.resultnotes').append('<div style="text-align:center;margin-top: 10px;font-size:16px;color:white;">红包已被抢光了~</div>'); 		
			}else if(des.indexOf("您还未到达可以再次抽奖的时间") >=0){	
				$('.resultnotes').css("display","block");
				$('.resultnotes').empty();
				$('.resultnotes').css("height","100px");
				$('.resultnotes').append('<div style="text-align:center;margin-top: 5px;font-size:16px;color:white;text-indent: 1em;">'+res.message.message+'</div>'); 		
			}else{
				$('.resultnotes').css("display","block");
				$('.resultnotes').empty();
				$('.resultnotes').css("height","45px");
				$('.resultnotes').append('<div style="text-align:center;margin-top: 10px;font-size:16px;color:white;text-indent: 1em;">'+res.message.message+'</div>'); 		
			}
			setTimeout("$('.resultnotes').css('display','none')",5000);  //延时消失
		}
		var currenttime = get_unix_time(getNowFormatDate()); ////获取当前时间戳
		//alert(parseInt(1425440271)>parseInt(1425440272));
		for(i=0; i<res.message.myaward.length; i++) {
		    //alert(res.message.myaward[i].winexpire);
			
			if(res.message.myaward[i].winexpire == 0){
				var expiredate = "无期限";
			}else{
				var expiredate = res.message.myaward[i].winexpire;
			}
			//alert(parseInt(get_unix_time(getNowFormatDate())));
			//alert(parseInt(expiretime));
			//alert(res.message.myaward[i].codeexpire);
			if(res.message.myaward[i].code != "" && res.message.myaward[i].status == 0){
				if(res.message.myaward[i].codeexpire == "")
				{
					myaward += '<tr><td>兑换码:'+res.message.myaward[i].code+'</td><td>'+res.message.myaward[i].amount+'￥<br>'+res.message.myaward[i].des+'</td><td>未领取</td><td>'+expiredate+'</td></tr>';
				}else{
					myaward += '<tr><td>兑换码:已过期'+'</td><td>'+res.message.myaward[i].amount+'￥'+'</td><td>未领取</td><td>'+expiredate+'</td></tr>';
				}
				if(res.message.myaward[i].credit != 0){
						myaward += '<tr><td>中奖励积分'+'</td><td>'+res.message.myaward[i].credit+'分</td><td>已领取</td><td></td></tr>';
				}
			}
			if(res.message.myaward[i].code != "" && res.message.myaward[i].status == 1){
				myaward += '<tr><td>兑换码:'+res.message.myaward[i].code+'</td><td>'+res.message.myaward[i].amount+'￥</td><td>已领取</td><td>'+expiredate+'</td></tr>';
			}
			if(res.message.myaward[i].code == "" && res.message.myaward[i].credit != 0){
				myaward += '<tr><td>未中奖励积分'+'</td><td>'+res.message.myaward[i].credit+'分</td><td>已领取</td><td></td></tr>';
			}
		}
		$("#my_prize tbody").html(myaward); 
		//"中奖名单" also need update after getaward
		$.getJSON("<?php  echo $this->createMobileUrl('getOtherAward', array('id' => $id,'gweid' => $gweid))?>&r="+new Date().getTime(),function(res){
			for(i=0; i<res.message.otheraward.length; i++) {
				//alert(res.message.otheraward[i].code);
				if(res.message.otheraward[i].realname != null){
					if(res.message.otheraward[i].code != ""){
						otheraward += '<tr><td>'+res.message.otheraward[i].realname+'</td><td>中奖金额:'+res.message.otheraward[i].amount+'￥</td></tr>';
						if(res.message.otheraward[i].credit != 0){
								otheraward += '<tr><td>'+res.message.otheraward[i].realname+'</td><td>中奖励积分:'+res.message.otheraward[i].credit+'分</td></tr>';
						}
					}
					if(res.message.otheraward[i].code == ""){
						if(res.message.otheraward[i].credit != 0){
							otheraward += '<tr><td>'+res.message.otheraward[i].realname+'</td><td>未中奖励积分:'+res.message.otheraward[i].credit+'分</td></tr>';
						}
					}
				}
			}
			$("#list_winner tbody").html(otheraward); 
		});
	});
}

function getNowFormatDate() {
    var date = new Date();
    var seperator1 = "-";
    var seperator2 = ":";
    var year = date.getFullYear();
    var month = date.getMonth() + 1;
    var strDate = date.getDate();
    if (month >= 1 && month <= 9) {
        month = "0" + month;
    }
    if (strDate >= 0 && strDate <= 9) {
        strDate = "0" + strDate;
    }
    var currentdate = year + seperator1 + month + seperator1 + strDate
            + " " + date.getHours() + seperator2 + date.getMinutes()
            + seperator2 + date.getSeconds();
    return currentdate;
}
function get_unix_time(dateStr)
{
    var newstr = dateStr.replace(/-/g,'/'); 
    var date =  new Date(newstr); 
    var time_str = date.getTime().toString();
    return time_str.substr(0, 10);
}
</script>
<?php  } ?>
<?php if((($redenvelope['maxlottery'] && $total >= $redenvelope['maxlottery']) || empty($redenvelope['maxlottery'])) && (strtotime($redenvelope['startdate']) <  time())) {?>
	<script>
		$('.resultnotes').css("display","block");
		$('.resultnotes').empty();
		$('.resultnotes').css("height","90px");
		$('.resultnotes').append('<div style="text-align:center;margin-top: 10px;font-size:16px;color:white;">您已经超过当日抢红包次数！</div>'); 		
	</script>	
<?php }?>
<?php if(strtotime($redenvelope['startdate']) >=  time()){?>
	<script>
		$('.resultnotes').css("display","block");
		$('.resultnotes').empty();
		$('.resultnotes').css("height","60px");
		$('.resultnotes').append('<div style="text-align:center;margin-top: 10px;font-size:16px;color:white;">活动还未开始，开始时间：<?php echo $redenvelope['startdate'];?>，敬请期待!</div>'); 		
	</script>	
<?php }?>
<?php  include $this -> template('footerbar');?>
<?php  include $this -> template('footer');?>