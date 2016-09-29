<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
<html>
	<head>
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.css">
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/font-awesome.min.css" />
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/common.mobile.css">
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/messenger.css">
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/messenger-theme-future.css">
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/goodspay.css" />
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/shopping.mobile.css" />
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/jquery-1.11.1.min.js"></script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/bootstrap.js"></script>
		<title>下订单</title>		
	</head>
	<body>	
		<div class="head">
			<span class="title">下订单</span>
		</div>
		<div class="research" style="margin-top:46px;margin-bottom:40px">
			<div class="mobile-div img-rounded">
				<div class="mobile-hd">下订单</div>	
				<div>
					<div style=" float:left;width:40%; height:153px;margin-top:2px;" name="goodsimg">
						<?php $upload =wp_upload_dir();
						if((empty($thumb))||(stristr($thumb,"http")!==false)){
							$goodsthumb=$thumb;
						}else{
							$goodsthumb=$upload['baseurl'].$thumb;
						}?>
						<?php if(!empty($goodsthumb))  {?>
							<img class="research-thumb" alt="图片" data-holder-rendered="true" style="width:100%;height:100%" src="<?php echo $goodsthumb; ?>">
						<?php } else {?>
							<div class="thumbnail" style="height:150px;"><p>没有图片</p></div>
						<?php }?>
					</div>
					<div style="height:auto;overflow:hidden;margin-left:0px;" name="goodsinfo">
						<div class="mobile-content" name="goodstitle">
							<h4 style="color:#34495e;"><?php echo $title; ?></h4>
						</div>
						<!--ismanual=1表示手动输入金额，不显示商品单价-->
						<div style="<?php if($ismanual==1){ echo 'display:none';} ?>" id="goodsprice" class="mobile-content" name="goodsprice">
						<?php if(!empty($buyer)&&($isvipprice=='1')){?>
							单价：<p><s>￥<?php echo number_format($marketprice,2,".",""); ?></s></p>
						<?php }else{ ?>
							单价：￥<?php echo number_format($price,2,".",""); ?>
						<?php } ?>
						</div>
						<div id="vipprice" style="<?php if(empty($buyer)||($isvipprice=='0')||($ismanual==1)){echo 'display:none'; } ?>" class="mobile-content" name="vipprice">
							会员价格：￥<?php echo number_format($price,2,".",""); ?>
						</div>
						<!--ismanual=1表示手动输入金额，总金额处显示手动输入文本框-->
						<div class="mobile-content" name="goodstotalfee">
							<div style="float:left;">总金额：</div>
						<?php if($ismanual==1){ ?>
							<div style="float:left;"><font style="font-size:16px;font-weight:bold;" color="#f15a28" >￥</font><input id="goodstotalfee" style="font-size:14px;font-weight:bold;color:#f15a28;background-color:#fff;width:90%;"  value="<?php echo number_format($manual_price,2,".","");?>"></input></div>
						<?php }else{ ?>
							<div style="float:left;"><font style="font-size:16px;font-weight:bold;" color="#f15a28" >￥</font><input id="goodstotalfee" style="border:0px; font-size:14px;font-weight:bold;color:#f15a28;background-color:#fff;width:90%;" disabled="disabled" value="<?php echo number_format($totalfee,2,".","");?>"></input></div>
						<?php } ?>
						</div>
						<!--ismanual=1表示手动输入金额，或者库存为0时不再显示对购买数量的操作-->
						<div style="width:200px;clear:both;<?php if($ismanual==1||$total=='0'){ echo 'display:none';} ?>" class="mobile-content" >
							数量:
							<img style="width:20px" onclick='totaldel()' src="<?php bloginfo('template_directory'); ?>/images/delete.gif" >&nbsp; 
							<input id="number" style="width:40px; text-align: center;background-color:#fff" disabled="disabled" maxLength="4" value="<?php echo $goodstotal?>" name="cart_quantity"> &nbsp;
							<img style="width:20px" onclick="totaladd('<?php echo $total; ?>')" src="<?php bloginfo('template_directory'); ?>/images/add.gif" >
						</div>
						<!--ismanual!=1表示非手动输入金额并且库存为0时提示库存不足-->
						<div style="<?php if(!(($ismanual!=1)&&($total=='0'))){ echo 'display:none';} ?>" class="mobile-content" >
							货存不足，无法购买
						</div>
						<div style="<?php if($goodstatus!=1){ echo 'display:none';} ?>" class="mobile-content" >
							商品已下架
						</div>
					</div>	
				</div>
			</div>
			<!--isdelivery==1表示不需要发货-->			
			<div class="mobile-div img-rounded" style="<?php if($isdelivery==1){ echo 'display:none';} ?>" >
				<div class="mobile-hd">收货地址</div>
				<div class="mobile-content" style="margin-top:11px;">
					<div class="address" style="margin-bottom:5px;">姓名：<input id="username" class="form-control" style="display:inline;width:76%" value="<?php echo $order_address['username']; ?>"></input></div>
					<div class="address" style="margin-bottom:5px;">电话：<input id="telnumber" class="form-control" style="display:inline;width:76%" value="<?php echo $order_address['telnumber']; ?>"></input></div>
					<div class="address" style="margin-bottom:5px;">邮编：<input id="postalcode" class="form-control" style="display:inline;width:76%" value="<?php echo $order_address['postalcode']; ?>"></input></div>
					<div class="address" style="margin-bottom:5px;">地区：<input id="stagename" class="form-control" style="display:inline;width:76%" value="<?php echo $order_address['stagename']; ?>"></div>
					<div class="address" style="margin-bottom:5px;">地址：<input id="detailinfo" class="form-control" style="display:inline;width:76%" value="<?php echo $order_address['detailinfo']; ?>"></div>
					<!--<input id="stagename" style="border:1;display:none" value=""></input>
					<img  onclick="orderaddress()" style="height:14ps;margin-left:89%;" width="20ps" src="<?php bloginfo('template_directory'); ?>/images/arrow.jpg"  value=">"  style="width:70px">-->		
					<a  onclick="orderaddress()" style="height:14ps;margin-right: 4%;float:right" width="20ps" style="width:70px">获取微信收货地址</a>
				</div>
			</div>
			<div id="wepay" class="mobile-submit">
				<input type="hidden" name="token" value="" />
				<input type="button" class="btn btn-large btn-success" style="width:100%;" <?php if((($ismanual!=1)&&($total=='0'))||($goodstatus==1)){ ?> disabled="disabled" <? } ?> onClick="callpay()" value="提交订单"></button><br><br>
			</div>			
		</div>
		<div class="footerbar">
			<a style="width:33%;padding-top:15px" onclick="href='<?php echo $this->createMobileUrl('goodsinfo',array('gweid'=>$gweid,'goodsgid' => $goodsgid));?>'">首页</a>
			<a style="width:33%;padding-top:15px" onclick="href='<?php echo $this->createMobileUrl('myorderlist',array('gweid' => $gweid,'goodsgid' => $goodsgid)); ?>'">我的订单</a>
			<a style="width:33%;padding-top:15px" onclick="href='<?php echo $this->createMobileUrl('rightslists',array('gweid' => $gweid,'goodsgid' => $goodsgid));?>'">我的维权</a>
		</div>
		<!--<div id="footer"></div>--> 
	</body>
	<script language='javascript'>
		//共享收货地址
		function orderaddress(){
			
			//共享收货地址
			WeixinJSBridge.invoke('editAddress',{
				"appId" : '<?= $data['appId'] ?>', 
				"scope" : "jsapi_address",
				"signType" : 'sha1', //微信签名方式
				"addrSign" : '<?=$data['addrSign'] ?>',
				"timeStamp" : '<?=$data['timeStamp'] ?>', //时间戳
				"nonceStr" : '<?= $data['nonceStr'] ?>', //随机串
				},function(res){
				//若res 中所带的返回值不为空，则表示用户选择该返回值作为收货地址。否则若返回空，则表示用户取消了这一次编辑收货地址。
				if(res.err_msg == 'edit_address:ok'){	
					document.getElementById("username").value = res.userName;
					document.getElementById("telnumber").value = res.telNumber;
					document.getElementById("postalcode").value = res.addressPostalCode;
					document.getElementById("stagename").value = res.proviceFirstStageName+""+res.addressCitySecondStageName+""+res.addressCountiesThirdStageName;
					document.getElementById("detailinfo").value = res.addressDetailInfo;
				}else{
					//alert(res.err_msg);
					alert("获取收货地址失败");
				}
			});
		}
		isSubmitting = false;
		function callpay(){
			
			var goodstotal=document.getElementById("number").value;
			var price = '<?= $price?>';
			var gweid='<?= $gweid ?>';
			var goodsid='<?= $goodsid ?>';
			var ismanual='<?= $ismanual ?>';
			var isdelivery='<?= $isdelivery ?>';			
			
			if(ismanual==1){
				var totalfee = document.getElementById("goodstotalfee").value;
				if(totalfee==''){
					alert("请输入金额");
					return false;
				}
				if(parseFloat(totalfee)==0){
					alert("金额不能为0");
					return false;
				}
				if (!/^\d+[.]?\d*$/.test(totalfee)){
					alert("请填写正确的金额");
					return false;
				}
				var flfee=parseFloat(document.getElementById("goodstotalfee").value);
				if(flfee><?php echo WEPAY_MAX_TOTAL_FEE;?>){
					alert("金额超出范围，请重新输入<?php echo WEPAY_MAX_TOTAL_FEE;?>以内金额");
					return false;
				}
				if(!(/^([1-9]\d+|\d)(\.\d{1,2})?$/).test(document.getElementById("goodstotalfee").value)){
					alert("金额最多只能保留小数点后两位");
					return false;
				}	
			}else{
				var totalfee = price*document.getElementById("number").value;
			}
			
			var username=document.getElementById("username").value;
			var telnumber=document.getElementById("telnumber").value;
			var postalcode=document.getElementById("postalcode").value;
			var stagename=document.getElementById("stagename").value;
			var detailinfo=document.getElementById("detailinfo").value;
			if(isdelivery==0){
				var mobilereg = /^[0-9-]+$/; //联系电话是数字字符串或者带有横线
				var postalreg= /^[1-9][0-9]{5}$/;//邮编格式
				
				if(username==''||telnumber==''||postalcode==''||stagename==''||detailinfo==''){
					alert("请输入完整的收货地址信息");
					return false;
				}
				if(!mobilereg.test(document.getElementById('telnumber').value)){
					alert("电话格式不正确");
					return false;
				}
				if(!postalreg.test(document.getElementById('postalcode').value)){
					alert("邮编格式不正确");
					return false;
				}
			}
			var address_array=new Array();
			address_array[0]=username;
			address_array[1]=telnumber;
			address_array[2]=postalcode;
			address_array[3]=stagename;
			address_array[4]=detailinfo;
			address=eval(address_array);
			
			if(isSubmitting)
				return false;
				isSubmitting = true;
			$.ajax({
				async:false,
				url:window.location.href, 
				type: "POST",
				data:{'order_add':'isAdd','gweid':gweid,'goodsid':goodsid,'goods_price':price,'goodstotal':goodstotal,'totalfee':totalfee,'address':address},
				success: function(data){
					if (data.status == 'error'){
						alert(data.message);
					}else if (data.status == 'success'){
						window.location.href = data.url;
					}
					isSubmitting = false;
				},
				 error: function(data){
					alert("出现错误,请重试");
					isSubmitting = false;
				},
				dataType: 'json'
			});				 
		}
		function clickhere(){	
			$("#type").css("color","blue"); 
			$("#type").css("border-color","blue"); 
		}
		function totaladd(total){
			var price = '<?= number_format($price,2,".","")?>';
			var num=parseInt(document.getElementById("number").value)+1;
			var totalfee=Math.floor((price*(num)).toFixed(2));
			if(totalfee>=<?php echo WEPAY_MAX_TOTAL_FEE;?>){
				alert("已经超过微信最大支付金额，无法购买");
			}else if ((total==-1)||( parseInt(document.getElementById("number").value)<parseInt(total))){
				document.getElementById("number").value++;
				var feevalue=(price*document.getElementById("number").value).toFixed(2);
				document.getElementById("goodstotalfee").value=feevalue;
			}else{
				alert("库存不足，已达购买数量上限");
			}
		}
		function totaldel(){			
			var price = '<?= number_format($price,2,".","")?>';
			if (document.getElementById("number").value>1){
				document.getElementById("number").value--;
				var feevalue=(price*document.getElementById("number").value).toFixed(2);
				document.getElementById("goodstotalfee").value=feevalue;
			}
		}
		
	</script>
<?php  include $this -> template('footer');?>