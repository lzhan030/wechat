<?php defined('IN_IA') or exit('Access Denied');?><?php  $bootstrap_type = 3;?>
<?php include $this->template('header');?>
<?php include $this->template('common');?>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/wechat/weshopping/images/jquery.gcjs.js"></script>
<script type='text/javascript' src='<?php bloginfo('template_directory'); ?>/wechat/weshopping/images/touchslider.min.js'></script>
<script language='javascript' src='<?php bloginfo('template_directory'); ?>/wechat/weshopping/images/photoswipe/simple-inheritance.min.js'></script>
<script language='javascript' src='<?php bloginfo('template_directory'); ?>/wechat/weshopping/images/photoswipe/photoswipe-1.0.11.min.js'></script>
<link href="<?php bloginfo('template_directory'); ?>/wechat/weshopping/images/photoswipe/photoswipe.css" rel="stylesheet" />
<script language="javascript" src="<?php bloginfo('template_directory'); ?>/wechat/weshopping/images/touchslider.min.js"></script>
<script language="javascript" src="<?php bloginfo('template_directory'); ?>/wechat/weshopping/images/swipe.js"></script>
<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/wechat/weshopping/images/style.css?1">
<style>
	/*用于规格的选中
	.current img{
		border:3px solid red;
	}*/
	.optionimg.current img{
		border:3px solid red;
	}
	.option.current{
		border:3px solid red;
	}	
</style>
<div class="head">
	<a href="javascript:history.back();" class="bn pull-left"><i class="fa fa-angle-left"></i></a>
	<span class="title">商品详情</span>
	<a href="<?php  echo $this->createMobileUrl('list')?>" class="bn pull-right" style="margin-right:30px;"><i class="fa fa-home"></i></a>
	<a href="<?php  echo $this->createMobileUrl('mycart')?>" class="bn pull-right"><i class="fa fa-shopping-cart"></i><span class="buy-num img-circle" id="carttotal"><?php  echo $carttotal;?></span></a>
</div>
<div class="detail-main" style='margin-bottom:65px;'>
	<div class="detail-img">
		<div id="banner_box" class="box_swipe">
			<ul style="background:#FFF;">
				<?php  if(is_array($piclist)) { foreach($piclist as $row) { ?>
				<li style="text-align:center;list-style: none;">
					<a href="<?php  echo $row?($_W['attachurl'].$row):'#'; ?>" rel='<?php  echo $row?($_W['attachurl'].$row):''; ?>'>
						<img src="<?php  echo $row?($_W['attachurl'].$row):''; ?>" alt="" height="200px" style="width: 100%"/>
					</a>
				</li>
				<?php  } } ?>
			</ul>
			<ol>
	   			<?php  if(is_array($piclist)) { foreach($piclist as $row) { ?>
				<li class="on"></li>
				<?php  } } ?>
			</ol>
		</div>
<script>
var proimg_count = <?php  echo count($piclist)?>;
$(function() {
	new Swipe($('#banner_box')[0], {
		speed:500,
		auto:3000,
		callback: function(){
			var lis = $(this.element).next("ol").children();
			lis.removeClass("on").eq(this.index).addClass("on");
		}
	});
	if (proimg_count > 0) {
		(function(window, $, PhotoSwipe) {
			$('#banner_box ul li a[rel]').photoSwipe({});
		}(window, window.jQuery, window.Code.PhotoSwipe));
	}
});
</script>
	</div>
	<div class="detail-div img-rounded">
		<div class="detail-group text-center" style="line-height:20px;font-weight:bold;font-size:18px;"><?php  echo $goods['title'];?></div>
		<?php if($goods['ismanual']=='1'){ ?>
		<div class="detail-group" style='margin-top:10px;'>
			<span class="col-xs-8" style="width:100%;">
		   	 	金额 : ¥ <input type="tel" class="form-control input-sm pricetotal goodsnum ismanual" style="display:inline;width:64px;text-align:center" value="1" id="total"  />
			</span>
		</div>
		<?php }else{?>
		<div class="detail-group" style='margin-top:10px;'>
			<span class="col-xs-8" style="width:100%;">
				<?php  if($marketprice==$productprice) { ?>
				现价 : ¥ <span id='marketprice' class="text-danger" style="font-size:18px; font-weight:bold;"><?php  echo $marketprice;?></span> <?php  if(!empty($goods['unit'])) { ?>/ <?php  echo $goods['unit'];?><?php  } ?>
				<?php  } else { ?>
		   	 	现价 : ¥ <span class="text-danger" id='marketprice'  style="font-size:18px; font-weight:bold;"><?php  echo $marketprice;?></span> <span id='productpricecontainer' style='<?php  if($productprice<=0) { ?>display:none<?php  } ?>'>  &nbsp;原价 : <del style="font-size:14px; ">¥  <span id='productprice'><?php  echo $productprice;?></span></del></span>
				<?php  } ?>
			</span>
		</div>
		<!--point -->
		<div class='detail-group pointdiv' style="margin-top:10px;<?php  if(!empty($mid)&&!empty($goods['point'])) { ?> 
		<?php }else{ ?> display:none; <?php } ?>" ><!--如果是oauth2.0则无需会员,不显示-->
			<span style="float:left;margin-left:15px; margin-top:5px;">积分兑换 :</span>
			<span id='point' class="text-danger" style="font-size:18px; font-weight:bold;">
					<?php  echo $goods['point'];?>
			</span> 
			分</span> <?php  if(!empty($goods['unit'])) { ?>/ <?php  echo $goods['unit'];?><?php  } ?>
		</div>
		<div class='detail-group pointdiv' style="margin-top:10px;margin-left:16px;<?php  if(!empty($mid)&&!empty($goods['point'])) { ?> 
		<?php }else{ ?> display:none; <?php } ?>">
			<label class="checkbox-inline">
				<input style="border-color:#ccc;" type="checkbox" id="ispoint" value="1" name="ispoint"/>使用积分购买
			</label>
		</div>
		<!--point END-->
		<div class='detail-group' style="margin-top:10px;">
			<span style="float:left;margin-left:15px; margin-top:5px;">数量 :</span>
			<div class="input-group" style="width:100px;float:left;margin-left:8px;">
				<span class="input-group-btn">
					<button class="btn btn-default btn-sm" type="button" onclick="reduceNum()"><i class="fa fa-minus"></i></button>
				</span>
				<input onchange="addmanualnum()" type="tel" class="form-control input-sm pricetotal goodsnum" style="width:50px;text-align:center" value="1" id="total"  />
				<span class="input-group-btn">
					<button class="btn btn-default btn-sm" type="button" onclick="addNum()"><i class="fa fa-plus"></i></button>
				</span>
			</div> 
			<?php  if($stock!=-1) { ?>
			<span id='stockcontainer' class="help-block" style="float:left;margin-left:5px;">（ 剩余 <span id='stock'><?php  echo $stock;?></span> ）</span>
			<?php  } else { ?>
			<span id='stockcontainer' class="help-block" style="float:left;margin-left:5px;"><span id='stock'></span></span>
			<?php  } ?>
		</div>
		<?php }?>
		<?php  if(is_array($specs)) { foreach($specs as $spec) { ?>
		<input type='hidden' name="optionid[]" class='optionid optionid_<?php  echo $spec['id'];?>' value="" title="<?php  echo $spec['title'];?>">
		<div id='option_group' class='detail-group' style="margin-top:10px;">
			<div class="detail-group">
				<span style='float:left;display:block;height:30px;line-height:30px;overflow:hidden;text-overflow:ellipsis;margin-left:15px;padding:0'><?php  echo $spec['title'];?></span>
				<span style="float:left;display:block;height:30px;line-height:30px;padding:0 3px;">:</span>
				<span style="float:left;margin-left:8px;" class='options options_<?php  echo $spec['id'];?>' specid='<?php  echo $spec['id'];?>'>
				<?php  if(is_array($spec['items'])) { foreach($spec['items'] as $o) { ?>
				<?php  if(empty($o['thumb'])) { ?>
				<span class="property option option_<?php  echo $spec['id'];?>" specid='<?php  echo $spec['id'];?>' oid="<?php  echo $o['id'];?>"  sel='false'><?php  echo $o['title'];?></span>
				<?php  } else { ?>
				<span class="propertyimg optionimg option_img_<?php  echo $spec['id'];?> " oid="<?php  echo $o['id'];?>" specid='<?php  echo $spec['id'];?>' sel='false'><img src="<?php  echo $_W['attachurl'].$o['thumb']?>" width='50' height='70' /></span>
				<?php  } ?>
				<?php  } } ?>
				</span>
			</div>
		</div>
		<?php  } } ?>
	</div>
	<?php if(!empty($goods['content'])) {?>
		<div class="detail-div img-rounded detail-content" style="word-break:break-all">
			<span class="item-title">商品描述：</span>
			<div class="detail-group">
				<span style="padding-left: 15px; padding-right: 15px;"><?php  echo $goods['content'];?></span>
			</div>
		</div>
	<?php }?>
	<?php  if(count($params)>0) { ?>
	<div class="detail-div img-rounded other-detail">
		<span class="item-title">其他详情：</span>
	<?php  if(is_array($params)) { foreach($params as $p) { ?>
		<div class="detail-group">
			<span class="col-xs-4" style="padding-left: 15px;padding-right: 15px;"><?php  echo $p['title'];?></span>
			<span class="col-xs-8"><?php  echo $p['value'];?></span>
   		</div>
	<?php  } } ?>
	</div>
	<?php  } ?>
	<div style="position:fixed; bottom:0; left:0; width:100%; z-index:88; text-align:center; background:#E9E9E9; padding:10px 2%;">
		<?php  if($goods['status']==1 || ($goods['deleted']==2 && (time()<strtotime($goods['timestart'])||time()>strtotime($goods['timeend'])))) { ?>
			<a href="javascript:void(0)" class="btn btn-default col-xs-12"  style="width:100%;">此商品已下架</a>
		<?php  } else { ?>
			<input type="hidden"  id="optionid" name="optionid" value="" />
			<a href="javascript:void(0)" onclick='addtocart()' class="btn btn-danger col-xs-12" style="width:45%;"><i class="fa fa-plus"></i> 添加到购物车</a>
			<a href="javascript:void(0)" onclick='buy()' class="btn btn-success col-xs-12"  style="float:right; width:45%;">立即购买</a>
		<?php  } ?>
	</div>
</div>
<?php require_once 'wp-content/themes/ReeooV3/wechat/weshopping/template/mobile/common/detail_common.php'; ?>
<?php  $title = $goods['title'];?>
<?php include $this->template('footer');?>
<?php include $this->template('footerbar');?>
<script>
$(function() {
	$(".footerbar").hide();
	$("#footer").hide();
});
</script>
