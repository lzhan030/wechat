<?php defined('IN_IA') or exit('Access Denied');?><?php  $bootstrap_type = 3;?>
<?php  if(empty($_W['isajax'])) { ?>
<?php include $this->template('header');?>
<style>
	.show-more {padding-bottom:30px;}
   .td_a { height:7em; text-align: center; width:33%;}
   .td_a {vertical-align: middle;background-image: url(http://2.wpcloudforsina.sinaapp.com/wp-content/themes/ReeooV3/wechat/weshopping/template/mobile/template1/images/icon_background.png); background-repeat: no-repeat;background-position: center;background-size: 6.2em 6.2em;}
   .td_a a img{width: 3.5em;height: 3.5em;margin-top: 0.5em;}
   .td_a a p{color: rgb(247,118,46);}
</style>
<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/wechat/weshopping/images/style.css">
<div class="head">
	<a href="javascript:;" onclick="$('.head .order').toggleClass('hide');" class="bn pull-left"><i class="fa fa-reorder"></i></a>
	<span class="title">

		<?php  if($_GPC['pcate']) { ?><?php  echo $category[$_GPC['pcate']]['name'];?><?php  } ?>
		<?php  if($_GPC['ccate']) { ?><?php  echo $category[$_GPC['ccate']]['name'];?><?php  } ?>
		<?php  if($_GPC['keyword']) { ?>搜索结果<?php  } ?>
	</span>
	<a href="<?php  echo $this->createMobileUrl('mycart')?>" class="bn pull-right">
		<i class="fa fa-shopping-cart"></i>
		<span class="buy-num img-circle" id="carttotal"><?php  echo $carttotal;?></span>
	</a>
	<ul class="unstyled order hide">
		<?php  if(is_array($category)) { foreach($category as $item) { ?>
		<li>
			<a href="<?php  echo $this->createMobileUrl('list2', array('pcate' => $item['id']))?>" class="bigtype">
				<i class="fa fa-folder-open-alt"></i> <?php  echo $item['name'];?>
			</a>
			<?php  if(is_array($children[$item['id']])) { foreach($children[$item['id']] as $child) { ?>
			<a href="<?php  echo $this->createMobileUrl('list2', array('ccate' => $child['id']))?>" class="smtype">
				<i class="fa fa-folder-open-alt"></i> <?php  echo $child['name'];?>
			</a>
			<?php  } } ?>
		</li>
		<?php  } } ?>
	</ul>
</div>
<style type='text/css'>
	.sel { background:#e9342a; color:#fff;}
	.nosel { background:#fff;color:#000}
</style>
<div class="shopping-main">
	<form action="" method="get">
		<input type="hidden" name="module" value="weshopping" />
		<input type="hidden" name="gweid" value="<?php echo $_GET['gweid']?>" />
		<input type="hidden" name="do" value="list2" />
		 
	   		<?php  if($_GPC['isnew']==1) { ?><input type="hidden" name="isnew" value="1" /><?php  } ?>
			<?php  if($_GPC['ishot']==1) { ?><input type="hidden" name="ishot" value="1" /><?php  } ?>
			<?php  if($_GPC['isdiscount']==1) { ?><input type="hidden" name="isdiscount" value="1" /><?php  } ?>
			<?php  if($_GPC['istime']==1) { ?>
			<input type="hidden" name="istime" value="1" />
			<?php  } ?>
			<input type="hidden" name="sort" value="<?php  echo $sort;?>" />
		<div class="input-group">
			<input type="text" class="form-control input-lg" name="keyword" value="<?php  echo $_GPC['keyword'];?>"  placeholder="商品关键词 / 编号">
			<span class="input-group-btn">
				<button class="btn btn-danger btn-lg" type="submit">搜索</button>
			</span>
		</div>
	</form>
	<div class="list" id="list" style='margin-top: 0px;'>
		<div style='float:left;height:30px;margin:auto;width:100%;margin-top:10px;'>
   		<div <?php  if($sort==0) { ?>class='sel'<?php  } else { ?>class="nosel"<?php  } ?> style='border-top-left-radius: 5px;border-bottom-left-radius:5px;border:1px solid #e9342a;text-align: center;float:left;width:33.3%' onclick="location.href='<?php  echo $sorturl;?>&sort=0&sortb0=<?php  echo $sortb00;?>'">
		按时间 <?php  if($sort==0) { ?><?php  if($sortb0=="desc") { ?><i class="fa fa-arrow-down"></i><?php  } else { ?><i class="fa fa-arrow-up"></i><?php  } ?><?php  } ?>
		</div>
		<div <?php  if($sort==1) { ?>class='sel'<?php  } else { ?>class="nosel"<?php  } ?> style='border:1px solid #e9342a;margin-left:-1px;float:left;width:33.3%;text-align: center;' onclick="location.href='<?php  echo $sorturl;?>&sort=1&sortb1=<?php  echo $sortb11;?>'">
		按销量 <?php  if($sort==1) { ?><?php  if($sortb1=="desc") { ?><i class="fa fa-arrow-down"></i><?php  } else { ?><i class="fa fa-arrow-up"></i><?php  } ?><?php  } ?>
		</div>
		<div <?php  if($sort==3) { ?>class='sel'<?php  } else { ?>class="nosel"<?php  } ?> style='border-top-right-radius: 5px;margin-left:-1px;border-bottom-right-radius:5px;text-align: center;border:1px solid #e9342a;float:left;width:33.3%' onclick="location.href='<?php  echo $sorturl;?>&sort=3&sortb3=<?php  echo $sortb33;?>'">
		按价格 <?php  if($sort==3) { ?><?php  if($sortb3=="desc") { ?><i class="fa fa-arrow-down"></i><?php  } else { ?><i class="fa fa-arrow-up"></i><?php  } ?><?php  } ?>
		</div>
</div>
<div class="list-tips">
	<?php  if($_GPC['isnew']==1) { ?>新品推荐<?php  } ?>
	<?php  if($_GPC['ishot']==1) { ?>热卖商品<?php  } ?>
	<?php  if($_GPC['isdiscount']==1) { ?>折扣商品<?php  } ?>
	<?php  if($_GPC['istime']==1) { ?>限时卖<?php  } ?>  共<b><?php  echo $total;?></b>种
</div>
<?php  } ?>
<?php  if(is_array($list)) { foreach($list as $item) { ?>
<div class="list-item img-rounded">
	<div>
		<a href="<?php  echo $this->createMobileUrl('detail', array('id' => $item['id']))?>"><img src="<?php  echo $_W['attachurl'];?><?php  echo $item['thumb'];?>" style="width: 100%;"></a>
		<span class="title"><a href="<?php  echo $this->createMobileUrl('detail', array('id' => $item['id']))?>"><?php  echo $item['title'];?></a><?php  if($item['type'] == '2') { ?>(虚拟)<?php  } ?></span>
		<?php  if($item['istime']==1) { ?>
			<span style='text-align: center;margin-left:10px;margin-right:10px;color:white;font-size:11px;' class='label label-danger' id="time_<?php  echo $item['id'];?>">
			<?php  if($item['timelast'] < 0) { ?>
				时间到了
			<?php  } else { ?>
				<?php  echo $item['timelaststr'];?>
				<script language='javascript'>
					var total_time_<?php  echo $item['id'];?> = <?php  echo $item['timelast'];?>;  
					var int_time_<?php  echo $item['id'];?>  = setInterval(function(){
						d(<?php  echo $item['id'];?>);
					},1000);
				</script>
			<?php  } ?>
			</span>
		 <?php  } ?>
	</div>
	<span class="sold">
		<span class="soldnum pull-left">已售<?php  echo $item['sales'];?>件</span>
		<?php if($item['ismanual']=='1'){ ?>
		<span class="price pull-right">自定义金额</span>
		<?php }else{ ?>
		<span class="price pull-right"><?php  echo $item['market_price'];?>元</span>
		<?php } ?>
	</span>
<!--	<div class="add-cart" onclick="order.add(<?php  echo $item['id'];?>)"><i class="fa fa-shopping-cart"></i> 添加到购物车</div>-->
</div>
<?php  } } ?>
<?php  if(empty($_W['isajax'])) { ?>
	</div>
	<div class="show-more"><a href="javascript:;" onclick="loadPage('<?php  echo $pindex;?>', 'list')" class="img-rounded" id="pager">浏览更多商品</a></div>
</div>
<script type="text/javascript">
function loadPage(pindex, container) {
	pindex = parseInt(pindex) + 1;
	$('#pager').html('正在加载数据...');
	$.get(location.href+'<?php if(!empty($_POST['sou'])) echo "&keyword=".urlencode($_POST['sou']); ?>', {'page' : pindex}, function(html){
		if (html.indexOf('list-item') > -1) {
			$('#'+container).append(html);
			$('#pager').get(0).onclick = function(){
				loadPage(pindex, container);
			}
			$('#pager').html("浏览更多商品");
		} else {
			$('#pager').html('已经显示全部商品');
		}
	});
}

function d(id){
	eval("total_time_" + id+"--");
	var total_time = eval("total_time_" + id);
	var days = parseInt(total_time/86400)
	var remain = parseInt(total_time%86400);
	var hours = parseInt(remain/3600)
	var remain = parseInt(remain%3600);
	var mins = parseInt(remain/60);
	var secs = parseInt(remain%60);
	if (total_time <= 0) {
		$("#time_" + id).html( "时间到了");
		var int_time =  eval("int_time_" + id);
		window.clearInterval(int_time);
	} else {
		var ret = "";
		if(days>0){
			days = days+"";
			if(days.length<=1) { days="0"+days;}
			ret+=days+" 天 ";
		}
		if(hours>0){
			hours = hours+"";
			if(hours.length<=1) { hours="0"+hours;}
			ret+=hours+":";
		}
		if(mins>0){
			mins = mins+"";
			if(mins.length<=1) { mins="0"+mins;}
			ret+=mins+":";
		}
		secs = secs+"";
		if(secs.length<=1) { secs="0"+secs;}
		ret+=secs;
		$("#time_" + id).html( "倒计时 " +ret);
	}
}
</script>

<?php include $this->template('footer');?>
<?php include $this->template('footerbar');?>
<?php  } ?>