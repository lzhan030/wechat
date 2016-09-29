<?php defined('IN_IA') or exit('Access Denied');?>
<?php  
	$bootstrap_type = 3;
	$upload =wp_upload_dir();
?>
<?php include $this -> template('header');?>
<?php include $this -> template('common');?>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/wechat/weshopping/js/jquery.gcjs.js"></script>
<script type='text/javascript' src='<?php bloginfo('template_directory'); ?>/wechat/weshopping/js/touchslider.min.js'></script>
<script language='javascript' src='<?php bloginfo('template_directory'); ?>/wechat/weshopping/js/photoswipe/simple-inheritance.min.js'></script>
<script language='javascript' src='<?php bloginfo('template_directory'); ?>/wechat/weshopping/js/photoswipe/photoswipe-1.0.11.min.js'></script>
<link href="<?php bloginfo('template_directory'); ?>/wechat/weshopping/js/photoswipe/photoswipe.css" rel="stylesheet" />
<script language="javascript" src="<?php bloginfo('template_directory'); ?>/wechat/weshopping/js/touchslider.min.js"></script>
<script language="javascript" src="<?php bloginfo('template_directory'); ?>/wechat/weshopping/js/swipe.js"></script>

<!--<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/wechat/weshopping/css/style.css?v=<?php echo time();?>">-->
<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/wechat/weshopping/images/style.css?v=<?php echo time();?>">
<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory'); ?>/wechat/weshopping/css/base.css?v=<?php echo time();?>">
<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory'); ?>/wechat/weshopping/css/detail.css?v=<?php echo time();?>">
<style>
    .content img{max-width:98%;}
    ol li, ul li{list-style: inherit;}
</style>
<div id="top" style="margin-top: -40px;">
	<div class="back-ui-a">
	<a href="javascript:history.back(1)">返回</a>
	</div>
	<div class="header-title">商品说明</div>
	<div class="site-nav">
		<ul class="fix">
			<li class="home"><a href="<?php echo $this->createMobileUrl('list', array('gweid' => $gweid))?>">首页</a></li>
			<li class="mysn"><a href="<?php echo $this->createMobileUrl('contactUs', array('gweid' => $gweid))?>">小店介绍</a></li>
			<li class="mycart"><a href="<?php echo $this->createMobileUrl('mycart', array('gweid' => $gweid))?>">购物车</a></li>
			<li class="allcate"><a href="<?php  echo $this->createMobileUrl('list2', array('gweid' => $gweid))?>">商品分类</a></li>
		</ul>
	</div>
</div>

<div class="line"></div>
<div class="detail-img">
	<div id="banner_box" class="box_swipe">
		<ul style="background:#FFF;">
			<?php  if(is_array($piclist)) { foreach($piclist as $row) { ?>
			<li style="text-align:center;list-style: none;">
				<a href="<?php  echo $_W['attachurl'];?><?php  echo $row;?>" rel='<?php  echo $_W['attachurl'];?><?php  echo $row;?>'>
					<img src="<?php if((empty($row))||(stristr($row,"http")!==false)){echo $row;}else{echo $upload['baseurl'].$row;}?>" alt="" height="220px" style=""/>
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
<div class="line"></div>
<!--买家输入的不显示对应的已出售多少件-->
<?php if($goods['ismanual']!='1'){ ?>
<div style="margin:0px 5px;color:#777">
	<span>重量：<?php  echo $goods['weight']."克";?> &nbsp;&nbsp;&nbsp;已出售：<?php echo $goods['sales'];?>&nbsp;&nbsp;<?php echo $goods['unit'];?></span>
</div>
<div class="line"></div>
<?php }?>
<div style="margin:5px;">
	<div style="font-weight: bold;margin-left:10px;font-size:18px;margin-bottom: 5px;"><?php  echo $goods['title'];?></div>
</div>
<div style="margin-left:5px;">
    <!--ismanual-->
	<?php if($goods['ismanual']=='1'){ ?>
	<div style="margin-left: 15px;margin-top: 15px;">
		<label for="market_price" >金额(元)</label>
		<input name="manual_price" class="ismanual" style=" width:90px;text-align: center;background-color:#fff"  value="1" id="total"></input><!--point update(add class ismanual,value null to 1,add id=total,remove id=manual_price)-->
	</div>
	<?php }?>
	<?php if($goods['ismanual']!='1'){ ?>
	<div class='detail-group' style="margin-top:10px;">
		<span style="float:left;margin-left:15px; margin-top:5px;">数量 :</span>
		<div class="input-group" style="width:100px;float:left;margin-left:8px;">
			<span class="input-group-btn">
				<button class="btn btn-default btn-sm" type="button" onclick="reduceNum()"><i><img style="width: 15px;" src="<?php bloginfo('template_directory'); ?>/images/delete.gif" ></i></button>
			</span>
			<input onchange="addmanualnum()" type="tel" class="form-control input-sm pricetotal goodsnum" style="width:50px;text-align:center" value="1" id="total"  />
			<span class="input-group-btn">
				<button class="btn btn-default btn-sm" type="button" onclick="addNum()"><i><img style="width:15px" src="<?php bloginfo('template_directory'); ?>/images/add.gif" ></i></button>
			</span>
		</div>		
		<?php  if($stock!=-1) { ?>
		<span id='stockcontainer' style="float:left;margin-left:5px;line-height: 30px;" class="help-block">( 剩余 <span id='stock'><?php  echo $stock;?></span> )</span>
		<?php  } else { ?>
		<span id='stockcontainer' style="float:left;margin-left:5px;"><span id='stock'></span></span>
		<?php  } ?>
	</div>
	<div class="detail-group" style='margin-top:10px;'>
		<span class="col-xs-8" style="width:100%;">
			<?php  if($marketprice==$productprice) { ?>
			现价 : <span style="margin-left: 8px;">¥ </span><span id='marketprice' class="text-danger" style="font-size:18px; font-weight:bold;"><?php  echo $marketprice;?></span> <?php  if(!empty($goods['unit'])) { ?>/ <?php  echo $goods['unit'];?><?php  } ?>
			<?php  } else { ?>
			现价 : <span style="margin-left: 8px;">¥ </span><span class="text-danger" id='marketprice'  style="font-size:18px; font-weight:bold;"><?php  echo $marketprice;?></span> <span id='productpricecontainer' style='<?php  if($productprice<=0) { ?>display:none<?php  } ?>'>  &nbsp;原价 : <del style="font-size:14px; ">¥  <span id='productprice'><?php  echo $productprice;?></span></del></span>
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
			<input style="padding:6px;-webkit-appearance:checkbox;border-radius:4px;border-color:#ccc;" type="checkbox" id="ispoint" value="1" name="ispoint"/>使用积分购买
		</label>
	</div>
	<!--point END-->
	<?php }?>
	<?php 	
	if(is_array($specs)) 
	{ foreach($specs as $spec) { ?>
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
			<span class="propertyimg optionimg option_img_<?php  echo $spec['id'];?> " oid="<?php  echo $o['id'];?>" specid='<?php  echo $spec['id'];?>' sel='false'><img src="<?php if((empty($o['thumb']))||(stristr($o['thumb'],"http")!==false)){echo $o['thumb'];}else{echo $upload['baseurl'].$o['thumb'];}?>" width='50' height='70' /></span>
			<?php  } ?>
			<?php  } } ?>
			</span>
		</div>
	</div>
	<?php  } } ?>
	<?php  if(count($params)>0) { ?>
	<!--<div class="detail-div img-rounded other-detail">
	<?php  if(is_array($params)) { foreach($params as $p) { ?>
		<div class="detail-group">
			<span class="col-xs-4"><?php  echo $p['title'];?></span>
			<span class="col-xs-8"><?php  echo $p['value'];?></span>
		</div>
	<?php  } } ?>
	</div>-->
	<?php  } ?>
</div>
<div class="line"></div>
<div style="margin:5px;">
	<?php  if(count($params)>0) { ?>
	<?php  if(is_array($params)) { foreach($params as $p) { ?>
		<div style="margin-left:15px;margin-bottom:5px;">
			<span><font style="color:#777;"><?php  echo $p['title']."：";?></font><span style="margin-left:10px;"><?php  echo $p['value'];?></span></span>
		</div>
	<?php  } } ?>
	<?php  } ?>
	<?php if(!empty($goods['content'])) {?>
		<div class="content" style="margin-left: 15px;"><font style="color:#777;">商品描述：</font></span><span style="margin-left:10px;"><?php  echo $goods['content'];?></span></div>
	<?php }?>
</div>
<div class="layout" style="margin-top:10px">	
	<?php  if($goods['status']==1 || $goods['deleted']==1) { ?>
		<a href="javascript:void(0)" class="btn btn-default col-xs-12"  style="width:100%; margin-top:30px;">此商品已下架</a>
	<?php  } else { ?>
	    
		<input type="hidden"  id="optionid" name="optionid" value="" />
		<div style="position:fixed; bottom:0; left:0; width:100%; z-index:88; text-align:center; background:#E9E9E9; padding:10px 2%;">
			<input type="hidden" id="optionid" name="optionid" value="">
			<a href="javascript:void(0)" onclick="addtocart()" class="btn btn-danger col-xs-12" style="width:45%;"><i class="fa fa-plus"></i> 加入购物车</a>
			<a href="javascript:void(0)" onclick="buy()" class="btn btn-success col-xs-12" style="float:right; width:45%;">立即购买</a>
		</div>
	<?php  } ?>
</div>
<!--<div class="line"></div>-->
<?php require_once 'wp-content/themes/ReeooV3/wechat/weshopping/template/mobile/common/detail_common.php'; ?>
<?php  $title = $goods['title'];?>
<script>
$(function() {
	$("#footer").hide();
});
</script>
