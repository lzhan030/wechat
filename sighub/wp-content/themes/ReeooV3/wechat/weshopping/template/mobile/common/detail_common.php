
<script>
	var options=<?php  echo json_encode($options)?>;
	var specs=<?php  echo json_encode($specs)?>;
	var hasoption = <?php echo $goods['hasoption']=='1'?'true':'false'?>;
	<!--point -->
	var point=<?php echo intval($point); ?>;//会员剩余point
	var goodspoint=<?php echo intval($goods['point']); ?>;//单个商品所需point
	var mid=<?php  echo intval($mid); ?>;	
	<!--point END-->
	$(function() {
		 $('.other-detail .detail-group:last').css("border-bottom", "0");
		
		if (proimg_count > 0) {
			(function(window, $, PhotoSwipe) {
				$('.touchslider-viewport .list a[rel]').photoSwipe({});
			}(window, window.jQuery, window.Code.PhotoSwipe));

			$('.touchslider').touchSlider({
				mouseTouch: true,
				autoplay: true,
				delay: 2000
			});
		}
		 $(".option,.optionimg").click(function() {
			 var specid = $(this).attr("specid");
			 var oid = $(this).attr("oid");
			$(".optionid_"+specid).val(oid);
			$(".options_" + specid + "  span").removeClass("current").attr("sel", "false");
			$(this).addClass("current").attr("sel", "true");
			
			$("#total").val("1");  //每次切换的时候，都要重置购买的数量为1，否则会出现数量多于当前的剩余
			<!--point -->
			$('#ispoint').attr("checked", false);//每次切换的时候，checkbox默认都是不选中
			<!--point END-->			
			var optionid = "";
			var stock =0;
			var marketprice = 0;
			var productprice = 0;
			 var ret = option_selected();
  
			if(ret.no==''){
				var len = options.length;
				for(var i=0;i<len;i++) {
					var o = options[i];
				  
					var ids = ret.all.join("_");
				   
					if( o.specs==ids){
						optionid = o.id;
						stock = o.stock;
						marketprice = o.marketprice;
						productprice = o.productprice;
						<!--point -->
						point_option = o.point;
						<!--point END-->
						break;
					}
					
				}
			   $("#optionid").val(optionid); 
			   
				if(stock!="-1"){
					 $("#stockcontainer").html("( 剩余 <span id='stock'>" + stock + "</span> )");
				}
				else{
					$("#stockcontainer").html("<span id='stock'></span>");
				}
				$("#marketprice").html(marketprice);
				
				 
				$("#productprice").html(productprice);
				if(productprice<=0){
					$("#productpricecontainer").hide();
				}
				else{
					$("#productpricecontainer").show();
				}
				<!--point -->
				if(mid==0 || point_option=='' || point_option==0 || point_option=='0' || typeof(point_option)=="undefined"){
					$(".pointdiv").hide();
				}else{
					$(".pointdiv").show();
					$("#point").html(point_option);
				}
				<!--point END-->	
			}
		});
		
		$("#total").blur(function(){
			if(!$(this).hasClass('ismanual')){
				var total = $("#total");
				if(!total.isInt()){
					total.val("1");
				}
				var stock = $("#stock").html()==''?-1:parseInt($("#stock").html());
				var mb = maxbuy;
				if(mb>stock && stock!=-1){
					mb = stock;
				}
				var num = parseInt(total.val() );
				if(num>stock && stock!=-1){
					tip("您最多可购买 " + stock + " 件!",true);
					total.val(stock);
				}
				
				if(num>mb && mb>0){
					tip("您最多可购买 " + mb + " 件!",true);
					total.val(mb);
				}
				
				<!--point -->
				if($('#ispoint').is(':checked')) {
					var istotal=$("#total").val();
					var goodspoint=$("#point").html()==''?0:parseInt($("#point").html());
					if(parseInt(istotal)*parseInt(goodspoint)>parseInt(point)){
						tip("积分不足",true);
						$('#ispoint').attr("checked", false);
					}
				}
				<!--point END-->
				
				
			}else{
				var total = $("#total");
				if(total.val() == ''){
					tip("请输入金额");
					total.val(1);
					return false;
				}
				if (!/^\d+[.]?\d*$/.test(total.val())){
					tip("请填写正确的金额");
					if(isNaN(parseFloat(total.val())))
						total.val('1');
					else
						total.val(parseFloat(total.val()));
					return false;
				}
				if(parseFloat(total.val())==0){
					tip("金额不能为0");
					total.val('1');
					return false;
				}

				total.val(parseFloat(total.val()));
				if(total.val()>15000){
					tip("金额超出范围");
					total.val('15000');
					return false;
				}
				if(!(/^([1-9]\d+|\d)(\.\d{1,2})?$/).test(total.val())){
					tip("金额最多只能保留小数点后两位");
					total.val(parseFloat(total.val()));
					return false;
				}
			}

		})
		
});
var maxbuy = <?php echo empty($goods['maxbuy'])?"0":$goods['maxbuy']?>;
function addNum(){
	var total = $("#total");
	if(!total.isInt()){
		total.val("1");
	}
	
	var stock = $("#stock").html()==''?-1:parseInt($("#stock").html());
	var mb = maxbuy;
	
	if(mb>stock && stock!=-1){
		mb = stock;
	}
	var num = parseInt(total.val()) + 1;
	if(num>stock && stock!=-1){
		tip("您最多可购买 " + stock + " 件!",true);
		num--;
	}
	
	if(num>mb && mb>0){
		tip("您最多可购买 " + mb + " 件!",true);
		num = mb;
	}
	
	<!--point -->
	if($('#ispoint').is(':checked')) {
		var goodspoint=$("#point").html()==''?0:parseInt($("#point").html());
		if(parseInt(num)*parseInt(goodspoint)>parseInt(point)){
			tip("积分不足",true);
			$('#ispoint').attr("checked", false);
		}
	}
	<!--point END-->
	
	total.val(num);
}
function reduceNum(){
	var total = $("#total");
	if(!total.isInt()){
		total.val("1");
	}
	var num = parseInt( total.val() );
	if(num-1<=0){
		return;
	}
	num--;
	total.val(num);
}
function addmanualnum(){
	var total = $("#total");
	var num = parseInt( $("#total").val() );
	var z= /^[0-9]*$/; //判断手动输入的是否是数字
	if(!z.test(num) || num == 0){
		$("#total").val(1);
		return;
	}
	if(!total.isInt()){
		total.val("1");
		num=1;
	}
	
	var stock = $("#stock").html()==''?-1:parseInt($("#stock").html());
	var mb = maxbuy;
	
	if(mb>stock && stock!=-1){
		mb = stock;
	}
	
	if(num>stock && stock!=-1){
		tip("您最多可购买 " + stock + " 件!",true);
		num = stock;
	}
	
	
	if(num>mb && mb>0){
		tip("您最多可购买 " + mb + " 件!",true);
		num = mb;
	}
	
	<!--point -->
	if($('#ispoint').is(':checked')) {
		var istotal=num;
		var goodspoint=$("#point").html()==''?0:parseInt($("#point").html());
		if(parseInt(istotal)*parseInt(goodspoint)>parseInt(point)){
			tip("积分不足",true);
			$('#ispoint').attr("checked", false);
		}
	}
	<!--point END-->
	
	total.val(num);
}

function addtocart(){
	<?php if($goods['ismanual']=='1'){ ?>
		var total = $("#total");
		if(total.val() == ''){
			tip("请输入金额");
			total.val(1);
			return false;
		}
		if (!/^\d+[.]?\d*$/.test(total.val())){
			tip("请填写正确的金额");
			if(isNaN(parseFloat(total.val())))
				total.val('1');
			else
				total.val(parseFloat(total.val()));
			return false;
		}
		if(parseFloat(total.val())==0){
			tip("金额不能为0");
			total.val('1');
			return false;
		}
		
		total.val(parseFloat(total.val()));
		if(total.val()><?php echo WEPAY_MAX_TOTAL_FEE;?>){
			tip("金额超出范围，请重新输入<?php echo WEPAY_MAX_TOTAL_FEE;?>以内金额");
			return false;
		}
		
		if(!(/^([1-9]\d+|\d)(\.\d{1,2})?$/).test(total.val())){
			tip("金额最多只能保留小数点后两位");
			total.val(parseFloat(total.val()));
			return false;
		}			

	<?php }else{?>
	    var price = $('#marketprice').text();
		var totalprice = parseFloat(price) * parseFloat($("#total").val());
		<!--point -->
		if(!$('#ispoint').is(':checked')) {
		<!--point END-->
			if(totalprice><?php echo WEPAY_MAX_TOTAL_FEE;?>){
				alert("金额超出范围，请减少所买件数，控制总金额在<?php echo WEPAY_MAX_TOTAL_FEE;?>以内");
				$("#total").val("1");  //将输入的超出件数重置为1
				return false;
			}
		}
	<?php }?>
	var ret = option_selected();
	if(ret.no!=''){
		tip("请选择" + ret.no + "!",true);
		return;
	}
	tip("正在处理数据...");
	var total = $("#total").val();
	var stock = parseInt($('#stock').text());
	if(stock == 0){
		tip('库存不足，无法购买。',true);
		return;
	}
	
	<!--point 判断是否使用积分-->
	if($('#ispoint').is(':checked')) {
		var pointcheck=1;
	}else{
		var pointcheck=0;//不使用积分
	}
	<!--point END-->
	<?php if($goods['ismanual']=='1'){ ?>
		var url = "<?php  echo $this->createMobileUrl('mycart',array('op'=>'add','id'=>$goods['id'], 'gweid' => $gweid),true);?>" +"&optionid=" + $("#optionid").val() + "&total=1&manual_price=" + $('#total').val()+ "&pointcheck=" + 0;<!--point-->
	<?php }else{?>
		var url = "<?php  echo $this->createMobileUrl('mycart',array('op'=>'add','id'=>$goods['id'], 'gweid' => $gweid),true);?>" +"&optionid=" + $("#optionid").val() + "&total=" + total+ "&pointcheck=" + pointcheck;<!--point-->
	<?php }?>
	$.getJSON(url, function(s){
		if(s.result==0){
			tip("只能购买 " + s.maxbuy + " 件!");
		}else{
			tip_close();tip("已加入购物车!");
			$('#carttotal').css({'width':'50px', 'height':'50px', 'line-height':'50px'}).html(s.total).animate({'width':'20px', 'height':'20px', 'line-height':'20px'}, 'slow');
		}
	});
}
function buy(){
    <?php if($goods['ismanual']=='1'){ ?>
	var total = $("#total");
	if(total.val() == ''){
		tip("请输入金额");
		total.val(1);
		return false;
	}
	if (!/^\d+[.]?\d*$/.test(total.val())){
		tip("请填写正确的金额");
		if(isNaN(parseFloat(total.val())))
			total.val('1');
		else
			total.val(parseFloat(total.val()));
		return false;
	}
	if(parseFloat(total.val())==0){
		tip("金额不能为0");
		total.val('1');
		return false;
	}

	total.val(parseFloat(total.val()));
	if(total.val()><?php echo WEPAY_MAX_TOTAL_FEE;?>){
		tip("金额超出范围，请重新输入<?php echo WEPAY_MAX_TOTAL_FEE;?>以内金额");
		return false;
	}
	if(!(/^([1-9]\d+|\d)(\.\d{1,2})?$/).test(total.val())){
		tip("金额最多只能保留小数点后两位");
		total.val(parseFloat(total.val()));
		return false;
	}	
	<?php }else{?>
	    var price = $('#marketprice').text();
		var totalprice = parseFloat(price) * parseFloat($("#total").val());
		<!--point -->
		if(!$('#ispoint').is(':checked')) {
		<!--point END-->
			if(totalprice><?php echo WEPAY_MAX_TOTAL_FEE;?>){
				alert("金额超出范围，请减少所买件数，控制总金额在<?php echo WEPAY_MAX_TOTAL_FEE;?>以内");
				$("#total").val("1");  //将输入的超出件数重置为1
				return false;
			}
		}
		
	<?php }?>
		var ret = option_selected();
		if(ret.no!=''){
		   tip("请选择" + ret.no + "!",true);
			return;
		}
		var stock = parseInt($('#stock').text());
		if(stock == 0){
			tip('库存不足，无法购买。',true);
			return;
		}
	 var total = $("#total").val();
	<!--point 判断是否使用积分-->
	if($('#ispoint').is(':checked')) {
		var pointcheck=1;
	}else{
		var pointcheck=0;//不使用积分
	}
	<!--point END-->
	<?php if($goods['ismanual']=='1'){ ?>
		location.href = "<?php echo $this->createMobileUrl('confirm',array('id'=>$goods['id'], 'gweid' => $gweid, 'ismanual' => 1),true)?>"+"&optionid=" + $("#optionid").val() + "&total=1" + "&manual_price=" + $('#total').val()+ "&pointcheck=" + 0;<!--point-->
	<?php }else{?>
		location.href = "<?php echo $this->createMobileUrl('confirm',array('id'=>$goods['id'], 'gweid' => $gweid, 'ismanual' => 0),true)?>"+"&optionid=" + $("#optionid").val() + "&total=" + total+ "&pointcheck=" + pointcheck;<!--point-->
	<?php }?>
}
var selected = [];
function option_selected(){
	var ret= {
		no: "",
		all: []
	};
	if(!hasoption){
		return ret;
	}
			$(".optionid").each(function(){
				ret.all.push($(this).val());
				if($(this).val()==''){
					ret.no = $(this).attr("title");
					return false;
				}
	})
	return ret;
}
<!--point -->
$("#ispoint").click(function(){
	if($('#ispoint').is(':checked')) {//选中动作触发
		var total=$("#total").val();
		var goodspoint=$("#point").html()==''?0:parseInt($("#point").html());
		if(parseInt(total)*parseInt(goodspoint)>parseInt(point)){
			alert('积分不足');
			$(this).attr("checked", false);
		}
	}	
});
<!--point END-->

</script>

