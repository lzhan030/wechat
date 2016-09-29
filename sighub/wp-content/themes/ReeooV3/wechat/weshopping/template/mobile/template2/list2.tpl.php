<?php defined('IN_IA') or exit('Access Denied');?><?php  $bootstrap_type = 3;?>
<?php  if(empty($_W['isajax'])) { ?>
<?php include $this -> template('header');?>
<style>
	.show-more {padding-bottom:30px;}
</style>
<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/wechat/weshopping/css/style.css">
<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory'); ?>/wechat/weshopping/css/base.css?v=<?php echo time();?>">
<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory'); ?>/wechat/weshopping/css/page.css?v=<?php echo time();?>">
<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory'); ?>/wechat/weshopping/css/reset.css">
<div id="top">
	<div class="back-ui-a">
	<a href="javascript:history.back(1)">返回</a>
	</div>
	<div class="header-title">商品列表</div>
	<div class="site-nav">
		<ul class="fix">
			<li class="home"><a href="<?php echo $this->createMobileUrl('list', array('gweid' => $gweid))?>">首页</a></li>
			<li class="mysn"><a href="<?php echo $this->createMobileUrl('contactUs', array('gweid' => $gweid))?>">小店介绍</a></li>
			<li class="mycart"><a href="<?php echo $this->createMobileUrl('mycart', array('gweid' => $gweid))?>">购物车</a></li>
			<li class="allcate"><a href="<?php  echo $this->createMobileUrl('allcategories', array('gweid' => $gweid))?>">商品分类</a></li>
		</ul>
	</div>
</div>
<style type='text/css'>
	.sel { background:#e9342a; color:#fff;}
	.nosel { background:#fff;color:#000}
</style>
<div class="shopping-main">
	<input type="hidden" name="i" value="<?php  echo $_W['weid'];?>" />
	<input type="hidden" name="c" value="entry" />
	<input type="hidden" name="m" value="ewei_shopping" />
	<input type="hidden" name="do" value="list2" />
		<?php  if($_GPC['isnew']==1) { ?><input type="hidden" name="isnew" value="1" /><?php  } ?>
		<?php  if($_GPC['ishot']==1) { ?><input type="hidden" name="ishot" value="1" /><?php  } ?>
		<?php  if($_GPC['isdiscount']==1) { ?><input type="hidden" name="isdiscount" value="1" /><?php  } ?>
		<?php  if($_GPC['istime']==1) { ?>
		<input type="hidden" name="istime" value="1" />
		<?php  } ?>
		<input type="hidden" name="sort" value="<?php  echo $sort;?>" />
	<div class="layout mt20">
		<div class="search-box">
			<form action="" method="post" onSubmit="return validateform()">
				<input type="search" name="sou" id="sou" class="search-input" autocomplete="off" value="<?php  echo $soucontent;?>"  placeholder="商品关键词 / 编号" onfocus="this.value=''">
				<input type="submit" class="search-submit">
			</form>
		</div>
	</div>
	<div id="list" style='margin-top: 0px;'>
	<div class="type-filter list-ui-div" id="Filter_Order">
		<ul>
			<li>
				<a href="javascript:;" <?php  if($sort==0) { ?>class='cur'<?php  } else { ?>class="nosel"<?php  } ?> onclick="location.href='<?php  echo $sorturl;?>&sort=0&sortb0=<?php  echo $sortb00;?><?php if(!empty($_POST['sou'])) echo "&keyword=".urlencode($_POST['sou']); ?>'">
				按时间 <?php  if($sort==0) { ?><?php  if($sortb0=="desc") { ?><i class="fa fa-arrow-down"></i><?php  } else { ?><i class="fa fa-arrow-up"></i><?php  } ?><?php  } ?>
				</a>
			</li>
			<li>
				<a href="javascript:;" <?php  if($sort==1) { ?>class='cur'<?php  } else { ?>class="nosel"<?php  } ?> onclick="location.href='<?php  echo $sorturl;?>&sort=1&sortb1=<?php  echo $sortb11;?><?php if(!empty($_POST['sou'])) echo "&keyword=".urlencode($_POST['sou']); ?>'">
				按销量 <?php  if($sort==1) { ?><?php  if($sortb1=="desc") { ?><i class="fa fa-arrow-down"></i><?php  } else { ?><i class="fa fa-arrow-up"></i><?php  } ?><?php  } ?>
				</a>
			</li>
			<li>
				<a href="javascript:;" <?php  if($sort==3) { ?>class='cur'<?php  } else { ?>class="nosel"<?php  } ?> onclick="location.href='<?php  echo $sorturl;?>&sort=3&sortb3=<?php  echo $sortb33;?><?php if(!empty($_POST['sou'])) echo "&keyword=".urlencode($_POST['sou']); ?>'">
				按价格 <?php  if($sort==3) { ?><?php  if($sortb3=="desc") { ?><i class="fa fa-arrow-down"></i><?php  } else { ?><i class="fa fa-arrow-up"></i><?php  } ?><?php  } ?>
				</a>
			</li>
		</ul>
	</div>
		
	<!--<div>-->		
		<div class="list-tips">
			<?php  if($_GPC['isnew']==1) { ?>新品推荐<?php  } ?>
			<?php  if($_GPC['ishot']==1) { ?>热卖商品<?php  } ?>
			<?php  if($_GPC['isdiscount']==1) { ?>折扣商品<?php  } ?>
			<?php  if($_GPC['istime']==1) { ?>限时卖<?php  } ?>  共<b><?php  echo $total;?></b>种
		</div>
		<?php  } ?>
		<?php 
			if(is_array($list)) { 
		    $upload =wp_upload_dir();
		    foreach($list as $item) { ?>
		<div class="list_aera">
			<div class="box">
				<a href="<?php  echo $this->createMobileUrl('detail', array('id' => $item['id'], 'gweid' => $gweid))?>">
					<img src="<?php if(empty($item['thumb'])|| (stristr($item['thumb'],"http")!==false)){ echo $item['thumb'];}else{echo $upload['baseurl'].$item['thumb'];} ?>">
				</a>
			</div>
			<h1><a href="<?php  echo $this->createMobileUrl('detail', array('id' => $item['id'], 'gweid' => $gweid))?>"><?php  echo $item['title'];?></a><?php  if($item['type'] == '2') { ?>(虚拟)<?php  } ?></h1>
			<!--<h2><?php  echo $item['content'];?></h2>-->
			<!--<br>-->
			<p>	
				<?php if($item['ismanual']=='1'){ ?>
				<span class="big_b" style="color: #C00;font-size: 14px;">自定义金额</span>
				<?php }else{ ?>
				<span class="big_b" style="font-size: 14px;">价格:<span style="color: #C00;">￥<?php  echo $item['market_price'];?></span></span>
				<?php } ?>
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
			</p>
			<p>
			    <span style="padding: 5px;font-size: 14px;">已售<?php echo $item['sales'];?>件</span>
			</p>
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
		//if (html.indexOf('list-item') > -1) {
		if (html.indexOf('list_aera') > -1) {
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
function validateform()
{
	if (document.getElementById("sou").value == "") {
		alert("请输入查询内容");
		return false;
	}
	return true; 
}

</script>

<?php include $this -> template('footer');?>
<?php  } ?>