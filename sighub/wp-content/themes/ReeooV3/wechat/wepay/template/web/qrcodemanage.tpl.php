<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
<?php
	$gweid=$_SESSION['GWEID'];	
	$r=$_GET['range'];
	$in=$_GET['indata'];
	if (!Empty($r)&&!Empty($in)&& !isset($_GET['nativeOrder']))
	{$product_flag=1;}
	if (!Empty($r)&&!Empty($in)&& isset($_GET['nativeOrder']))
	{$native_flag=1;}
	global $_GPC;
?>

<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/uploadexcel.css">
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/init.css">
<style>
	.sltfield{height: 34px;padding: 6px 12px;font-size: 14px;line-height: 1.428571429;color: #555;vertical-align: middle;background-color: #fff;background-image: none;border: 1px solid #ccc;border-radius: 4px;-webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);-webkit-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;}
	.panel{border-radius: 0px; -webkit-box-shadow: 0 0px 0px rgba(0,0,0,0.05); box-shadow: 0 0px 0px rgba(0,0,0,0.05);}
</style>
	</style>
<div class="main_auto">
	<div class="main-title">
		<div class="title-1">当前位置：<a href="<?php echo $this->createWebUrl('index',array());?>">微支付</a> > <font class="fontpurple">原生支付</font></div>
	</div>
	<ul class="nav nav-tabs" id="nativeTab" style="margin-top: 20px;"> 
      <li class="active"><a href="#product">原生商品二维码</a></li> 
      <li><a href="#createNativeOrder">原生支付订单页面</a></li> 
    </ul> 
<div class="tab-content"> 
      <div class="tab-pane active" id="product">
			<input type="button" class="personentry btn btn-primary" onclick="location.href='<?php echo $this->createWebUrl('createproductqrinfo');?>'" name="del" id="adds" value="创建新原生支付商品" style="margin-top:20px;">
			<form name ="content" onSubmit="return validateforma()" action="<?php echo $this->createWebUrl('Qrcodemanage',array('gweid' => $gweid));?>" method="get" enctype="multipart/form-data">	
				<div class="panel panel-default" style="width: 98%;margin-top:20px">
				<div class="panel-heading">原生支付商品列表</div>
				<div class="panel-heading">
					 <select id="range" name="range" class="sltfield" style="margin-right:5px">
						<option value="">请选择</option>
						<option value="all">全部</option>
						<option value="product_id">商品编号</option>
						<option value="product_name">商品名称</option>
					 </select>
					<input type="hidden" id="module" name="module" value="<?php echo $_GET['module'];?>" />
					<input type="hidden" id="do" name="do" value="<?php echo $_GET['do'];?>" />
					<input type="hidden" id="gweid" name="gweid" value="<?php echo $_GET['gweid'];?>" />
					<input id="indata" class="sltfield" name="indata" value="<?php echo $_GET['indata'];?>" />
					<input type="hidden" name="beIframe" value="1">
					<input id="search1" class="btn btn-info btn-sm" type="submit" value="查询"/>
				</div>	
				<table class="table table-striped">
						<tbody>
							<tr>
								<td scope="col" width="100" align="center" style="font-weight:bold">商品编号</td>
								<td scope="col" width="100" align="center" style="font-weight:bold">商品名称</td>
								<td scope="col" width="100" align="center" style="font-weight:bold">商品价格（元）</td>
								<td scope="col" width="100" align="center" style="font-weight:bold">二维码URL</td>
								<td scope="col" width="100" align="center" style="font-weight:bold">操作</td>
							</tr>
							<?php
								if(is_array($productslist) && !empty($productslist)){
									foreach($productslist as $product){
							?>
							<tr>
									<td align="center"><?php echo $product['product_id']; ?></td>
									<td align="center"><?php echo $product['product_name']; ?> </td>
									<td align="center"><?php echo $product['product_price']; ?> </td>
									<td align="center"><input type="text" class="form-control" readonly="readonly"  value=<?php echo $product['qr_code']?>></td>
								<td class="row" align="center"><input type="hidden"  value="308" maxlength="100"> 
								<input type="button" class="btn btn-sm btn-warning" onclick="delproduct('<?php echo $product['product_id'] ?>',this)" name="del" id="buttondel" value="删除"> 
								<input type="button" class="btn btn-sm btn-info" onclick="location.href='<?php echo $this->createWebUrl('productinformation',array('id' => $product['product_id']));?>'" name="upd" id="buttonupd" value="详情"> 
								</td>	
							</tr>
							<?php }}?>
						</tbody>
				</table>
			</div>
		</form>
		<?php echo $pager; ?>
	</div>
      <div class="tab-pane" id="createNativeOrder">
      	<input type="button" class="btn btn-primary" onclick="location.href='<?php echo $this->createWebUrl('goodsindexhandle',array('native' => true));?>'" name="add" id="buttonadd" value="创建原生支付订单页面" style="margin-top:20px;"/>
		<form name ="content" onSubmit="return validateformb()" action="<?php echo $this->createWebUrl('goodsindexlist',array('gweid' => $gweid));?>" method="get" enctype="multipart/form-data">	
			<div class="panel panel-default" style="margin-right:30px; margin-top:20px">
				<div class="panel-heading">原生支付订单列表</div>
				<table class="table table-striped" width="800" bgoodsindex="1" align="center">
					<tbody>
						<tr>
							<td colspan=10 scope="col" width="100" align="left" >
								<select id="nrange" name="range" class="sltfield" style="margin-right:3px">
									<option value="">请选择
									<option value="all">全部</option>
									<option value="id">链接编号</option>
									<option value="goodsindex_name">链接名称</option>
								</select>
							<input type="hidden" id="module" name="module" value="<?php echo $_GPC['module'];?>" />
							<input type="hidden" id="do" name="do" value="<?php echo $_GPC['do'];?>" />
							<input type="hidden" id="gweid" name="gweid" value="<?php echo $_GPC['gweid'];?>" />
							<input id="nindata" class="sltfield" name="indata" value="" />
							<input type="hidden" name="beIframe" value="1">
							<input type="hidden" name="nativeOrder" value="true">
							
							<input id="search2" class="btn btn-info btn-sm" type="submit" value="查询"/>
							</td>
						</tr>
						<tr>
							<td scope="col" width="10%" align="center" style="font-weight:bold">链接编号</td>
							<td scope="col" width="15%" align="center" style="font-weight:bold">链接名称</td>
							<td scope="col" width="60%" align="center" style="font-weight:bold">链接</td>
							<td scope="col" width="15%" align="center" style="font-weight:bold">操作</td>
						</tr>
						<?php 
							if($native_flag==1){
								$pindex = isset($_GET['nativeOrder'])?max(1, intval($_GET['page'])):1;
								$total = $this -> doWebCountSelectedGoodsindex($gweid,$in,$r,'NATIVE');
								$psize = 5;
								$pindex = min(max(ceil($total/$psize),1),$pindex );
								$offset=($pindex - 1) * $psize;
								$pager = $this -> doWebpaginationa_page($total,$pindex,$psize,'',array('before' => 5, 'after' => 4),array('nativeOrder' => 'true'));
								$rs = $this -> doWebCountSelectedGoodsindexsPage($gweid,$in,$r,$offset,$psize,'NATIVE');
						?>
						<?php
							if($rs!==false){
								foreach ($rs as $goodsindex) {
						?>						
						<tr>
							<td align="center"><?php echo $goodsindex->id; ?></td>
							<td align="center"><?php echo $goodsindex->goodsindex_name; ?></td>
							<td align="center"><input type="text" class="form-control" readonly="readonly"  value="<?php echo $this->createMobileUrl('goodsinfo',array('gweid'=>$gweid,'goodsgid' => $goodsindex->id)); ?>"></td>
							<td class="row" align="center">
								<input type="button" class="btn btn-sm btn-warning" onclick="goodsindexdel('<?php echo $goodsindex->id; ?>',this)" value="删除"></button>
								<input type="button" class="btn btn-sm btn-info" onclick="location.href='<?php echo $this->createWebUrl('goodsindexhandle',array('goodsindexid' => $goodsindex->id,'native' => 'true'));?>'" name="goodsindexupdate" id="goodsindexupdate" value="更新">
							</td>		
						</tr>
						<?php
						}
						?>
					</tbody>
				</table>
			</div>
		</form>
					<?php echo $pager;}
					if($rs==False && empty($rs)&&!isset($_REQUEST['deleteflag'])){ 
						echo "<script language='javascript'>alert('没有符合该条件的查询结果');</script>";
						}?>


					<?} 	else { ?>
					<?php 
						$pindex = isset($_GET['nativeOrder'])?max(1, intval($_GET['page'])):1;
						$total = $this -> doWebCountGoodsindex($gweid,'NATIVE');
						$psize = 5;
						$pindex = min(ceil($total/$psize),$pindex );
						$offset=($pindex - 1) * $psize;
						$pager = $this -> doWebpaginationa_page($total, $pindex, $psize,'',array('before' => 5, 'after' => 4),array('nativeOrder' => 'true'));
						$rs = $this -> doWebCountGoodsindexsPage($offset,$psize,$gweid,'NATIVE');
					?>
					<?php
						foreach ($rs as $goodsindex) {
					?>
					<tr>
						<td align="center"><?php echo $goodsindex->id; ?></td>
						<td align="center"><?php echo $goodsindex->goodsindex_name; ?></td>
						<td align="center"><input type="text" class="form-control" readonly="readonly"  value="<?php echo $this->createMobileUrl('goodsinfo',array('gweid'=>$gweid,'goodsgid' => $goodsindex->id)); ?>"></td>
						<td class="row" align="center">
							<input type="button" class="btn btn-sm btn-warning" onclick="goodsindexdel('<?php echo $goodsindex->id; ?>',this)" value="删除"></button>
							<input type="button" class="btn btn-sm btn-info" onclick="location.href='<?php echo $this->createWebUrl('goodsindexhandle',array('goodsindexid' => $goodsindex->id,'native' => 'true'));?>'" name="goodsindexupdate" id="goodsindexupdate" value="更新">
						</td>		
					</tr>
						<?php
						}
						?>
					</tbody>
				</table>
				</div>
			</form>	
			<?php echo $pager;} ?>
		
		</div>
    </div>	
</div>
<script language='javascript'>
      $(function () { 
      	<?php if(isset($_GET['nativeOrder'])){?>
      		$('#nativeTab a:last').tab('show');//初始化显示哪个tab 
      	<?php } ?>
      
        $('#nativeTab a').click(function (e) { 
          e.preventDefault();//阻止a链接的跳转行为 
          $(this).tab('show');//显示当前选中的链接及关联的content 
        }) 
      }) 
	$(function(){
		if( $('#range').val() == 'all')
			$("#indata").hide();
		$('#range').change( function(){
				if($(this).val() == 'all'){
				$("#indata").hide();//隐藏
				$("#indata").val("");  
				}
				else 
				$("#indata").show();//显示
			})
		}
	);
	$('#range').val('<?php echo $_GET['range'];?>');
		<?php if(empty($productslist)&&!empty($search_condition)){?>
		alert('没有符合该条件的查询结果');
		<?php }?>
	var g="<?php echo $r ?>";
	var i="<?php echo $in ?>";
	$(function(){
		if( $('#nrange').val() == 'all')
			$("#nindata").hide();
		$('#nrange').change( function(){
				if($(this).val() == 'all'){
				$("#nindata").hide();//隐藏
				$("#nindata").val("");  
				}
				else 
				$("#nindata").show();//显示
			})
		}
	);
	<?php
	if($product_flag==1){?>
		document.getElementById("range").value=g;
		document.getElementById("indata").value=i;
	<?php }?>
	<?php
	if($native_flag==1){?>
		document.getElementById("nrange").value=g;
		document.getElementById("nindata").value=i;
	<?php }?>
	isSubmitting=false;
	function goodsindexdel(id,obj){
		if(confirm("确定删除吗？")){
			if(isSubmitting)
			return false;
			isSubmitting = true;
			$deletedobj = $(obj).parent().parent();
		$.ajax({
			url:window.location.href, 
			type: "POST",
			data:{'goodsindex_del':'isDel','goodsindexid':id},
			success: function(data){
				if (data.status == 'error'){
					alert(data.message);
				}else if (data.status == 'success'){
					alert(data.message);
					$deletedobj.remove();
				}
				isSubmitting = false;
			},
			 error: function(data){
				alert("出现错误");
				isSubmitting = false;
			},
			dataType: 'json'
		});	
		}		
	}
	isSubmitting=false;
	function delproduct(id,obj){
		if(confirm("确定删除吗？")){
			if(isSubmitting)
			return false;
			isSubmitting = true;
			$deletedobj = $(obj).parent().parent();
		$.ajax({
			url:window.location.href, 
			type: "POST",
			data:{'productindex_del':'isDel','productindexid':id},
			success: function(data){
				if (data.status == 'error'){
					alert(data.message);
				}else if (data.status == 'success'){
					alert(data.message);
					$deletedobj.remove();
				}
				isSubmitting = false;
			},
			 error: function(data){
				alert("出现错误");
				isSubmitting = false;
			},
			dataType: 'json'
		});
		}		
	}
	function checknull(obj, warning)
	{	
	  if (obj.value == "") {
		alert(warning);
		obj.focus();
		return true;
	  }
	return false;
	}
	function validateforma()
	{
		var range = $('#range').val();
		if(range == "all")
			return true;
			
		if(range == ""){
			alert("请选择查询条件！");
			return false;
		}
		if($('#indata').val()==""){
			alert("请输入查询内容");
			return false;
		}
		return true;
	} 	
	function validateformb()
	{
		var range = $('#nrange').val();
		if(range == "all")
			return true;
			
		if(range == ""){
			alert("请选择查询条件！");
			return false;
		}
		if($('#nindata').val()==""){
			alert("请输入查询内容");
			return false;
		}
		return true;
	} 
</script>