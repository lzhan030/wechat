<?php defined('IN_IA') or exit('Access Denied');?><?php  $bootstrap_type = 3;?>
<?php include $this->template('header');?>
<?php include $this->template('common');?>
<script src="<?php bloginfo('template_directory'); ?>/js/jquery.form.js" type="text/javascript" ></script>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/wechat/weshopping/js/app/cascade.js?1"></script>
<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/wechat/weshopping/images/style.css?v=<?php echo time();?>">
<div class="head">
	<a href="javascript:history.back();" class="bn pull-left"><i class="fa fa-angle-left"></i></a>
	<span class="title">结算</span>
	<a href="<?php  echo $this->createMobileUrl('mycart')?>" class="bn pull-right"><i class="fa fa-shopping-cart"></i><span class="buy-num img-circle"> <?php  echo $carttotal;?> </span></a>
</div>
<form class="form-horizontal" method="post" role="form" onsubmit='return check()'>
	<input type="hidden" name="goodstype" value="<?php  echo $goodstype;?>" />
	<input type="hidden" name="address" value="<?php  echo $row['id'];?>" />
	<div class="order-main" >
		<div style="<?php if(!$needdispatch){ echo 'display:none';} ?>">
			<h5>收货地址</h5>
			<!--<div id="myaddress">
				<?php  if(!empty($row)) { ?>
				<div id='address_<?php  echo $row['id'];?>' class="shopcart-main img-rounded address_item" style='margin:0;padding:10px;margin-bottom:10px;cursor:pointer' onclick='changeAddress()'>
					<span><?php  echo $row['province'];?> <?php  echo $row['city'];?> <?php  echo $row['area'];?> <?php  echo $row['address'];?> <br/> <?php  echo $row['realname'];?>, <?php  echo $row['mobile'];?></span>
					<span style='float:right'>&nbsp;&nbsp;
						<a href="<?php  echo $this->createMobileUrl('address',array('from'=>'confirm','returnurl'=>urlencode($returnurl)))?>">管理收货地址</a>
					</span>
				</div>
				<?php  } else { ?>
				<div>
					<button type="button" class="btn btn-danger" onclick="location.href='<?php  echo $this->createMobileUrl('address',array('from'=>'confirm','returnurl'=>urlencode($returnurl)))?>'"><i class="fa fa-plus"></i> 添加修改地址</button>
				</div>
				<?php  } ?>
			</div>-->
			<div class="mobile-div img-rounded"  >
				<!--<div class="mobile-hd">收货地址</div>-->
				<div class="mobile-content" style="margin-top:11px;">
					<div class="add-address-main" style="font-size: 14px;">
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">姓名：</label>
							<div class="col-sm-9">
								<input type="text" id="username" class="form-control" value="<?php echo $order_address['username']; ?>"></input>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">联系电话：</label>
							<div class="col-sm-9">
								<input type="text" id="telnumber" class="form-control" value="<?php echo $order_address['telnumber']; ?>"></input>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">邮编：</label>
							<div class="col-sm-9">
								<input type="text" id="postalcode" class="form-control" value="<?php echo $order_address['postalcode']; ?>"></input>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">地区：</label>
							<div class="col-sm-9">
								<select id="sel-provance" onChange="selectCity();" class="pull-left form-control" style="width:30%; margin-right:5%;">
									<option value="" selected="true">省/直辖市</option>
								</select>
								<select id="sel-city" onChange="selectcounty()" class="pull-left form-control" style="width:30%; margin-right:5%;">
									<option value="" selected="true">请选择</option>
								</select>
								<select id="sel-area" class="pull-left form-control" style="width:30%;">
									<option value="" selected="true">请选择</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">详细地址：</label>
							<div class="col-sm-9">
								<input type="text" id="detailinfo" class="form-control" value="<?php echo $order_address['detailinfo']; ?>"></input>
							</div>
						</div>
					</div>
						
					<a  onclick="orderaddress()" style="height:14ps;margin-right: 4%;float:right" width="20ps" style="width:70px">获取微信收货地址</a>
				</div>
			</div>
	 		<h5>配送方式</h5>
			<select id='dispatch' name="dispatch" class="form-control">
		   	<?php  if(is_array($dispatch)) { foreach($dispatch as $d) { ?><!--point update-->
				<?php if($d['dispid']=='-2'){?>
					<option value="<?php  echo $d['id'];?>" price='<?php  echo intval($d['price']);?>' distype='<?php  echo intval($d['dispatchtype']);?>' dispid='<?php  echo intval($d['dispid']);?>' ><?php  echo $d['dispatchname'];?> (<?php  echo intval($d['price']);?>积分)</option>
				<?php }else{ ?>
					<option value="<?php  echo $d['id'];?>" price='<?php  echo $d['price'];?>' distype='<?php  echo intval($d['dispatchtype']);?>' dispid='<?php  echo intval($d['dispid']);?>'><?php  echo $d['dispatchname'];?> (<?php  echo $d['price'];?>元)</option>
				<?php } ?>
			<?php  } } ?>
			</select>
		</div>
		<h5>订单详情</h5>
		<div class="order-detail">
			<table class="table">
				<thead>
				<tr>
					<th class="name">商品</th>
					<th class="num">数量</th>
					<th class="total">合计</th>
				</tr>
				</thead>
				<tbody>
				<?php  if(is_array($allgoods)) { foreach($allgoods as $item) { ?>
				<tr>
					<span id="stock_<?php  echo $item['cartid'];?>" style='display:none'><?php  echo $item['stock'];?></span><!--point update new add-->
					<td class="name">
						<span  style="float:left;">
							<a href='<?php  echo $this->createMobileUrl('detail',array('id'=>$item['id'], 'gweid' => $gweid))?>'><?php  echo $item['title'];?></a>
							<?php  if(!empty($item['optionname'])) { ?><br/>
							<span style='font-size:12px;color:#666'> <?php  echo $item['optionname'];?></span>
							<?php  } ?>
						</span>
					</td>
					<td class="num">
					    <?php if($item['ismanual'] != 1){?>
						<div class="input-group pull-left">
							<span class="input-group-btn">
								<button class="btn btn-default btn-sm" type="button" onclick="reduceNum('<?php  echo $item['cartid'];?>')"><i class="fa fa-minus"></i></button>
							</span>
							<input onchange="clickchangeprice('<?php  echo $item['cartid'];?>',<?php  echo $item['maxbuy'];?>)" type="tel" <?php if(!empty($_GET['id'])){ ?>name="total"<?php }?>class="form-control input-sm pricetotal goodsnum" value="<?php  echo $item['total'];?>" price="<?php  echo $item['market_price'];?>" pricetotal="<?php  echo $item['totalprice'];?>" id="goodsnum_<?php  echo $item['cartid'];?>" cartid='<?php  echo $item['cartid'];?>' maxbuy="<?php  echo $item['maxbuy'];?>" />
							<span class="input-group-btn">
								<button class="btn btn-default btn-sm" type="button" onclick="addNum('<?php  echo $item['cartid'];?>',<?php  echo $item['maxbuy'];?>)"><i class="fa fa-plus"></i></button>
							</span>
							<?php  if(!empty($item['unit'])) { ?><?php  echo $item['unit'];?><?php  } ?>
						</div>	
						<!--point -->
						<div class="input-group pull-left" style="<?php  if(!empty($mid)&&!empty($item['point'])) { ?> 
						<?php }else{ ?> display:none; <?php } ?>"> 
							<label class="checkbox-inline">
								<input style="border-color:#ccc;" type="checkbox" id="ispoint_<?php  echo $item['cartid'];?>" value="1" <?php if(!empty($item['ispoint'])){ ?> checked="checked" <?php } ?>  onclick="checkpoint('<?php  echo $item['cartid'];?>')" name="ispoint"/>使用积分购买(
								<span id="point_<?php  echo $item['cartid'];?>"><?php echo $item['point']; ?></span> 分<?php  if(!empty($item['unit'])) { ?> / <?php  echo $item['unit'];?><?php  } ?>)
							</label>
						</div>
						<!--point END-->
						<!--input the 单价 hidden-->
						<input type="hidden" id="singleprice_<?php  echo $item['cartid'];?>" value="<?php  echo $item['market_price'];?>">
						<?php }else{  
							echo $item['total']; 
							 if(!empty($item['unit'])) {  echo $item['unit'];} 
						}?>
						
					</td>
					<td class="total">
						
						<?php if($item['ismanual'] == 1){?>	
							<span class='goodsprice' >
								<input type="tel" <?php  if(!empty($_GET['id'])) { ?>name="manual_price"<?php  } ?> onchange="changeprice('<?php echo $item['cartid'];?>')" id="goodsprice_<?php  echo $item['cartid'];?>" class="form-control input-sm pricetotal goodsnum ismanual" style="display:inline;width:54px;text-align:center" value="<?php echo $item['totalprice'];?>"  />元
							</span>
						<?php }else{?>
						
							<span style="<?php  if(!empty($mid)&&!empty($item['point'])&&!empty($item['ispoint'])) { ?> display:none; <?php }else{ ?>  <?php } ?>" class='goodsprice' id="goodsprice_<?php echo $item['cartid'];?>">
								<?php  echo $item['totalprice'];?> 元
							</span>
							
							<span  style="<?php  if(!empty($mid)&&!empty($item['point'])&&!empty($item['ispoint'])) { ?> 
							<?php }else{ ?> display:none;  <?php } ?>" class='goodsprice_point' id="goodsprice_point_<?php echo $item['cartid'];?>">
								<?php  echo $item['totalpoint'];?> 分
							</span>
							
						<?php }?>
						
					</td>
				</tr>
				<?php  } } ?>
				</tbody>
			</table>
			<div class="order-detail-hd">
				<span class="pull-right" style="color:#E74C3C;"><!--point update-->
					[合计：<span id='totalprice' style="margin-right:3px;"><?php  echo $totalprice;?></span>
					 积分：<span id='pointtotal'><?php  echo $pointtotal;?></span>]
				</span>
			</div>
			<div style="clear:both;"></div>
		</div>
		<h5>留言</h5>
		<div class="message-box">
			<textarea class="form-control" id="remark" rows="3" name="remark" placeholder="亲，还有什么能帮助到您吗？就写到这里吧！"></textarea>
		</div>
		<button type="button" name="submit" value="yes" style="width:100%;" class="btn btn-success btn-lg" style="margin-bottom:20px;" onClick="callpay()" <?php if((($ismanual!=1)&&($total=='0'))||($goodstatus==1)){ ?> disabled="disabled" <? } ?>>提交订单</button>
		<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
	</div>
</form>
<?php require_once 'wp-content/themes/ReeooV3/wechat/weshopping/template/mobile/common/confirm_common.php'; ?>
<?php include $this->template('footer');?>
<?php include $this->template('footerbar');?>