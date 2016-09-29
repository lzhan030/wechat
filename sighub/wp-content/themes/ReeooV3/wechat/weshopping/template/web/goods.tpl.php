<?php defined('IN_IA') or exit('Access Denied');?>
<?php // (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common', TEMPLATE_INCLUDEPATH)) : (include template('common', TEMPLATE_INCLUDEPATH));?>
<?php include $this -> template('header');?>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/wechat/weshopping/js/lib/jquery-ui-1.10.3.min.js"></script>
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/js/editor/themes/default/default.css" />
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/js/editor/plugins/code/prettify.css" />
<script charset="utf-8" src="<?php bloginfo('template_directory'); ?>/js/editor/kindeditor-all.js"></script>
<script charset="utf-8" src="<?php bloginfo('template_directory'); ?>/js/editor/lang/zh_CN.js"></script>
<script charset="utf-8" src="<?php bloginfo('template_directory'); ?>/js/editor/plugins/code/prettify.js"></script>
<div class="main-title">
	<div class="title-1">当前位置：<a href="<?php echo $this->createWebUrl('index',array());?>">微商城</a> > <font class="fontpurple">商品管理</font></div>
</div>
<div class="shop_content">
<ul class="nav nav-tabs">
	<li <?php  if($operation == 'display') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('goods', array('op' => 'display'))?>">管理商品</a></li>
	<li <?php  if($operation == 'post' && empty($item['id'])) { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('goods', array('op' => 'post'))?>">添加商品</a></li>
	<li <?php  if($operation == 'post' && !empty($item['id'])) { ?>class="active" ><a href="<?php  echo $this->createWebUrl('goods', array('op' => 'post','id'=>$item['id']))?>">编辑商品</a></li><?php  } ?>
</ul>
</div>
<?php  if($operation == 'post') { ?>
<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/wechat/weshopping/css/uploadify_t.css" />
<style type='text/css'>
	.tab-pane {padding:20px 0 20px 0;}
</style>
<div class="main">
	<form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" id="form1" onsubmit='return formcheck()'>
		<div class="panel panel-default" style="margin-right:20px;">
			<div class="panel-heading">
				<?php  if(empty($item['id'])) { ?>添加商品<?php  } else { ?>编辑商品<?php  } ?>
			</div>
			<div class="panel-body">
				<ul class="nav nav-tabs" id="myTab">
					<li	class="active"><a href="#tab_basic">基本信息</a></li>
					<li><a href="#tab_des">商品描述</a></li>
			   		<li><a href="#tab_param">自定义属性</a></li>
					<li><a href="#tab_option">商品规格</a></li>
					<li><a href="#tab_other">其他设置</a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane active" style="margin-right:130px;" id="tab_basic"><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('goods_basic', TEMPLATE_INCLUDEPATH)) : (include template('goods_basic', TEMPLATE_INCLUDEPATH));?></div>
		  			<div class="tab-pane" style="margin-right:18px;" id="tab_des"><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('goods_des', TEMPLATE_INCLUDEPATH)) : (include template('goods_des', TEMPLATE_INCLUDEPATH));?></div>
		  			<div class="tab-pane" id="tab_param"><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('goods_param', TEMPLATE_INCLUDEPATH)) : (include template('goods_param', TEMPLATE_INCLUDEPATH));?></div>
		  			<div class="tab-pane" id="tab_option"><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('goods_option', TEMPLATE_INCLUDEPATH)) : (include template('goods_option', TEMPLATE_INCLUDEPATH));?></div>
		  			<div class="tab-pane" id="tab_other"><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('goods_other', TEMPLATE_INCLUDEPATH)) : (include template('goods_other', TEMPLATE_INCLUDEPATH));?></div>
				</div>
			</div>
		</div>
		<div class="form-group col-sm-12">
			<input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1" />
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
		</div>
	</form>
</div>

<script type="text/javascript">
	var category = <?php  echo json_encode($children)?>;

	$(function () {
		window.optionchanged = false;
		$('#myTab a').click(function (e) {
			e.preventDefault();//阻止a链接的跳转行为
			$(this).tab('show');//显示当前选中的链接及关联的content
		})
	});

	function formcheck(){
		var isParaError = true;
		var isValError = true;
		$('textarea[name="content"]').val($('#editor').html());
		if($("#goodsname").val()=='') {
			$('#myTab a[href="#tab_basic"]').tab('show');
			alert("请输入商品名称");
			return false;
		}
		
		if($("#pcate").val()=='0'){
			$('#myTab a[href="#tab_basic"]').tab('show');
			alert("请选择商品分类");
			return false;
		}
		//价格判断
		if($(':radio[name=ismanual]:checked').val()=='0') {
			var mprice=$("#marketprice").val();
			var pprice=$("#productprice").val();
			var cprice=$("#costprice").val();
			
			var mcheck_result=price_check(mprice,true,"销售");
			if(mcheck_result==true){
				var pcheck_result=price_check(pprice,false,"市场价");
			}
			if(mcheck_result==true && pcheck_result==true){
				var ccheck_result=price_check(cprice,false,"成本价");
			}
			if(mcheck_result==false || pcheck_result==false || ccheck_result==false){
				$('#myTab a[href="#tab_basic"]').tab('show');
				return false;
			}
		}
		//最多购买数量
		if(!((/^\+?(0|[1-9][0-9]*)$/.test($("#point").val()))||$("#point").val()=='')){
			$('#myTab a[href="#tab_basic"]').tab('show');
			alert("请输入正确的积分");
			return false;
		}
		//包邮数字填写
		if($(':radio[name=isfreedelivery]:checked').val()=='1') {
			if(!(/^\+?(0|[1-9][0-9]*)$/.test($("#freedeliverycount").val()))){
				$('#myTab a[href="#tab_basic"]').tab('show');
				alert("请输入数字包邮件数");
				return false;
			}
		}
		//库存判断
		if(!((/^\+?(0|[1-9][0-9]*)$/.test($("#total").val()))||$("#total").val()==-1||$("#total").val()=='')){
			$('#myTab a[href="#tab_basic"]').tab('show');
			alert("请输入正确的库存数量");
			return false;
		}
		//最多购买数量
		if(!((/^\+?(0|[1-9][0-9]*)$/.test($("#maxbuy").val()))||$("#maxbuy").val()=='')){
			$('#myTab a[href="#tab_basic"]').tab('show');
			alert("请输入正确的最多购买量");
			return false;
		}
		//已出售数量
		if(!((/^\+?(0|[1-9][0-9]*)$/.test($("#sales").val()))||$("#sales").val()=='')){
			$('#myTab a[href="#tab_basic"]').tab('show');
			alert("请输入正确的已出售数量");
			return false;
		}
		//自定义属性名称判断
		$("input[name^='param_title[]']").each(function() {
			if(isParaError==true){
				if( $(this).val()=="") {
					isParaError = false;
				}
			}
		});
		
		if(!isParaError) {
			$('#myTab a[href="#tab_param"]').tab('show');
			alert("请输入自定义属性属性名称");
			return false;
		}
		//自定义属性值判断
		$("input[name^='param_value[]']").each(function() {
			if(isValError==true){
				if( $(this).val()=="") {
					isValError = false;
				}
			}
		});
		if(!isValError) {
			$('#myTab a[href="#tab_param"]').tab('show');
			alert("请输入自定义属性属性值");
			return false;
		}
		//商品规格判断
		if(window.optionchanged){
			$('#myTab a[href="#tab_option"]').tab('show');
			alert('规格数据有变动，请重新点击 [刷新规格项目表] 按钮');
			return false;
		}
		var full = checkoption();
		if(!full){return false;}
		//商品排序
		if(!((/^\+?(0|[1-9][0-9]*)$/.test($("#displayorder").val()))||$("#displayorder").val()=='')){
			$('#myTab a[href="#tab_other"]').tab('show');
			alert("请输入正确的商品排序");
			return false;
		}
		return true;
	}
	
	function checkoption(){
		var full = true;
		if( $("#hasoption").get(0).checked){
			$(".spec_title").each(function(i){
				if( $(this).val()=="") {
					$('#myTab a[href="#tab_option"]').tab('show');
					alert("请输入规格名称");
					full =false;
					return false;
				}
			});
			$(".spec_item_title").each(function(i){
				if(full==true){	
					if( $(this).val()=="") {
						$('#myTab a[href="#tab_option"]').tab('show');
						alert("请输入规格项名称");
						full =false;
						return false;
					}
				}
			});
			$("input[name^='option_stock_']").each(function() {
				if(full==true){
					if(!((/^\+?(0|[1-9][0-9]*)$/.test($(this).val()))||$(this).val()==-1||$(this).val()=='')){
						$('#myTab a[href="#tab_option"]').tab('show');
						alert("请输入正确的库存数量");
						full =false;
						return false;
					}
				}
			});
			
			$("input[name^='option_marketprice_']").each(function() {
				if(full==true){
					var check_result=price_check($(this).val(),true,"销售");
					if(check_result==false){
						$('#myTab a[href="#tab_option"]').tab('show');
						full =false;
						return false;
					}
				}
			});
			
			$("input[name^='option_productprice_']").each(function() {
				if(full==true){
					var check_result=price_check($(this).val(),false,"市场");
					if(check_result==false){
						$('#myTab a[href="#tab_option"]').tab('show');
						full =false;
						return false;
					}
				}
			});
			$("input[name^='option_costprice_']").each(function() {
				if(full==true){
					var check_result=price_check($(this).val(),false,"成本");
					if(check_result==false){
						$('#myTab a[href="#tab_option"]').tab('show');
						full =false;
						return false;
					}
				}
			});
			$("input[name^='option_point_']").each(function() {
				if(full==true){
					if(!((/^\+?(0|[1-9][0-9]*)$/.test($(this).val()))||$(this).val()=='')){
						$('#myTab a[href="#tab_option"]').tab('show');
						alert("请输入正确的使用积分");
						full =false;
						return false;
					}
				}
			});
		}
		if(!full) { return false; }
		return full;
	}
	function price_check(check_price,isnull,title){
		if(isnull==true){
			if(check_price==''){
				alert("请输入"+title+"价格");
				return false;
			}
			if( parseFloat(check_price)==0){
				alert(title+"价格不能等于0");
				return false;
			}
			if (!/^\d+[.]?\d*$/.test(check_price)){
				alert("请填写正确的"+title+"价格");
				return false;
			}
			if(check_price><?php echo WEPAY_MAX_TOTAL_FEE;?>){
				alert(title+"价格超出范围，请重新输入<?php echo WEPAY_MAX_TOTAL_FEE;?>以内金额");
				return false;
			}
			if(!(/^([1-9]\d+|\d)(\.\d{1,2})?$/).test(check_price)){
				alert(title+"价格最多只能保留小数点后两位");
				return false;
			}
		}else{
			if (!(/^\d+[.]?\d*$/.test(check_price)||(check_price==''))){
				alert("请填写正确的"+title+"价格");
				return false;
			}
		}
		return true;
	}
</script>

<?php  } else if($operation == 'display') { ?>

<div class="main" style="width:98%">
	<div class="panel panel-info">
	<div class="panel-heading">筛选</div>
	<div class="panel-body">
		<form action="" method="get" class="form-horizontal" role="form">
			<input type="hidden" name="c" value="site" />
			<input type="hidden" name="a" value="entry" />
			<input type="hidden" name="m" value="ewei_shopping" />
			<input type="hidden" name="module" value="weshopping" />
			<input type="hidden" name="do" value="goods" />
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">商品标题</label>
				<div class="col-xs-12 col-sm-8 col-lg-9">
					<input class="form-control" name="keyword" id="" type="text" value="<?php  echo $_GET['keyword'];?>">
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">状态</label>
				<div class="col-xs-12 col-sm-8 col-lg-9">
					<select name="status" class='form-control'>
							<option value="-1">请选择状态</option>
							<option value="1" <?php  if($_GET['status']=='1') { ?> selected<?php  } ?>>下架</option>
							<option value="0" <?php  if($_GET['status']=='0') { ?> selected<?php  } ?>>上架</option>
							<option value="2" <?php  if($_GET['status']=='2') { ?> selected<?php  } ?>>限时上架</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">分类</label>
				<div class="col-xs-6 col-sm-4">
					<select class="form-control" style="margin-right:15px;" name="cate_1" onchange="fetchChildCategory(this.options[this.selectedIndex].value)">
						<option value="0">请选择一级分类</option>
						<?php  if(is_array($category)) { foreach($category as $row) { ?>
						<?php  if($row['parentid'] == 0) { ?>
						<option value="<?php  echo $row['id'];?>" <?php  if($row['id'] == $_GET['cate_1']) { ?> selected="selected"<?php  } ?>><?php  echo $row['name'];?></option>
						<?php  } ?>
						<?php  } } ?>
					</select>
				</div>
				<div class="col-xs-6 col-sm-4">
					<select class="form-control input-medium" id="cate_2" name="cate_2">
						<option value="0">请选择二级分类</option>
						<?php  if(!empty($_GET['cate_1']) && !empty($children[$_GET['cate_1']])) { ?>
						<?php  if(is_array($children[$_GET['cate_1']])) { foreach($children[$_GET['cate_1']] as $row) { ?>
						<option value="<?php  echo $row['0'];?>" <?php  if($row['0'] == $_GET['cate_2']) { ?> selected="selected"<?php  } ?>><?php  echo $row['1'];?></option>
						<?php  } } ?>
						<?php  } ?>
					</select>
				</div>
				<div class=" col-xs-12 col-sm-2 col-lg-2">
					<button class="btn btn-default">搜索</button>
				</div>
			</div>
			<div class="form-group">
			</div>
		</form>
	</div>
</div>
<style>
.label{cursor:pointer;}
</style>
<div class="panel panel-default" style="width:101%">
	<div class="panel-body table-responsive">
		<table class="table table-hover">
			<thead class="navbar-inner">
				<tr>
					<th style="width:115px;">ID</th>
					<th style="width:190px;">商品标题</th>
					<th style="width:100px;">商品属性(点击可修改)</th>
					<th style="width:230px;">访问链接</th>
					<th style="width:150px;">状态(点击可修改)</th>
					<th style="text-align:right; min-width:100px;">操作</th>
				</tr>
			</thead>
			<tbody>
				<?php  if(is_array($list)) { foreach($list as $item) { ?>
				<tr>
					<td><?php  echo $item['id'];?></td>
					<td title='<?php  if(!empty($category[$item['pcate']])) { ?>[<?php  echo $category[$item['pcate']]['name'];?>]<?php  } ?><?php  if(!empty($children[$item['pcate']])) { ?>[<?php  echo $children[$item['pcate']][$item['ccate']]['1'];?>]<?php  } ?><?php  echo $item['title'];?>'><?php  if(!empty($category[$item['pcate']])) { ?><span class="text-error">[<?php  echo $category[$item['pcate']]['name'];?>] </span><?php  } ?><?php  if(!empty($children[$item['pcate']])) { ?><span class="text-info">[<?php  echo $children[$item['pcate']][$item['ccate']]['1'];?>] </span><?php  } ?><?php  echo $item['title'];?></td>
					<td>
						<label data='<?php  echo $item['isrecommend'];?>' class='label label-default <?php  if($item['isrecommend']==1) { ?>label-info<?php  } else { ?><?php  } ?>' onclick="setProperty(this,<?php  echo $item['id'];?>,'recommend')">首页</label>
					</td>
					<td><input type="text" class="form-control" readonly="readonly"  value="<?php echo $this->createMobileUrl('detail',array('id' => $item['id'],'gweid'=>$gweid)); ?>"></td>
					<td>
						<label data='<?php  echo $item['status'];?>' class='label  label-default <?php  if($item['status']==0 || $item['status']==2) { ?>label-info<?php  } ?>' onclick="setProperty(this,<?php  echo $item['id'];?>,'status')"><?php  if($item['status']==0) { ?>上架<?php  }else if($item['status']==2){  ?>限时上架<?php  }else { ?>下架<?php  } ?></label>
						<label data='<?php  echo $item['type'];?>' class='label  label-default <?php  if($item['type']==1) { ?>label-info<?php  } ?>' onclick="setProperty(this,<?php  echo $item['id'];?>,'type')"><?php  if($item['type']==1) { ?>实体物品<?php  } else { ?>虚拟物品<?php  } ?></label>
					</td>
					<td style="text-align:right;">
						<a href="<?php  echo $this->createWebUrl('goods', array('id' => $item['id'], 'op' => 'delete'))?>" onclick="return confirm('此操作不可恢复，确认删除？');return false;" class="btn btn-warning btn-sm" data-toggle="tooltip" data-placement="top">删除</a>
						<a href="<?php  echo $this->createWebUrl('goods', array('id' => $item['id'], 'op' => 'post'))?>" class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="top">编辑</a>&nbsp;&nbsp;
					</td>
				</tr>
				<?php  } } ?>
			</tbody>
			<!--<tr>
				<td></td>
				<td colspan="6">
					<input name="token" type="hidden" value="<?php  echo $_W['token'];?>" />
					<input type="submit" class="btn btn-primary" name="submit" value="提交" />
				</td>
			</tr>-->
		</table>
		<?php  echo $pager;?>
	</div>
	</div>
</div>
<script type="text/javascript">
	require(['bootstrap'],function($){
		$('.btn').hover(function(){
			$(this).tooltip('show');
		},function(){
			$(this).tooltip('hide');
		});
	});

	var category = <?php  echo json_encode($children)?>;
	function setProperty(obj,id,type){
		$(obj).html($(obj).html() + "...");
		$.post("<?php  echo $this->createWebUrl('setgoodsproperty')?>"
			,{id:id,type:type, data: obj.getAttribute("data")}
			,function(d){
				$(obj).html($(obj).html().replace("...",""));
				if(type=='type'){
				 $(obj).html( d.data=='1'?'实体物品':'虚拟物品');
				}
				if(type=='status'){
				 if(d.data=='0'){goodsstatus='上架'};
				 if(d.data=='2'){goodsstatus='限时上架'};
				 if(d.data=='1'){goodsstatus='下架'};
				 $(obj).html(goodsstatus);
				}
				$(obj).attr("data",d.data);
				if(d.result==1 ){
					$(obj).toggleClass("label-info");
				}
				if((d.data==2) && (type=='status')){
					$(obj).toggleClass("label-info");
				}
			}
			,"json"
		);
	}
	
</script>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>
