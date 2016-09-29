<?php defined('IN_IA') or exit('Access Denied');?>
<?php  
	$bootstrap_type = 3; 
	$upload =wp_upload_dir();
?>
<?php include $this -> template('header');?>
<?php include $this -> template('common');?>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/wechat/weshopping/js/jquery.gcjs.js"></script>
<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/wechat/weshopping/css/style.css?v=<?php echo time();?>">
<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory'); ?>/wechat/weshopping/css/base.css">
<style>
	.shopcart-footer{margin-bottom:30px;}
</style>

<div id="top" style="margin-top: -45px;">
    <div class="header-title1">购物车</div>
	<div class="site-nav">
		<ul class="fix">
			<li class="home"><a href="<?php echo $this->createMobileUrl('list', array('gweid' => $gweid))?>">首页</a></li>
			<li class="mysn"><a href="<?php echo $this->createMobileUrl('contactUs', array('gweid' => $gweid))?>">小店介绍</a></li>
			<li class="mycart"><a href="<?php echo $this->createMobileUrl('mycart', array('gweid' => $gweid))?>">购物车</a></li>
			<li class="allcate"><a href="<?php  echo $this->createMobileUrl('allcategories', array('gweid' => $gweid))?>">商品分类</a></li>
		</ul>
	</div>
</div>

<div class="shopcart-main img-rounded">
	<div style='text-align:center;padding:50px 0 50px 0; <?php  if(count($list)>0) { ?>display:none<?php  } ?>' id='cartempty'>
		<img src='<?php bloginfo('template_directory'); ?>/wechat/weshopping/images/icon_cart_empty.png' /><br/><br/>
		<span style='color:#adadad'>您的购物车空空如也，赶紧去选购吧~~</span>
	</div>
	<?php  
	$price=0;
	$pointtotal=0;
	if(is_array($list)) { foreach($list as $item) { ?>
	<!--point update-->
	<?php	
	if(intval($item['goods']['ispoint'])==0 || intval($item['goods']['point'])==0){
		$price += $item['totalprice'];
	}else{
		$pointtotal+=intval($item['goods']['point'])*intval($item['total']);
	}
	?>
	<!--point update END-->
	<?php  $goods = $item['goods']?>
	<span id="stock_<?php  echo $item['id'];?>" style='display:none'><?php  echo $goods['total'];?></span>
	<div class="shopcart-item clearfix" id='item_<?php  echo $item['id'];?>' style='height:<?php  if(!empty($mid)&&!empty($goods['point'])&&$goods['ismanual']!='1') { ?> 180px; <?php }else{ if(!empty($goods['optionname'])) { ?>140px;<?php  } else { ?>120px<?php  } }?>'><!--point update-->
		<img src="<?php if((empty($goods['thumb']))||(stristr($goods['thumb'],"http")!==false)){echo $goods['thumb'];}else{echo $upload['baseurl'].$goods['thumb'];}?>">
		<div class="shopcart-item-detail">
			<div class="name" style="font-size: 14px;"><?php  echo $goods['title'];?><?php  if($goods['unit']) { ?><?php  } ?></div>
			<?php  if(!empty($goods['optionname'])) { ?>
				<div class="price">规格：<span><?php  echo $goods['optionname'];?></span></div>
			<?php  } ?>
			<?php if($goods['ismanual']=='1'){ ?>
				
				<div class="price" style="float:left;line-height: 25px;">
					价格：<input onchange="changeprice(<?php echo $item['id'];?>)"  name="manual_price"  style=" float:right;width:70px;text-align: center;background-color:#fff"  value="<?php  echo $item['totalprice'];?>" price="<?php  echo $goods['market_price'];?>" pricetotal="<?php  echo $item['totalprice'];?>" id="goodsnum_<?php  echo $item['id'];?>" cartid='<?php  echo $item['id'];?>' maxbuy="<?php  echo $goods['maxbuy'];?>" class="form-control input-sm pricetotal goodsnum ismanual"></input> ￥
				</div>
				
				<div class="text-right pull-right" style="height:35px; line-height:40px;">
					<a href="javascript:;" onclick="removeCart(<?php  echo $item['id'];?>)" class="shopcart-item-remove" style="font-size: 14px;"><i class="fa fa-remove"></i> 删除</a>
				</div>
			
			<?php }else{?>
				<div class="price">单价：<span id="singleprice_<?php  echo $item['id'];?>"><?php  echo $goods['market_price'];?></span> 元<?php  if(!empty($goods['unit'])) { ?> / <?php  echo $goods['unit'];?><?php  } ?></div>
			
				<!--point -->
				<div class="price" style="<?php  if(!empty($mid)&&!empty($goods['point'])) { ?> 
				<?php }else{ ?> display:none; <?php } ?>">
					积分兑换 :
					<span id="point_<?php  echo $item['id'];?>">
							<?php  echo $goods['point'];?>
					</span> 
					分<?php  if(!empty($goods['unit'])) { ?> / <?php  echo $goods['unit'];?><?php  } ?>
				</div>
				<!--point END-->
			
				<div id="pricedisplay_<?php  echo $item['id'];?>" style="<?php  if(!empty($mid)&&!empty($goods['point'])&&!empty($goods['ispoint'])) { ?> display:none;
				<?php }else{ ?>  <?php } ?>" class="price">小计：<span class='singletotalprice' id="goodsprice_<?php  echo $item['id'];?>"><?php  echo $item['totalprice'];?></span> 元</div>
				
				<!--point -->
				<div id="pointdisplay_<?php  echo $item['id'];?>" style="<?php  if(!empty($mid)&&!empty($goods['point'])&&!empty($goods['ispoint'])) { ?> 
				<?php }else{ ?> display:none; <?php } ?>" class="price">小计：<span class='singletotalprice_pointdisplay' id="goodsprice_pointdisplay_<?php  echo $item['id'];?>"><?php  echo $item['totalpoint'];?></span> 分</div>
				<!--point END-->
			
			
				<!--point -->
				<div class="price" style="<?php  if(!empty($mid)&&!empty($goods['point'])) { ?> 
				<?php }else{ ?> display:none; <?php } ?>"> 
					<label class="checkbox-inline">
						<input style="-webkit-appearance:checkbox;border-radius:4px;border-color:#ccc;" type="checkbox" id="ispoint_<?php  echo $item['id'];?>" value="1" <?php if(!empty($goods['ispoint'])){ ?> checked="checked" <?php } ?>  onclick="checkpoint('<?php  echo $item['id'];?>')" name="ispoint"/>使用积分购买
					</label>
				</div>
				<!--point END-->
			
				<div class="clearfix">
				<div class="input-group pull-left" style="width:120px;">
					<span class="input-group-btn">
						<button style="width:40px; background: white;" class="btn btn-default btn-sm" type="button" onclick="reduceNum(<?php  echo $item['id'];?>)"><i><img style="width: 18px;" src="<?php bloginfo('template_directory'); ?>/images/delete.gif" ></i></button>
					</span>
			
					<input onchange="clickchangeprice('<?php  echo $item['id'];?>','<?php  echo $goods['maxbuy'];?>')" type="tel" style="width: 50px;" class="form-control input-sm pricetotal goodsnum" value="<?php  echo $item['total'];?>" price="<?php  echo $goods['market_price'];?>" pricetotal="<?php  echo $item['totalprice'];?>" id="goodsnum_<?php  echo $item['id'];?>" cartid='<?php  echo $item['id'];?>' maxbuy="<?php  echo $goods['maxbuy'];?>" />
					
					<span class="input-group-btn">
						<button style="width:40px; background: white;" class="btn btn-default btn-sm" type="button" onclick="addNum('<?php  echo $item['id'];?>','<?php  echo $goods['maxbuy'];?>')"><i><img style="width:18px" src="<?php bloginfo('template_directory'); ?>/images/add.gif" ></i></button>
					</span>
				</div>
				<div class="text-right pull-right" style="height:35px; line-height:50px;">
					<a href="javascript:;" onclick="removeCart(<?php  echo $item['id'];?>)" class="shopcart-item-remove" style="font-size: 14px;"><i class="fa fa-remove"></i> 删除</a>
				</div>
			</div>
			<?php }?>
		</div>
	</div>
	<?php  $n++;?>
	<?php  } } ?>
</div>
<div style='height:80px;width:100%;'>&nbsp;</div>
<div id='cartfooter' class="shopcart-footer" <?php  if(count($list)<=0) { ?>style='display:none'<?php  } ?>' style="z-index:3; position: relative;">
	<span class="pull-left">合计：<span id="pricetotal"><?php  echo $price;?></span> 元</span>
	<span id="pointtotal" style="margin-left:10px;"><?php  echo $pointtotal;?></span> 积分</span><!--point update-->
	<!--<a href="<?php  echo $this->createMobileUrl('confirm', array('gweid' => $gweid))?>" class="btn btn-success pull-right">立即结算</a>-->
	<a href="javascript:void(0)" onclick='buynow()'  class="btn btn-success pull-right" >立即结算</a>
</div>
<?php require_once 'wp-content/themes/ReeooV3/wechat/weshopping/template/mobile/common/cart_common.php'; ?>
<?php include $this -> template('footer');?>