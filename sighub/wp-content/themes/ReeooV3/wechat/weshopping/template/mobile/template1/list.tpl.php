<?php defined('IN_IA') or exit('Access Denied');?><?php  $bootstrap_type = 3;?>
<?php  if(empty($_W['isajax'])) { ?>
<?php include $this->template('header');?>
<?php include $this->template('common');?>
<script language="javascript" src="<?php bloginfo('template_directory'); ?>/wechat/weshopping/images/touchslider.min.js"></script>
<script language="javascript" src="<?php bloginfo('template_directory'); ?>/wechat/weshopping/images/swipe.js"></script>
<style>
   .td_a { height:7em; text-align: center; width:33%;}
   .td_a {vertical-align: middle;background-image: url(http://2.wpcloudforsina.sinaapp.com/wp-content/themes/ReeooV3/wechat/weshopping/template/mobile/template1/images/icon_background.png); background-repeat: no-repeat;background-position: center;background-size: 6.2em 6.2em;}
   .td_a a img{width: 3.5em;height: 3.5em;margin-top: 0.5em;}
   .td_a a p{color: rgb(247,118,46);}
</style>
<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/wechat/weshopping/images/style.css">
<div class="head">
	<a href="javascript:;" onclick="$('.head .order').toggleClass('hide');" class="bn pull-left"><i class="fa fa-reorder"></i></a>
	<span class="title">商城首页<?php  if($_GPC['pcate']) { ?> - <?php  echo $category[$_GPC['pcate']]['name'];?><?php  } ?><?php  if($_GPC['ccate']) { ?> - <?php  echo $children[$_GPC['pcate']][$_GPC['ccate']]['name'];?><?php  } ?></span>
	<a href="<?php  echo $this->createMobileUrl('mycart')?>" class="bn pull-right"><i class="fa fa-shopping-cart"></i><span class="buy-num img-circle" id="carttotal"><?php  echo $carttotal;?></span></a>
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

<div id="banner_box" class="box_swipe" style='width:100%;float:left;'>
	<ul>
		<?php  if(is_array($advs)) { foreach($advs as $adv) { ?>
		<li>
			<a href="<?php  if(empty($adv['link'])) { ?>#<?php  } else { ?><?php  echo $adv['link'];?><?php  } ?>">
				<img src="<?php  echo $_W['attachurl'];?><?php  echo $adv['thumb'];?>" alt="" height='200px' style="width: 100%;"/>
			</a>
			<span class="title"><?php  echo $adv['advname'];?></span>
		</li>
		<?php  } } ?>
	</ul>
	<ol>
	   <?php  $slideNum = 1;?>
	<?php  if(is_array($advs)) { foreach($advs as $adv) { ?>
		<li<?php  if($slideNum == 1) { ?> class="on"<?php  } ?>></li>
		<?php  $slideNum++;?>
	<?php  } } ?>
	</ol>
</div>
<script>
	$(function() {
		new Swipe($('#banner_box')[0], {
			speed:500,
			auto:3000,
			callback: function(){
				var lis = $(this.element).next("ol").children();
				lis.removeClass("on").eq(this.index).addClass("on");
			}
		});
	});
</script>

<div class="shopping-main">
	<table style='width:100%;'>
		<tr>
			<td style="text-align: center;">
				<a href="<?php  echo $this->createMobileUrl('list2')?>">
					<img src="<?php bloginfo('template_directory'); ?>/wechat/weshopping/template/mobile/template1/images/icon_indexn_06.png" style='width:6.2em;height:6.2em'>
				</a>
			</td>
			<?php foreach($categoryindex as $category_key => $category_item){ 
				if(($category_key-2)%3 == 0)
					echo '<tr>';
			?>
			<td class='td_a'>
				<a href="<?php  echo $this->createMobileUrl('list2',array('ccate'=>0,'pcate'=>$category_item['id']))?>">
					<img src="<?php echo !empty($category_item['thumb'])?$_W['attachurl'].$category_item['thumb']:''; ?>">
					<p><?php echo $category_item['name'];?></p>
				</a>
			</td>
			<?php if(($category_key-1)%3 == 0 || $category_key == count($categoryindex)-1)
					echo '</tr>';  }?>
	</table>
	<?php  } ?>
 	  <?php  if(is_array($rlist) && !empty($rlist)) { ?>
<div class="list" id="list_rec">
	 <div class="list-tips" style='float:left;width:100%;font-size:14px;color:#e9342a;padding-left:10px;'>推荐商品</div>
	   <?php foreach($rlist as $item) { ?>
	  		<?php include $this->template('list_item');?>
	  <?php  } ?>
	</div>
 <div class="show-more"><a href="javascript:;" onclick="loadRecPage('<?php  echo $rpindex;?>', 'list_rec')" class="img-rounded pager" id="pager_rec">浏览更多商品</a></div>
	<?php } ?>
 	<?php  if(is_array($recommandcategory)) { foreach($recommandcategory as $c) { ?>
	<?php  if(!empty($c['list'])) { ?>
	<?php  if(empty($_W['isajax'])) { ?><div class="list" id="list_<?php  echo $c['parentid'];?>_<?php  echo $c['id'];?>"><?php  } ?>
	<?php  if(empty($_W['isajax'])) { ?><div class="list-tips" style='float:left;width:100%;font-size:14px;color:#e9342a;padding-left:10px;'><?php  echo $c['name'];?></div><?php  } ?>
		<?php  if(is_array($c['list'])) { foreach($c['list'] as $item) { ?>
	   <?php include $this->template('list_item');?>
		<?php  } } ?>
		<?php  if(empty($_W['isajax'])) { ?></div>
	<div class="show-more"><a href="javascript:;" onclick="loadPage('<?php  echo $pindex;?>', 'list_<?php  echo $c['parentid'];?>_<?php  echo $c['id'];?>', '<?php  echo $c['parentid'];?>', '<?php  echo $c['id'];?>')" class="img-rounded pager" id="pager_<?php  echo $c['parentid'];?>_<?php  echo $c['id'];?>">浏览更多商品</a></div>
	<?php  } ?>
	<?php  } ?>

	<?php  } } ?>
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

</script>

<?php include $this->template('footer');?>
<?php include $this->template('footerbar');?>
<?php  } ?>