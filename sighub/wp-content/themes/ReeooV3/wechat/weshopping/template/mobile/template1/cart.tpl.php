<?php defined('IN_IA') or exit('Access Denied');?><?php  $bootstrap_type = 3;?>
<?php  
	$upload =wp_upload_dir();
?>
<?php include $this->template('header');?>
<?php include $this->template('common');?>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/wechat/weshopping/images/jquery.gcjs.js"></script>
<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/wechat/weshopping/images/style.css">
<style>
.shopcart-footer{margin-bottom:30px;}
</style>
<div class="head">
	<a href="javascript:history.back();" class="bn pull-left"><i class="fa fa-angle-left"></i></a>
	<span class="title">购物车</span>
	<a href="javascript:void(0)" onclick="clearCart()" class="bn pull-right" style="font-size:18px;"><i class="fa fa-trash"></i> 清空</a>
</div>
<div class="shopcart-main img-rounded">
<!--	<div class="shopcart-hd">
		<span class="pull-left"><?php  if(empty($_W['account']['name'])) { ?>微擎团队<?php  } else { ?><?php  echo $_W['account']['name'];?><?php  } ?>»</span>
		<a class="pull-right fa fa-remove-sign" href="<?php  echo $this->createMobileUrl('clear');?>" onclick="return confirm('此操作不可恢复，确认？'); return false;"></a>
	</div>-->
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
	<div class="shopcart-item clearfix" id='item_<?php  echo $item['id'];?>' style='height:<?php  if(!empty($mid)&&!empty($goods['point'])&&$goods['ismanual']!='1') { ?> 180px; <?php }else{ if(!empty($goods['optionname'])) { ?>140px;<?php  } else { ?>120px<?php  } }?>'>
	<!--point update-->
		<img src="<?php  echo $_W['attachurl'];?><?php  echo $goods['thumb'];?>">
		<div class="shopcart-item-detail">
			<div class="name"><?php  echo $goods['title'];?><?php  if($goods['unit']) { ?><?php  } ?></div>
			<?php  if(!empty($goods['optionname'])) { ?>
				<div class="price">规格：<span><?php  echo $goods['optionname'];?></span></div>
			<?php  } ?>
			<?php if($goods['ismanual']=='1'){ ?>
			<div class="price">
			   	金额 : ¥ <input type="tel" onchange="changeprice(<?php echo $item['id'];?>)" name="manual_price" class="form-control input-sm pricetotal goodsnum ismanual" value="<?php  echo $item['totalprice'];?>" price="<?php  echo $goods['market_price'];?>" pricetotal="<?php  echo $item['totalprice'];?>" id="goodsnum_<?php  echo $item['id'];?>" cartid='<?php  echo $item['id'];?>' maxbuy="<?php  echo $goods['maxbuy'];?>" style="width: 64px;display: inline;"/>
			</div>
			<div class="text-right pull-right" style="height:35px; line-height:50px;">
				<a href="javascript:;" onclick="removeCart(<?php  echo $item['id'];?>)" class="shopcart-item-remove"><i class="fa fa-remove"></i> 删除</a>
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
					<input style="border-color:#ccc;" type="checkbox" id="ispoint_<?php  echo $item['id'];?>" value="1" <?php if(!empty($goods['ispoint'])){ ?> checked="checked" <?php } ?>  onclick="checkpoint('<?php  echo $item['id'];?>')" name="ispoint"/>使用积分购买
				</label>
			</div>
			<!--point END-->
			
			<div class="clearfix">
				<div class="input-group pull-left">
					<span class="input-group-btn">
						<button class="btn btn-default btn-sm" type="button" onclick="reduceNum(<?php  echo $item['id'];?>)"><i class="fa fa-minus"></i></button>
					</span>
					<input onchange="clickchangeprice('<?php  echo $item['id'];?>','<?php  echo $goods['maxbuy'];?>')" type="tel" class="form-control input-sm pricetotal goodsnum" value="<?php  echo $item['total'];?>" price="<?php  echo $goods['market_price'];?>" pricetotal="<?php  echo $item['totalprice'];?>" id="goodsnum_<?php  echo $item['id'];?>" cartid='<?php  echo $item['id'];?>' maxbuy="<?php  echo $goods['maxbuy'];?>" />
					<span class="input-group-btn">
						<button class="btn btn-default btn-sm" type="button" onclick="addNum(<?php  echo $item['id'];?>,<?php  echo $goods['maxbuy'];?>)"><i class="fa fa-plus"></i></button>
					</span>
				</div>
				<div class="text-right pull-right" style="height:35px; line-height:50px;">
					<a href="javascript:;" onclick="removeCart(<?php  echo $item['id'];?>)" class="shopcart-item-remove"><i class="fa fa-remove"></i> 删除</a>
				</div>
			</div>
			<?php }?>
		</div>
	</div>

	<?php  $n++;?>
	<?php  } } ?>
</div>
<div style='height:80px;width:100%;'>&nbsp;</div>
<div id='cartfooter' class="shopcart-footer" <?php  if(count($list)<=0) { ?>style='display:none'<?php  } ?> style="z-index:3;">
	<span class="pull-left">合计：<span id="pricetotal"><?php  echo $price;?></span> 元  </span>
	<span id="pointtotal" style="margin-left:10px;"><?php  echo $pointtotal;?></span> 积分</span><!--point update-->
	<a href="javascript:void(0)" onclick='buynow()'  class="btn btn-success pull-right" >立即结算</a>
</div>
<?php require_once 'wp-content/themes/ReeooV3/wechat/weshopping/template/mobile/common/cart_common.php'; ?>
<?php include $this->template('footer');?>
<?php include $this->template('footerbar');?>