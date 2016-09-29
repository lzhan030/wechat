<?php defined('IN_IA') or exit('Access Denied');?><?php  $bootstrap_type = 3;?>
<?php  if(empty($_W['isajax'])) { ?>
<?php include $this -> template('header');?>
<?php include $this -> template('common');?>
<script language="javascript" src="<?php bloginfo('template_directory'); ?>/wechat/weshopping/js/touchslider.min.js"></script>
<script language="javascript" src="<?php bloginfo('template_directory'); ?>/wechat/weshopping/js/swipe.js"></script>
<style>
   .td_a { height:7em; text-align: center}
   .ad-h2{line-height:40px;height:40px;font-size:16px;color:#FFF;border-top:1px solid #e5e5e5;background:#4C923A;padding-left: 10px;}
</style>
<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory'); ?>/wechat/weshopping/css/base.css?v=<?php echo time();?>">
<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory'); ?>/wechat/weshopping/css/base2013.css"> <!--use css from another template-->
<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory'); ?>/wechat/weshopping/css/index.css">
<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/wechat/weshopping/images/style.css">

<div id="top" style="margin-top: -45px;">
    <div class="header-title1">商城首页</div>
	<div class="site-nav">
		<ul class="fix">
			<li class="home"><a href="<?php echo $this->createMobileUrl('list', array('gweid' => $gweid))?>">首页</a></li>
			<li class="mysn"><a href="<?php echo $this->createMobileUrl('contactUs', array('gweid' => $gweid))?>">小店介绍</a></li>
			<li class="mycart"><a href="<?php echo $this->createMobileUrl('mycart', array('gweid' => $gweid))?>">购物车</a></li>
			<li class="allcate"><a href="<?php  echo $this->createMobileUrl('allcategories', array('gweid' => $gweid))?>">商品分类</a></li>
		</ul>
	</div>
</div>

<div class="layout mt20">
	<div class="search-box">
		<form action="" method="post" onSubmit="return validateform()">
			<input type="search" name="sou" id="sou" class="search-input" autocomplete="off" placeholder="商品关键词 / 编号" value="" onfocus="this.value=''">
			<input type="submit" class="search-submit" >
		</form>
	</div>
</div>
<div>
	<div class="sn-dock w mt20">
		<div class="sn-dock-inside">
			<ul class="fix">
				<li><a href="<?php  echo $this->createMobileUrl('allcategories', array('gweid' => $gweid))?>"><img src="<?php bloginfo('template_directory'); ?>/wechat/weshopping/images/1.png" alt=""><span>全部分类</span></a></li>
				<li><a href="<?php echo $this->createMobileUrl('contactUs', array('gweid' => $gweid))?>"><img src="<?php bloginfo('template_directory'); ?>/wechat/weshopping/images/2.png" alt=""><span>小店介绍</span></a></li>
				<li><a href="<?php  echo $this->createMobileUrl('myorder', array('gweid' => $gweid))?>"><img src="<?php bloginfo('template_directory'); ?>/wechat/weshopping/images/3.png" alt=""><span>我的订单</span></a></li>
				<li><a href="<?php echo $this->createMobileUrl('mycart', array('gweid' => $gweid))?>"><img src="<?php bloginfo('template_directory'); ?>/wechat/weshopping/images/4.png" alt=""><span>购物车</span></a></li>
			</ul>
		</div>
	</div>
	<?php  } ?>
	<?php if($rlistcount > 0){?>
	<div class="ad2 ad2-v1">
		<!--h2 class="ad-h2" style="font-size:16px;color:red;font-weight:bold;">推荐商品</h2-->
		<div class="ad-h2">推荐商品</div>
		<?php  if(is_array($rlist2)) {
			   if(!empty($rlist2)){include $this -> template('list_recommend');}
		} ?>
	</div>
    <?php }?>
	<?php  
	    if(is_array($recommandcategory)) { 
		$i = 1;
		$upload =wp_upload_dir();
		foreach($recommandcategory as $c) {
	?>
	<?php  if(!empty($c['list'])) { ?>
	<?php  if(empty($_W['isajax'])) { ?>
		<div class="floor floor<?php if($i <= 4){ echo $i;}elseif(($i - 4)%4 != 0){ echo ($i - 4)%4;}else{ echo 4;}?>" id="list_<?php  echo $c['parentid'];?>_<?php  echo $c['id'];?>">
	<?php  } ?>
	<?php  if(empty($_W['isajax'])) { ?>
		<div class="floor-title"><?php  echo $c['name'];?>
			<?php  if(empty($_W['isajax'])) { ?>
			    <?php if($c['parentid'] == 0){?>
					<!--<a href="<?php  echo $this->createMobileUrl('list2', array('ccate' => $c['parentid'],'pcate' => $c['id'], 'gweid' => $gweid))?>" class="more">更多</a>-->
					<a href="<?php  echo $this->createMobileUrl('list2', array('pcate' => $c['id'], 'gweid' => $gweid))?>" class="more">更多</a>
				<?php }else{?>
					<!--<a href="<?php  echo $this->createMobileUrl('list2', array('ccate' => $c['id'],'pcate' => $c['parentid'], 'gweid' => $gweid))?>" class="more">更多</a>-->
					<a href="<?php  echo $this->createMobileUrl('list2', array('ccate' => $c['id'], 'gweid' => $gweid))?>" class="more">更多</a>
				<?php }?>
			<?php  } ?>
		</div>
		<div class="floor-cate fix">
			<ul class="floor-cate-list fix">
				<?php  if(is_array($c['list'])) { foreach($c['list'] as $item) { ?>
					<li>
						<a href="<?php  echo $this->createMobileUrl('detail', array('id' => $item['id'], 'gweid' => $gweid))?>">
							<img src="<?php if(empty($item['thumb'])|| (stristr($item['thumb'],"http")!==false)){ echo $item['thumb'];}else{echo $upload['baseurl'].$item['thumb'];} ?>">
						</a>	
							<span><a style="margin-top: 5px;" href="<?php  echo $this->createMobileUrl('detail', array('id' => $item['id'], 'gweid' => $gweid))?>"><?php  echo $item['title'];?><?php  if($item['type'] == '2') { ?>(虚拟)<?php  } ?></a></span>
							<!--买家输入金额的商品不需要显示出金额-->
							<?php if($item['ismanual'] != 1){?>
							<span class="price" style="margin-top: 8px;">￥<?php  echo $item['market_price'];?></span>
							<?php }else{?>
							<span class="price" style="margin-top: 8px;">自定义金额</span>
							<?php }?>
					</li>
				<?php  } } ?>   
			</ul>
		</div>	
		<?php  } ?>	
	</div>
	<?php  $i = $i + 1;} ?>    
	<?php   } } ?>

	<?php  if(empty($_W['isajax'])) { ?>
</div>
<script type="text/javascript">
function loadPage(pindex, container, pcate, ccate) {
	pindex = parseInt(pindex) + 1;
	var pager = $('#pager_' + pcate + "_" + ccate);
	pager.html('正在加载数据...');
	var url = "<?php  echo $this->createMobileUrl('listmore')?>";
	$.get(url, {'page' : pindex, 'pcate':pcate, 'ccate':ccate}, function(html){
		if (html.indexOf('list-item') > - 1) {
			pager.html('浏览更多商品');
			$('#' + container).append(html);
			pager.get(0).onclick = function(){
				loadPage(pindex, container, pcate, ccate);
			}
		} else {
			pager.html('已经显示全部商品');
		}
	});
}

function loadRecPage(pindex, container) {
	pindex = parseInt(pindex) + 1;
	var pager = $('#pager_rec');
	pager.html('正在加载数据...');
	var url = "<?php  echo $this->createMobileUrl('listmore_rec')?>";
	$.get(url, {'page' : pindex}, function(html){
		if (html.indexOf('list-item') > - 1) {
			pager.html('浏览更多商品');
			$('#' + container).append(html);
			pager.get(0).onclick = function(){
				loadRecPage(pindex, container);
			}
		} else {
			pager.html('已经显示全部商品');
		}
	});
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