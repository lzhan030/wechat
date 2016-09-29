
<script language='javascript'>
	<!--point -->
	var point=<?php echo intval($point); ?>;//会员剩余point
	var mid=<?php  echo intval($mid); ?>;	
	<!--point END-->
    //初始化地区选择下拉列表
    $(function(){
    	cascdeInit('','','','<?php bloginfo('template_directory'); ?>/wechat/weshopping/js/app/Area.xml');
    });
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
				tip("获取收货地址失败", true);
			}
		});
	}
	function changeAddress(){
		location.href = '<?php  echo $this->createMobileUrl('address', array('from'=>'confirm','returnurl'=>urlencode($returnurl)))?>'
	}
	function check(){
		if((".address_item").length<=0){
			tip("请添加收货地址!", true);
			return false;
		}
		return true;
	}
	$("#dispatch").change(canculate);
	
	<!--计算合计-->
	function canculate(){
		var price = 0;
		var pointall = 0;//point update
		$(".goodsprice").each(function(){
			if($(this).children('input').length){
				price+=parseFloat($(this).children('input').val());
			}else{
			    price+=parseFloat($(this).html());
			}
		});
		//计算checkbox选中的积分和point update
		$("input:checkbox[name=ispoint]:checked").each(function() {
			if($(this).attr('id').replace(/[^0-9]/ig, "")!==''){
				var pointitemid=parseInt($(this).attr('id').replace(/[^0-9]/ig, ""));
			}else{
				var pointitemid='';//从非购物车跳转，parseInt后变成NaN错误，赋值为空
			}
			var num = parseInt( $("#goodsnum_" + pointitemid).val() );
			var goodspoint=$("#point_" + pointitemid).html()==''?0:parseInt($("#point_" + pointitemid).html());
			pointall=parseInt(pointall)+parseInt(num)*parseInt(goodspoint);
			//如果checkbox采用了积分,则合计金额相应减少
			
			var redprice=$("#goodsprice_" + pointitemid).html()==''?parseFloat(0):parseFloat($("#goodsprice_" + pointitemid).html());
			price=price-redprice;
		});
		
		
		//计算邮费产生的积分 point update
		var dispid= parseInt($("#dispatch").find("option:selected").attr("dispid"));
		if(dispid==-2){
			var dispathpoint=parseInt($("#dispatch").find("option:selected").attr("price"));
		}else{
			var dispatchprice = parseFloat($("#dispatch").find("option:selected").attr("price"));
		}
		
		//point update
		if(dispatchprice>0){
			$("#totalprice").html(price + dispatchprice + " 元 (含运费"+dispatchprice + ")");
			$("#pointtotal").html(pointall+ " 分");
		}else if(dispathpoint>0){
			$("#totalprice").html(price + " 元");
			$("#pointtotal").html(pointall+dispathpoint+" 分 (含运费积分"+dispathpoint + ")");
		}
		else{
			$("#totalprice").html(price + " 元");
			$("#pointtotal").html(pointall+ " 分");//point update
		}
	}
	<!--更新购买数量判断是否可以继续选择该商品的积分购买-->
	function pointCheck(id,num){
		if($("#ispoint_" + id).is(':checked')) {			
			var goodspoint=$("#point_" + id).html()==''?0:parseInt($("#point_" + id).html());
			var pointall=parseInt(num)*parseInt(goodspoint);
			$("input:checkbox[name=ispoint]:checked").each(function() {
				if($(this).attr('id').replace(/[^0-9]/ig, "")!==''){
					var pointitemid=parseInt($(this).attr('id').replace(/[^0-9]/ig, ""));
				}else{
					var pointitemid='';//从非购物车跳转，parseInt后变成NaN错误，赋值为空
				}
				if(pointitemid!=id){
					var num = parseInt( $("#goodsnum_" + pointitemid).val() );
					var goodspoint=$("#point_" + pointitemid).html()==''?0:parseInt($("#point_" + pointitemid).html());
					pointall=parseInt(pointall)+parseInt(num)*parseInt(goodspoint);
				}		
			});
			
			if(pointall>parseInt(point)){//购物车所有的积分购买和与会员剩余积分比较
				tip("积分不足",true);
				$("#ispoint_" + id).attr("checked", false);
				$("#goodsprice_point_" + id).css('display','none');
				$("#goodsprice_" + id).css('display','block');
				if(id!=='')
					updateCart(id,num,0);
				<?php if($needdispatch){ ?>
				if(id == '')
					update_dispatch($('#sel-provance').val());
				<?php } ?>
				canculate();
			}
		}
		<!--point END-->

	}
	
	<!--更新购买数量后"小计"的重新计算显示-->
	function canculateItem(id,total){
		var pricenew = parseFloat($("#singleprice_"+id).val()) * parseInt(total);
		$("#goodsprice_" + id).html(pricenew+" 元");
		var pointnew = parseInt($("#point_"+id).html()) * parseInt(total);//point update
		$("#goodsprice_point_" + id).html(pointnew+" 分");//point update
		
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
			canculateItem(id,parseInt(stock));
			if(id!=='')
				updateCart(id,stock,ispoint);
			<?php if($needdispatch){ ?>
			if(id == '')
				update_dispatch($('#sel-provance').val());
			<?php } ?>
			canculate();
			return;
		}
		<!--point END-->
		
		
		
		if(num>mb && mb>0){
			tip("最多只能购买 " + mb + " 件!", true);
			$("#goodsnum_" + id).val(mb);<!--point update-->
			canculateItem(id,parseInt(mb));
			if(id!=='')
				updateCart(id,mb,ispoint);
			<?php if($needdispatch){ ?>
			if(id == '')
				update_dispatch($('#sel-provance').val());
			<?php } ?>
			canculate();
			return;
		}
		
		
		$("#goodsnum_" + id).val(num);
		canculateItem(id,parseInt(num));
		if(id!=='')
			updateCart(id,num,ispoint);<!-- point update-->
		<?php if($needdispatch){ ?>
		if(id == '')
			update_dispatch($('#sel-provance').val());
		<?php } ?>
		canculate();
	}
	
	
	<!--减少购买数量-->
	function reduceNum(id){
		var num = parseInt( $("#goodsnum_" + id).val() );
		if(num-1<=0){
			return;
		}
		num--;
		$("#goodsnum_" + id).val(num);
		canculateItem(id,parseInt(num));
		<!-- point -->
		if($("#ispoint_" + id).is(':checked')) {
			ispoint=1;
		}else{
			ispoint=0;
		}
		<!-- point END-->
		if(id!=='')
			updateCart(id,num,ispoint);<!-- point update-->
		<?php if($needdispatch){ ?>
		if(id == '')
			update_dispatch($('#sel-provance').val());
		<?php } ?>
		canculate();
		
	}
	
	
	<!--手动更新购买数量-->
	function clickchangeprice(id,maxbuy){
		var z= /^[0-9]*$/; //判断手动输入的是否是数字
		var mb = maxbuy;
		var stock =$("#stock_" + id).html()==''?-1:parseInt($("#stock_" + id).html());
				if(mb>stock && stock!=-1){
					mb = stock;
				}
		var num = parseInt( $("#goodsnum_" + id).val() );
		if(!z.test(num) || num == 0){
		    $("#goodsnum_" + id).val(1);
			canculateItem(id,parseInt(1));
			//需要更新cart表的信息
			if($("#ispoint_" + id).is(':checked')) {
				ispoint=1;
			}else{
				ispoint=0;
			}
			if(id!=='')
			updateCart(id,1,ispoint);
			<?php if($needdispatch){ ?>
			if(id == '')
				update_dispatch($('#sel-provance').val());
			<?php } ?>
			canculate();
			
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
			//需要更新cart表的信息
			if(id!=='')
			updateCart(id,parseInt(stock),ispoint);
			<?php if($needdispatch){ ?>
			if(id == '')
				update_dispatch($('#sel-provance').val());
			<?php } ?>
			canculate();
			
			return;
		}
		<!--point END-->
		
	
		if(num>mb && mb>0){		
			tip("最多只能购买 " + mb + " 件!", true);	
			$("#goodsnum_" + id).val(mb);
			canculateItem(id,parseInt(mb));						
			//需要更新cart表的信息
			if(id!=='')
			updateCart(id,parseInt(mb),ispoint);
			<?php if($needdispatch){ ?>
			if(id == '')
				update_dispatch($('#sel-provance').val());
			<?php } ?>
			canculate();
			
			return;
		}
		$("#goodsnum_" + id).val(num);
		canculateItem(id,parseInt(num));		
		
		if(id!=='')
			updateCart(id,num,ispoint);<!-- point update-->
		<?php if($needdispatch){ ?>
		if(id == '')
			update_dispatch($('#sel-provance').val());
		<?php } ?>
		canculate();
		
	}
	
	<!--更新购物车-->
	function updateCart(id,num,ispoint){
		if($("#goodsnum_" + id).hasClass('ismanual'))
			var url = "<?php  echo $this->createMobileUrl('mycart',array('op'=>'updateprice'));?>"+ "&id=" + id+"&marketprice=" + num+"&ispoint=" + 0;<!--point update-->
		else
			var url = "<?php  echo $this->createMobileUrl('mycart',array('op'=>'update'));?>"+ "&id=" + id+"&num=" + num+"&ispoint=" + ispoint;<!--point update-->
		$.getJSON(url, function(s){
			<?php if($needdispatch){ ?>
			update_dispatch($('#sel-provance').val());
			<?php } ?>
			canculate();
		});
	}
	
	<!--买家输入-->
	function changeprice(id){
		var price = $("#goodsprice_"+id).val();
		if(price==''){
			tip("请输入金额", true);
		}
		else if(parseFloat(price)==0){
			tip("金额不能为0", true);
		}
		else if (!/^\d+[.]?\d*$/.test(price)){
			tip("请填写正确的金额", true);
		}
		else if(parseFloat(price)><?php echo WEPAY_MAX_TOTAL_FEE;?>){
			alert("金额超出范围，请重新输入<?php echo WEPAY_MAX_TOTAL_FEE;?>以内金额");
		}
		else if(!(/^([1-9]\d+|\d)(\.\d{1,2})?$/).test(parseFloat(price))){
			tip("金额最多只能保留小数点后两位", true);
		}else{	
		
			$("#goodsprice_" + id).html(price);
			canculate();
			updateCart(id,price,0);  //数量一直为1
			var url = "<?php  echo $this->createMobileUrl('mycart',array('op'=>'updateprice',true));?>"+ "&id=" + id+"&marketprice=" + price;
			$.getJSON(url, function(s){
				
			});
			

		}
	}
	$(function(){
		
		<?php if(!empty($order_address['stagename'])){?>
			var province; 
			var city;
			var area;
			var stagename = '<?php echo $order_address['stagename'];?>'; 
			if(stagename.split(" ").length == 3){
				if(stagename.split(" ")[0] != ""){
					province = stagename.split(" ")[0];
					//$("#sel-provance").val(province);
				}else{
					province = "";
				}
				if(stagename.split(" ")[1] != ""){
					city = stagename.split(" ")[1];
					//$("#sel-city").val(city);
				}else{
					city = "";
				}
				if(stagename.split(" ")[2] != ""){
					area = stagename.split(" ")[2];
					//$("#sel-area").val(area);
				}else{
					area = "";
				}
			}
			if(stagename.split(" ").length == 1){
				if(stagename.split(" ")[0] != ""){
					province = stagename.split(" ")[0];
					//$("#sel-provance").val(province);
				}else{
					province = "";
				}
				city = "";
				area = "";
				
			}
			cascdeInit(province,city,area,'<?php bloginfo('template_directory'); ?>/wechat/weshopping/js/app/Area.xml');
		<?php }?>
		<?php if($needdispatch){ ?>
			update_dispatch($('#sel-provance').val());
		<?php } ?>
		canculate();
	})
	
	<?php if(!($this -> is_weixin() && $weixin -> isConfigAvailable()) && !$needdispatch){ ?>//???可以积分
	alert("当前无法发起微信支付, 无法处理虚拟订单，请创建实物订单!");
	$('button[name="submit"]').addClass('disabled');
	<?php } ?>
	function callpay(){		

		var username=document.getElementById("username").value;
		var telnumber=document.getElementById("telnumber").value;
		var postalcode=document.getElementById("postalcode").value;
		//var stagename=document.getElementById("stagename").value;
		var stagename;
		if($("#sel-city option:selected").val() == undefined && $("#sel-area option:selected").val() == undefined){
		    stagename= $("#sel-provance option:selected").val();
		}else{
			stagename= $("#sel-provance option:selected").val()+" "+$("#sel-city option:selected").val()+" "+$("#sel-area option:selected").val();
		}
		var detailinfo=document.getElementById("detailinfo").value;
		var isdelivery = <?php echo $needdispatch?'0':'1'; ?>;
		if(isdelivery==0){
			var mobilereg = /^[0-9-]+$/; //联系电话是数字字符串或者带有横线
			var postalreg= /^[1-9][0-9]{5}$/;//邮编格式
			
			if(username==''||telnumber==''||postalcode==''||stagename==''||detailinfo==''){
				alert("请输入完整的收货地址信息");
				return false;
			}
			if(!mobilereg.test(document.getElementById('telnumber').value)){
				tip("电话格式不正确", true);
				return false;
			}
			if(!postalreg.test(document.getElementById('postalcode').value)){
				tip("邮编格式不正确", true);
				return false;
			}
		}
		var flag = true;
		//还需要判断对应的金额的限制
		$(".goodsprice").each(function(){
		    if($(this).children('input').length){
		        var inputprice = $(this).children('input').val();
				if(inputprice==''){
					alert("请输入自定义金额");
					flag = false;
					return false;
				}
				else if(parseFloat(inputprice)==0){
					alert("自定义金额不能为0");
					flag = false;
					return false;
				}
				else if (!/^\d+[.]?\d*$/.test(inputprice)){
					alert("请填写正确的自定义金额");
					flag = false;
					return false;
				}
				else if(parseFloat(inputprice)><?php echo WEPAY_MAX_TOTAL_FEE;?>){
					alert("自定义金额超出范围，请重新输入<?php echo WEPAY_MAX_TOTAL_FEE;?>以内金额");
					flag = false;
					return false;
				}
				else if(!(/^([1-9]\d+|\d)(\.\d{1,2})?$/).test(parseFloat(inputprice))){
					alert("自定义金额最多只能保留小数点后两位");
					flag = false;
					return false;
				}
			}
		});
		
		
		
		//单个金额的判断可行，则提交订单时的总金额也需要判断下
		if(flag){
			var totalsubmit = $('#totalprice').text();
			if(parseFloat(totalsubmit)><?php echo WEPAY_MAX_TOTAL_FEE;?>){
				alert("总金额超出范围，请减少所买件数或者输入金额的大小，控制总金额在<?php echo WEPAY_MAX_TOTAL_FEE;?>以内");
				flag = false;
				return false;
			}
		}
		<!-- point 提交前判断积分是否超出会员-->
		if(flag){
			var totalpointsubmit = $('#pointtotal').text();
			if(parseInt(totalpointsubmit)>parseInt(point)){
				alert("使用的积分超出您现在的会员积分");
				flag = false;
				return false;
			}
		}
		<!-- point END-->
		<?php if(!empty($_GET['id'])&& empty($_GET['ismanual'])){?>
				if($("#ispoint_").is(':checked')) {
					var selectpoint=1;
				}else{
					var selectpoint=0;
				}
		<?php } ?>
		
		var address_array=new Array();
		address_array[0]=username;
		address_array[1]=telnumber;
		address_array[2]=postalcode;
		address_array[3]=stagename;
		address_array[4]=detailinfo;
		address=eval(address_array);
		if(flag){
			$.ajax({
				async:true,
				url:window.location.href, 
				type: "POST",
				//data:{'order_add':'isAdd','gweid':gweid,'goodsid':goodsid,'goods_price':price,'goodstotal':goodstotal,'totalfee':totalfee,'address':address},
				data:{'order_add':'isAdd','gweid':<?php echo $gweid;?>,'address':address, 'dispatch': $("#dispatch").find("option:selected").val(),'remark': $('#remark').val()<?php if(!empty($_GET['id'])){?> ,'total':$('input[name="total"]').val() <?php }?><?php if(!empty($_GET['id'])&&!empty($_GET['ismanual'])){?> ,'manual_price':$('input[name="manual_price"]').val() <?php }?><?php if(!empty($_GET['id'])&& empty($_GET['ismanual'])){?> ,'selectpoint':selectpoint <?php }?>},
				success: function(data){
					console.info(data);
				   // alert(data);
					if (data.type == 'error'){
						alert(data.message);
					}else if (data.status == 'success'){
						window.location.href = data.url;
					}
				},
				error: function(data){
					alert("出现错误,请重试");
				},
				dataType: 'json'
			});	
        }		
	}
	var olddispatch = 0;
	function update_dispatch(province){
		olddispatch = 0;
		if($('#dispatch').val()>0)
			olddispatch = $('#dispatch').val();
			
		//point update	获取此时的积分数量，如果配送方式加上后大于会员积分则不允许选择
		//计算checkbox选中的积分和point update
		var pointall=0;
		$("input:checkbox[name=ispoint]:checked").each(function() {
			if($(this).attr('id').replace(/[^0-9]/ig, "")!==''){
					var pointitemid=parseInt($(this).attr('id').replace(/[^0-9]/ig, ""));
			}else{
				var pointitemid='';//从非购物车跳转，parseInt后变成NaN错误，赋值为空
			}
			var num = parseInt( $("#goodsnum_" + pointitemid).val() );
			var goodspoint=$("#point_" + pointitemid).html()==''?0:parseInt($("#point_" + pointitemid).html());
			pointall=parseInt(pointall)+parseInt(num)*parseInt(goodspoint);
		});	
			
			
		$.ajax({
			async:false,
			url:window.location.href + '&action=orderdispatch&province='+province <?php if(!empty($id) && empty($_GET['ismanual'])){?>+'&modifiedtotal='+ $('input[name="total"]').val()<?php } ?>, 
			type: "GET",
			success: function(data){
				console.info(data);
				$('#dispatch').empty();
				var isselect=1;
				$(data).each(function(){
					if(this.dispid=='-2'){//point update
						if(parseInt(this.price)+parseInt(pointall)>parseInt(point)){//如果配送方式加上后大于会员积分则不允许选择
							$('#dispatch').append('<option disabled="" value="'+this.id+'" price="'+parseInt(this.price)+'" distype="'+parseInt(this.dispatchtype)+'" dispid="'+parseInt(this.dispid)+'">'+this.dispatchname+' ('+parseInt(this.price)+'积分)</option>');
						}else{
							$('#dispatch').append('<option value="'+this.id+'" price="'+parseInt(this.price)+'" distype="'+parseInt(this.dispatchtype)+'" dispid="'+parseInt(this.dispid)+'">'+this.dispatchname+' ('+parseInt(this.price)+'积分)</option>');
							olddispatch=this.id;
							isselect=2;
						}
						
					}else{
						$('#dispatch').append('<option value="'+this.id+'" price="'+this.price+'" distype="'+parseInt(this.dispatchtype)+'" dispid="'+parseInt(this.dispid)+'">'+this.dispatchname+' ('+this.price+'元)</option>');
						olddispatch=this.id;
						isselect=2;
					}
					
				});
				if($(data).length == 0){
					$('#dispatch').append('<option value="" price="0">暂无可用配送方式，请与商家联系</option>');
					if(!$('button[name="submit"]').hasClass('disabled'))
						$('button[name="submit"]').addClass('disabled');
				}
				else{	
					if(isselect==1){//如果配送方式加上后大于会员积分则不允许选择
						$('#dispatch').prepend('<option value="nopoint" price="0">暂无可用配送方式</option>');
						$("#dispatch").val('nopoint');
						if(!$('button[name="submit"]').hasClass('disabled'))
						$('button[name="submit"]').addClass('disabled');
					}else{
						if($('input[name="submit"]').hasClass('disabled'))
							$('input[name="submit"]').removeClass('disabled');
						if(olddispatch)
							$('#dispatch').val(olddispatch);
					}
				}
				
			   	},
			error: function(data){
				tip("出现错误,请重试", true);
			},
			dataType: 'json'
		});				 
	}
	
	<!--point -->
	function checkpoint(id){
		if($("#ispoint_" + id).is(':checked')) {
			$("#goodsprice_point_" + id).css('display','block');
			$("#goodsprice_" + id).css('display','none');
			//获取选中状态的checkbox
			var pointall=0;
			$("input:checkbox[name=ispoint]:checked").each(function() {
				if($(this).attr('id').replace(/[^0-9]/ig, "")!==''){
					var pointitemid=parseInt($(this).attr('id').replace(/[^0-9]/ig, ""));
				}else{
					var pointitemid='';//从非购物车跳转，parseInt后变成NaN错误，赋值为空
				}
				var num = parseInt( $("#goodsnum_" + pointitemid).val() );
				var goodspoint=$("#point_" + pointitemid).html()==''?0:parseInt($("#point_" + pointitemid).html());
				pointall=parseInt(pointall)+parseInt(num)*parseInt(goodspoint);
			
			});
			
			if(pointall>parseInt(point)){//购物车所有的积分购买和与会员剩余积分比较
				tip("积分不足",true);
				$("#ispoint_" + id).attr("checked", false);
				$("#goodsprice_point_" + id).css('display','none');
				$("#goodsprice_" + id).css('display','block');
			}else{
				var num = parseInt( $("#goodsnum_" + id).val() );
				var ispoint=1;
				canculate();
				updateCart(id,num,ispoint);
			}
		}else{
			var num = parseInt( $("#goodsnum_" + id).val() );
			var ispoint=0;
			canculate();
			$("#goodsprice_point_" + id).css('display','none');
			$("#goodsprice_" + id).css('display','block');
			updateCart(id,num,ispoint);	
		}
		
	}
	<!--point END-->
	
	<?php if($needdispatch){ ?>
		$('#sel-provance').change(function(){
			update_dispatch($('#sel-provance').val());
			canculate();
		});
	<?php } ?>
</script>
