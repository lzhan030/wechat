<script type="text/javascript">
<!--point -->
var point=<?php echo intval($point); ?>;//会员剩余point
var mid=<?php  echo intval($mid); ?>;	
<!--point END-->


<!--清空购物车-->
function clearCart(){
	if (confirm('确定要清空购物车吗？')) {
		tip("正在处理数据...");
		$.getJSON('<?php  echo $this->createMobileUrl('mycart',array('op'=>'clear','gweid' => $gweid));?>', function(s){
			$(".shopcart-item").remove();
			$("#cartempty").show();
			$("#cartfooter").hide();
			tip_close();
		});
	}
}

<!--删除购物车中某件商品-->
function removeCart(id){
	if (confirm('您确定要删除此商品吗？')) {
		tip("正在处理数据...");
		var url = "<?php  echo $this->createMobileUrl('mycart',array('op'=>'remove','gweid' => $gweid,true));?>"+ "&id=" + id;
		$.getJSON(url, function(s){
			$("#item_" + s.cartid).remove();
			if($(".shopcart-item").length<=0){
				$("#cartempty").show();
				$("#cartfooter").hide();
			}
			tip_close();
			canculate();
		});
	}
}

<!--更新购物车DB-->
function updateCart(id,num,ispoint){
	if($("#goodsnum_" + id).hasClass('ismanual'))
		var url = "<?php  echo $this->createMobileUrl('mycart',array('op'=>'updateprice'));?>"+ "&id=" + id+"&marketprice=" + num+"&ispoint=" + 0;<!--point update-->
	else
		var url = "<?php  echo $this->createMobileUrl('mycart',array('op'=>'update'));?>"+ "&id=" + id+"&num=" + num+"&ispoint=" + ispoint;<!--point update-->
	$.getJSON(url, function(s){
		canculate();
	});
}


<!--更新购买数量判断是否可以继续选择该商品的积分购买-->
function pointCheck(id,num){
	if($("#ispoint_" + id).is(':checked')) {			
		var goodspoint=$("#point_" + id).html()==''?0:parseInt($("#point_" + id).html());
		var pointall=parseInt(num)*parseInt(goodspoint);
		$("input:checkbox[name=ispoint]:checked").each(function() {
			var pointitemid=parseInt($(this).attr('id').replace(/[^0-9]/ig, ""));
			if(pointitemid!=id){
				var num = parseInt( $("#goodsnum_" + pointitemid).val() );
				var goodspoint=$("#point_" + pointitemid).html()==''?0:parseInt($("#point_" + pointitemid).html());
				pointall=parseInt(pointall)+parseInt(num)*parseInt(goodspoint);
			}		
		});
		
		if(pointall>parseInt(point)){//购物车所有的积分购买和与会员剩余积分比较
			tip("积分不足",true);
			$("#ispoint_" + id).attr("checked", false);
			$("#pointdisplay_" + id).css('display','none');
			$("#pricedisplay_" + id).css('display','block');
			 
			canculate();
			if(id!=='')
				updateCart(id,num,0);
		}
	}
}

<!--更新购买数量后"小计"的重新计算显示-->
function canculateItem(id,total){
	var pricenew = parseFloat( $("#singleprice_"+id).html() ) * total;
	$("#goodsprice_" + id).html(pricenew);
	var pointnew = parseInt( $("#point_"+id).html() ) * total;//point update
	$("#goodsprice_pointdisplay_" + id).html(pointnew);//point update
	
}

<!--增加购买数量-->
function addNum(id,maxbuy){
	var mb = maxbuy;
	var stock =$("#stock_" + id).html()==''?-1:parseInt($("#stock_" + id).html());
	if(mb>stock && stock!=-1){
		mb = stock;
	}
	var num = parseInt( $("#goodsnum_" + id).val() ) + 1;
		
	pointCheck(id,num);
	
	<!-- point -->
	if($("#ispoint_" + id).is(':checked')) {
		ispoint=1;
	}else{
		ispoint=0;
	}
	<!-- point END-->
	
	<!--point -->
	if(num>stock && stock!=-1){
		tip("最多只能购买 " + stock + " 件!",true);
		$("#goodsnum_" + id).val(stock);		
		canculateItem(id,stock);
		canculate();
		if(id!=='')
			updateCart(id,stock,ispoint);<!-- point update-->		
		return;
	}
	<!--point END-->
	
	if(num>mb && mb>0){
		tip("最多只能购买 " + mb + " 件!",true);
		$("#goodsnum_" + id).val(mb);<!--point update-->
		canculateItem(id,mb);
		canculate();
		if(id!=='')
			updateCart(id,mb,ispoint);<!-- point update-->
		return;
	}
	$("#goodsnum_" + id).val(num);
	var price = parseFloat( $("#singleprice_"+id).html() ) * num;
	if((!$("#ispoint_" + id).is(':checked')) && ( parseFloat(price)><?php echo WEPAY_MAX_TOTAL_FEE;?>)){
		alert("不能再添加数量，金额超出范围，最大不能超出<?php echo WEPAY_MAX_TOTAL_FEE;?>金额");
		$("#goodsnum_" + id).val(num - 1);
		canculateItem(id,num-1);
		canculate();
		if(id!=='')
		updateCart(id,num-1,ispoint);<!-- point update-->
		return;
	}else{
		canculateItem(id,num);
		canculate();
		updateCart(id,num,ispoint);<!-- point update-->
	}
}

<!--减少购买数量-->
function reduceNum(id){
	var num = parseInt( $("#goodsnum_" + id).val() );
	if(num-1<=0){
		return;
	}
	num--;
	$("#goodsnum_" + id).val(num);
	<!-- point -->
	if($("#ispoint_" + id).is(':checked')) {
		ispoint=1;
	}else{
		ispoint=0;
	}
	<!-- point END-->
	canculateItem(id,num);
	canculate();
	updateCart(id,num,ispoint);<!-- point update-->
}

<!--计算合计-->
function canculate(){

	var total = 0;
	var pointtotal=0;
	$(".singletotalprice").each(function(){<!--point update-->
		var id=parseInt($(this).attr('id').replace(/[^0-9]/ig, ""));		
		if($("#ispoint_" + id).is(':checked')) {
			var num = parseInt( $("#goodsnum_" + id).val() );
			var goodspoint=$("#point_" + id).html()==''?0:parseInt($("#point_" + id).html());
			pointtotal=parseInt(pointtotal)+parseInt(num)*parseInt(goodspoint);
		}else{
			total+=parseFloat( $(this).html() );
		}
		
		
	});
	
	$(".ismanual").each(function(){
		total+=parseFloat( $(this).val() );
	});
	$("#pricetotal").html(total);
	$("#pointtotal").html(pointtotal);
}
	
<!--手动更新购买数量-->
function clickchangeprice(id,maxbuy){
	var mb = maxbuy;
	var stock =$("#stock_" + id).html()==''?-1:parseInt($("#stock_" + id).html());
	if(mb>stock && stock!=-1){
		mb = stock;
	}
	var num = parseInt( $("#goodsnum_" + id).val() );
	var z= /^[0-9]*$/; //判断手动输入的是否是数字
	if(!z.test(num) || num == 0){
		$("#goodsnum_" + id).val(1);
		canculateItem(id,parseInt(1));
		canculate();//point new add
		if($("#ispoint_" + id).is(':checked')) {
			ispoint=1;
		}else{
			ispoint=0;
		}
		//需要更新cart表的信息
		updateCart(id,1,0);	//point new add	
		return;
	}
	
	pointCheck(id,num);
	
	<!-- point -->
	if($("#ispoint_" + id).is(':checked')) {
		ispoint=1;
	}else{
		ispoint=0;
	}
	<!-- point END-->
	
	<!--point -->
	if(num>stock && stock!=-1){
		tip("最多只能购买 " + stock + " 件!",true);
		$("#goodsnum_" + id).val(stock);
		canculateItem(id,parseInt(stock));
		canculate();
		//需要更新cart表的信息
		if(id!=='')
		updateCart(id,parseInt(stock),ispoint);
		return;
	}
	<!--point END-->
	
	if(num>mb && mb>0){
		tip("最多只能购买 " + mb + " 件!",true);
		$("#goodsnum_" + id).val(mb);
		canculateItem(id,parseInt(mb));
		canculate();
		//需要更新cart表的信息
		if(id!=='')
		updateCart(id,parseInt(mb),ispoint);
		return;
	}
	

	$("#goodsnum_" + id).val(num);
	var price = parseFloat( $("#singleprice_"+id).html() ) * num;
	if((!$("#ispoint_" + id).is(':checked')) && ( parseFloat(price)><?php echo WEPAY_MAX_TOTAL_FEE;?>)){
		alert("新设置的数量使得总金额超出范围，最大不能超出<?php echo WEPAY_MAX_TOTAL_FEE;?>金额");
		$("#goodsnum_" + id).val(1);
		canculateItem(id,parseInt(1));
		canculate();
		//需要更新cart表的信息
		if(id!=='')
		updateCart(id,parseInt(1),ispoint);
		return;
	}else{
		canculateItem(id,parseInt(num));
		canculate();
		updateCart(id,num,ispoint);<!-- point update-->
	}
}

//买家输入金额更新
function changeprice(id){
	var price = $("#goodsnum_"+id).val();
	if(price==''){
		alert("请输入金额");
	}
	else if(parseFloat(price)==0){
		alert("金额不能为0");
	}
	else if (!/^\d+[.]?\d*$/.test(price)){
		alert("请填写正确的金额");
	}
	else if(parseFloat(price)><?php echo WEPAY_MAX_TOTAL_FEE;?>){
		alert("金额超出范围，请重新输入<?php echo WEPAY_MAX_TOTAL_FEE;?>以内金额");
	}
	else if(!(/^([1-9]\d+|\d)(\.\d{1,2})?$/).test(parseFloat(price))){
		alert("金额最多只能保留小数点后两位");
	}else{	
		$("#goodsnum_" + id).val(price);
		canculate();
		updateCart(id,price,0);  //数量一直为1
		
	}
}
function buynow(){
    var manualtype = $("input[name='manual_price']");  
	//注意使用val()取不到值，使用value才可以取到
	var flag = true;
	if(manualtype.length > 0){
	    for(var i = 0; i < manualtype.length; i++){
			if(manualtype[i].value==''){
				alert("请输入金额");
				flag = false;
			}
			else if(parseFloat(manualtype[i].value)==0){
				alert("金额不能为0");
				flag = false;
			}
			else if (!/^\d+[.]?\d*$/.test(manualtype[i].value)){
				alert("请填写正确的金额");
				flag = false;
			}
			else if(parseFloat(manualtype[i].value)><?php echo WEPAY_MAX_TOTAL_FEE;?>){
				alert("金额超出范围，请重新输入<?php echo WEPAY_MAX_TOTAL_FEE;?>以内金额");
				flag = false;
			}
			else if(!(/^([1-9]\d+|\d)(\.\d{1,2})?$/).test(parseFloat(manualtype[i].value))){
				alert("金额最多只能保留小数点后两位");
				flag = false;
			}
		} 
	}
	//取总额比较
	if(flag){
		if(parseFloat($("#pricetotal").text())><?php echo WEPAY_MAX_TOTAL_FEE;?>){
			alert("金额超出范围，请重新输入金额或者减少所买件数，控制总金额在<?php echo WEPAY_MAX_TOTAL_FEE;?>以内");
			flag = false;
		} 
	}
	<!--point-->
	if(flag){//结算时验证积分
		var pointall=0;
		$("input:checkbox[name=ispoint]:checked").each(function() {
			var pointitemid=parseInt($(this).attr('id').replace(/[^0-9]/ig, ""));
			var num = parseInt( $("#goodsnum_" + pointitemid).val() );
			var goodspoint=$("#point_" + pointitemid).html()==''?0:parseInt($("#point_" + pointitemid).html());
			pointall=parseInt(pointall)+parseInt(num)*parseInt(goodspoint);
		
		});
		
		if(pointall>parseInt(point)){//购物车所有的积分购买和与会员剩余积分比较
			alert("您现有积分为"+parseInt(point)+",积分不足,无法购买");
			flag = false;
		}
	}
	<!--point END-->
	if(flag){
		location.href = '<?php  echo $this->createMobileUrl('confirm', array('gweid' => $gweid))?>';
	}
}
<!--point -->
function checkpoint(id){
	if($("#ispoint_" + id).is(':checked')) {
		$("#pointdisplay_" + id).css('display','block');
		$("#pricedisplay_" + id).css('display','none');
		//获取选中状态的checkbox
		var pointall=0;
		$("input:checkbox[name=ispoint]:checked").each(function() {
			var pointitemid=parseInt($(this).attr('id').replace(/[^0-9]/ig, ""));
			var num = parseInt( $("#goodsnum_" + pointitemid).val() );
			var goodspoint=$("#point_" + pointitemid).html()==''?0:parseInt($("#point_" + pointitemid).html());
			pointall=parseInt(pointall)+parseInt(num)*parseInt(goodspoint);
		
		});
		
		if(pointall>parseInt(point)){//购物车所有的积分购买和与会员剩余积分比较
			tip("积分不足",true);
			$("#ispoint_" + id).attr("checked", false);
			$("#pointdisplay_" + id).css('display','none');
			$("#pricedisplay_" + id).css('display','block');
		}else{
			var num = parseInt( $("#goodsnum_" + id).val() );
			var ispoint=1
			canculate();
			updateCart(id,num,ispoint);
		}
	}else{
		var num = parseInt( $("#goodsnum_" + id).val() );
		var ispoint=0;
		$("#pointdisplay_" + id).css('display','none');
		$("#pricedisplay_" + id).css('display','block');
		canculate();
		updateCart(id,num,ispoint);	
	}
	
}
function checkMaxBuy(id, maxbuy){
	
}
<!--point END-->
</script>