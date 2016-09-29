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
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/rollbox.css" />
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/jquery-1.11.1.min.js"></script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/bootstrap.js"></script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/jquery.lazyload.js"></script>
		<title>项目详情</title>		
	</head>
	<body>
		<div class="head">
			<span class="title">项目详情</span>
		</div>
		<div class="research" style="margin-top:46px;">
			<div class="mobile-div img-rounded">
				<div id="goodstitle" class="mobile-hd"><?php echo $goodsarray[$disgoodsid]['title']; ?></div>
				<div class="mobile-content" id="goodimg">
					<?php if(!empty($goodsarray[$disgoodsid]['thumb'])) {?>
						<img id="goodsthumb" class="research-thumb" src="<?php echo $goodsarray[$disgoodsid]['thumb']; ?>">
					<?php } else {?>
						<div class="thumbnail" style="height:200px;"><p>没有图片</p></div>
					<?php }?>
				</div>
			</div>
			<!--slide begin-->		
			<div class="mobile-div img-rounded" style="border:0px;margin-top:-9px;margin-bottom:-21px;<?php if(count($goodsarray)<=1){?> display:none <?php }?>">	
				<div class="rollBox">
					<div class="LeftBotton" style="background:url(<?php bloginfo('template_directory'); ?>/images/slide.gif) no-repeat 11px 0;" onmousedown="ISL_GoUp()" onmouseup="ISL_StopUp()" onmouseout="ISL_StopUp()"></div>
					<div class="Cont" id="ISL_Cont">
						<div class="ScrCont">
							<div id="List1">
						   <!-- 图片列表 begin-->
							<?php
								foreach($goodsarray as $k=>$val){
							?>
								<div class="pic">
									<?php if(!empty($val['thumb'])) {?>
										<img onclick="goodsinf('<?php echo $val['id'];?>')" src="<?php echo $val['thumb']; ?>" width="109" height="109" />
									<?php } else {?>
										<div onclick="goodsinf('<?php echo $val['id'];?>')" class="thumbnail" style="height:109px;width:109px;margin-bottom:0px;"><p>没有图片</p></div>
									<?php }?>
									<p><?php echo $val['title'];?></p>
								</div>
							<!-- 图片列表 end -->
							<?php }?>
							</div>
							<div id="List2"></div>
						</div>
					</div>
					<div class="RightBotton" style="background:url(<?php bloginfo('template_directory'); ?>/images/slide.gif) no-repeat -8px 0;" onmousedown="ISL_GoDown()" onmouseup="ISL_StopDown()" onmouseout="ISL_StopDown()"></div>
				</div>
			</div>
			<!--slide end-->
			<div class="mobile-div img-rounded">
				<div class="mobile-hd">详细描述</div>
				<div style="background-color:#fff;word-break:break-all;" id="description" class="mobile-content">
					<pre id="gdescription" style="padding-left:0px;padding-top:0px;background-color:#fff;border:none;font:14px/1.5 'Microsoft Yahei','Simsun'" class="mobile-content"><?php echo $goodsarray[$disgoodsid]['description']; ?></pre>
				</div>
			</div>	
			<form id="goodsform" action="" id="research" method="post" enctype="multipart/form-data" onsubmit="return checkinputinfo();">
				<div class="mobile-div img-rounded">
					<div class="mobile-hd">其他信息</div>
					<div class="mobile-content" style="color:#555555;">
						<table class="form-table">
							<tr align="center">
								<th><label for="goodsid">编号</label></th>
								<td><input id="goodsid" style="border-style:none;text-align: center;background-color:#fff" disabled="disabled" value="<?php echo $disgoodsid; ?>"></input></td>																																																																		</td>
							</tr>
							<tr  align="center">
								<th><label for="title">名称</label></th>
								<td><input id="title" style="border-style:none;text-align: center;background-color:#fff" disabled="disabled" value="<?php echo $goodsarray[$disgoodsid]['title']; ?>"></input></td>																																																																		</td>
							</tr>
							<tr   align="center" id='mprice'>
								<th><label for="market_price" >金额(元)</label></th>
								<td><!--ismanual=1表示手动输入金额-->
									<input id="manual_price" name="manual_price"  style="<?php if($goodsarray[$disgoodsid]['ismanual']!='1'){ ?> display:none; <?php } ?> width:90px;text-align: center;background-color:#fff"  value=""></input>
									<input id="market_price" name="market_price" style="<?php if($goodsarray[$disgoodsid]['ismanual']=='1'){ ?> display:none; <?php } ?> border-style:none;text-align: center;background-color:#fff" disabled="disabled" value="<?php echo number_format($goodsarray[$disgoodsid]['market_price'],2,".","");?>"></input>
								</td>																																																																		</td>
							</tr>
							<tr  align="center" id='vprice' style="<?php if($goodsarray[$disgoodsid]['isvipprice']=='0'){ echo 'display:none'; }?>">
								<th><label for="vip_price">会员价格(元)</label></th>
								<td><input id="vip_price" style="border-style:none;text-align: center;background-color:#fff;" disabled="disabled" value="<?php echo number_format($goodsarray[$disgoodsid]['vip_price'],2,".","");?>"></input></td>																																																																		</td>
							</tr>
						</table>
					</div>
				</div>
				<div id="wepay" class="mobile-submit">
					<input id="forgoodsid" type="hidden" name="forgoodsid" value="<?php echo $disgoodsid; ?>" />
					<input type="submit" class="btn btn-large btn-success" style="width:100%;" value="立即支付"><br><br>
				</div>		
			</form>
		</div>
		<div class="footerbar">
			<a style="width:33%;padding-top:15px" onclick="href='<?php echo $this->createMobileUrl('goodsinfo',array('gweid'=>$gweid,'goodsgid' => $goodsgid));?>'">首页</a>
			<a style="width:33%;padding-top:15px" onclick="href='<?php echo $this->createMobileUrl('myorderlist',array('gweid' => $gweid,'goodsgid' => $goodsgid)); ?>'">我的订单</a>
			<a style="width:33%;padding-top:15px" onclick="href='<?php echo $this->createMobileUrl('rightslists',array('gweid' => $gweid,'goodsgid' => $goodsgid));?>'">我的维权</a>
		</div>
		<!--<div id="footer"></div>--> 
		
	<script language="javascript" type="text/javascript">
		
		<!--//--><![CDATA[//><!--
		//图片滚动列表 mengjia 070816
		var Speed = 1; //速度(毫秒)
		var Space = 5; //每次移动(px)
		//var PageWidth = 528; //翻页宽度
		//var PageWidth = 200; //翻页宽度
		var PageWidth = 117; //翻页宽度
		var fill = 0; //整体移位
		var MoveLock = false;
		var MoveTimeObj;
		var Comp = 0;
		var AutoPlayObj = null;
		GetObj("List2").innerHTML = GetObj("List1").innerHTML;
		GetObj('ISL_Cont').scrollLeft = fill;
		GetObj("ISL_Cont").onmouseover = function(){clearInterval(AutoPlayObj);}
		GetObj("ISL_Cont").onmouseout = function(){AutoPlay();}
		AutoPlay();
		function GetObj(objName){if(document.getElementById){return eval('document.getElementById("'+objName+'")')}else{return eval('document.all.'+objName)}}
		function AutoPlay(){ //自动滚动
		 clearInterval(AutoPlayObj);
		 AutoPlayObj = setInterval('ISL_GoDown();ISL_StopDown();',3000); //间隔时间
		}
		function ISL_GoUp(){ //上翻开始
		 if(MoveLock) return;
		 clearInterval(AutoPlayObj);
		 MoveLock = true;
		 MoveTimeObj = setInterval('ISL_ScrUp();',Speed);
		}
		function ISL_StopUp(){ //上翻停止
		 clearInterval(MoveTimeObj);
		 if(GetObj('ISL_Cont').scrollLeft % PageWidth - fill != 0){
		  Comp = fill - (GetObj('ISL_Cont').scrollLeft % PageWidth);
		  CompScr();
		 }else{
		  MoveLock = false;
		 }
		 AutoPlay();
		}
		function ISL_ScrUp(){ //上翻动作
		 if(GetObj('ISL_Cont').scrollLeft <= 0){GetObj('ISL_Cont').scrollLeft = GetObj('ISL_Cont').scrollLeft + GetObj('List1').offsetWidth}
		 GetObj('ISL_Cont').scrollLeft -= Space ;
		}
		function ISL_GoDown(){ //下翻
		 clearInterval(MoveTimeObj);
		 if(MoveLock) return;
		 clearInterval(AutoPlayObj);
		 MoveLock = true;
		 ISL_ScrDown();
		 MoveTimeObj = setInterval('ISL_ScrDown()',Speed);
		}
		function ISL_StopDown(){ //下翻停止
		 clearInterval(MoveTimeObj);
		 if(GetObj('ISL_Cont').scrollLeft % PageWidth - fill != 0 ){
		  Comp = PageWidth - GetObj('ISL_Cont').scrollLeft % PageWidth + fill;
		  CompScr();
		 }else{
		  MoveLock = false;
		 }
		 AutoPlay();
		}
		function ISL_ScrDown(){ //下翻动作
		 if(GetObj('ISL_Cont').scrollLeft >= GetObj('List1').scrollWidth){GetObj('ISL_Cont').scrollLeft = GetObj('ISL_Cont').scrollLeft - GetObj('List1').scrollWidth;}
		 GetObj('ISL_Cont').scrollLeft += Space ;
		}
		function CompScr(){
		 var num;
		 if(Comp == 0){MoveLock = false;return;}
		 if(Comp < 0){ //上翻
		  if(Comp < -Space){
		   Comp += Space;
		   num = Space;
		  }else{
		   num = -Comp;
		   Comp = 0;
		  }
		  GetObj('ISL_Cont').scrollLeft -= num;
		  setTimeout('CompScr()',Speed);
		 }else{ //下翻
		  if(Comp > Space){
		   Comp -= Space;
		   num = Space;
		  }else{
		   num = Comp;
		   Comp = 0;
		  }
		  GetObj('ISL_Cont').scrollLeft += num;
		  setTimeout('CompScr()',Speed);
		 }
		}
		//--><!]]>
		
		function goodsinf(goodsid){
			var obj=eval(<?php echo json_encode($goodsarray);?>);
			document.getElementById("title").value = obj[goodsid]['title'];
			
			
			if(obj[goodsid]['ismanual']=='1'){
				document.getElementById("market_price").style.display = "none";
				document.getElementById("manual_price").style.display = "";
			}else{
				document.getElementById("market_price").style.display = "";
				document.getElementById("manual_price").style.display = "none";
				document.getElementById("market_price").value = parseFloat(obj[goodsid]['market_price']).toFixed(2);
			}
			
			if(parseFloat(obj[goodsid]['isvipprice'])=='1'){
				document.getElementById("vprice").style.display = "";
				document.getElementById("vip_price").value = parseFloat(obj[goodsid]['vip_price']).toFixed(2);
							
			}else{
				//隐藏tr
				document.getElementById("vprice").style.display = "none";
			}
			
			document.getElementById("goodsid").value = goodsid;
			document.getElementById("gdescription").innerText = obj[goodsid]['description'];
			if(obj[goodsid]['thumb']=="") {
				$("#goodimg").html("<div class='thumbnail' style='height:200px;'><p>没有图片</p></div>");
			} else {
				$("#goodimg").html("<img id='goodsthumb' class='research-thumb' src='"+obj[goodsid]['thumb']+"' />");
			}
			document.getElementById("forgoodsid").value = goodsid;
			document.getElementById("goodstitle").innerText = obj[goodsid]['title'];
						
			
		}
		
		function checkinputinfo(){
			if(document.getElementById("manual_price").style.display != "none"){
				var flfee=parseFloat(document.getElementById("manual_price").value);
				if(document.getElementById("manual_price").value==""){
					alert("请输入金额");
					return false;
				}
				if(parseFloat(document.getElementById("manual_price").value)==0){
					alert("金额不能等于0");
					return false;
				}
				if (!/^\d+[.]?\d*$/.test(document.getElementById("manual_price").value)){
					alert("请填写正确的金额");
					return false;
				}
				if(flfee><?php echo WEPAY_MAX_TOTAL_FEE;?>){
					alert("金额超出范围，请重新输入<?php echo WEPAY_MAX_TOTAL_FEE;?>以内金额");
					return false;
				}
				if(!(/^([1-9]\d+|\d)(\.\d{1,2})?$/).test(document.getElementById("manual_price").value)){
					alert("金额最多只能保留小数点后两位");
					return false;
				}
			}
			return true;
		}
	</script>	 
<?php  include $this -> template('footer');?>