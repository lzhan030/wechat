<?php defined('IN_IA') or exit('Access Denied');?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common', TEMPLATE_INCLUDEPATH)) : (include template('common', TEMPLATE_INCLUDEPATH));?>
<?php include $this -> template('header');?>
<div class="main-title">
	<div class="title-1">当前位置：<a href="<?php echo $this->createWebUrl('index',array());?>">微商城</a> > <font class="fontpurple">配送管理</font></div>
</div>
<div class="shop_content">
	<ul class="nav nav-tabs">
		<li <?php  if($operation == 'display') { ?> class="active" <?php  } ?>><a href="<?php  echo $this->createWebUrl('dispatch',array('op' =>'display'))?>">配送方式</a></li>
		<li <?php if(empty($_GET['id']) && $operation == 'post') { ?> class="active" <?php  } ?>><a href="<?php  echo $this->createWebUrl('dispatch',array('op' =>'post'))?>">添加配送方式</a></li>
		<?php if(!empty($_GET['id']) && $operation== 'post') { ?> <li class="active"><a href="<?php echo $this->createWebUrl('dispatch',array('op' =>'post','id'=>$dispatch['id']))?>">编辑配送方式</a></li> <?php  } ?>
	</ul>
	<?php  if($operation == 'display') { ?>
	<div class="main panel panel-default" style="height: auto;">
		<div class="panel-heading">配送列表</div>
		<div class="panel-body table-responsive">
			<table class="table table-hover" style="min-width:800px;">
				<thead class="navbar-inner">
					<tr>
						<th style="width:50px;">编号</th>
						<th>配送方式</th>
						<th>配送类型</th>
						<th>物流公司</th>
						<th>操作</th>
					</tr>
				</thead>
				<tbody>
					<?php  if(is_array($list)) { foreach($list as $item) { ?>
					<tr>
						<td><?php  echo $item['id'];?></td>
						<td><?php  echo $item['dispatchname'];?></td>
						<td><?php  if($item['dispatchtype']==0) { ?>
						先付款后发货（微支付）
						<?php  } else if($item['dispatchtype']==1) { ?> 货到付款
						<?php  } else if($item['dispatchtype']==2) { ?> 自提
						<?php  }  ?></td>
						<td><?php echo $item['express_name'];?></td>
						<td style="text-align:left;">
							<a href="<?php  echo $this->createWebUrl('dispatch', array('op' => 'delete', 'id' => $item['id']))?>" onclick="return confirm('此操作不可恢复，确认删除？');return false;" class="btn btn-warning btn-sm" data-placement="bottom" >删除</a>
							<a href="<?php  echo $this->createWebUrl('dispatch', array('op' => 'post', 'id' => $item['id']))?>" class="btn btn-info btn-sm" data-placement="bottom" >修改</a>
						</td>
					</tr>
					<?php  } } ?>
				</tbody>
			</table>
			<?php echo $pager;?>
		</div>
	</div>
</div>
<?php  } else if($operation == 'post') { ?>
<div class="main">
	<form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" onsubmit='return formcheck()'>
		<input type="hidden" name="id" value="<?php  echo $dispatch['id'];?>" />
		<input type="hidden" name="id_point" value="<?php  echo $dispatch['dispid'];?>" />
		<div class="panel panel-default">
			<div class="panel-heading">
				配送方式设置
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">配送方式名称(必填)</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" id='dispatchname' name="dispatchname" class="form-control" value="<?php echo $dispatch['dispatchname'];?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">物流公司</label>
					<div class="col-sm-9 col-xs-12">
						<select name='expressname' id="expressname" class='form-control'>
							<option value="" <?php  if(empty($dispatch['express'])) { ?>selected<?php  } ?>><?php  echo $express['express_name'];?></option>
							<?php  if(is_array($express)) { foreach($express as $ex) { ?>
							<option value="<?php  echo $ex['id'];?>" <?php  if($dispatch['express']==$ex['id']) { ?>selected<?php  } ?>><?php  echo $ex['express_name'];?></option>
							<?php  } } ?>
						</select>
					</div>
				</div>				
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">配送类型</label>
					<div class="col-sm-9 col-xs-12">
						<label class='radio-inline'>
							<input type='radio' name='dispatchtype' value='0' <?php  if($dispatch['dispatchtype']==0) { ?>checked<?php  } ?> /> 先付款后发货(微支付)
						</label>
						<label class='radio-inline'>
							<input type='radio' name='dispatchtype' value='1' <?php  if($dispatch['dispatchtype']==1) { ?>checked<?php  } ?> /> 货到付款
						</label>
						<label class='radio-inline'>
							<input type='radio' name='dispatchtype' value='2' <?php  if($dispatch['dispatchtype']==2) { ?>checked<?php  } ?> /> 自提
						</label>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">介绍</label>
					<div class="col-sm-9 col-xs-12">
						<textarea name="description" class="form-control" cols="70"><?php  echo $dispatch['description'];?></textarea>
					</div>
				</div>				
				<div class="form-group" style="margin-top:50px;border-top: 1px dashed #ccc;">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">
						<input type='radio' name='dispatchpricetype' id='simpleradio' value='0' <?php  if($dispatch['dispatchpricetype']==0) { ?>checked<?php  } ?> /> 简单配送方式
					</label>
				</div>
				<div class="form-group" id="simplepricetype">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
					<div class="col-sm-9 col-xs-12">				
					<div class="input-group  input-medium nopoint">
						<span class="input-group-addon">运费设置</span>
						<input type="text" name="dispatchprice" id='dispatchprice' class="form-control input-medium" value="<?php   echo $dispatch['dispatchprice']; ?>" />
						<span class="input-group-addon">元</span>
					</div>
						<span class='help-block'>无条件的运费设置</span>
					</div>
				</div>				
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">
						<input type='radio' name='dispatchpricetype' id='complexradio' value='1' <?php  if($dispatch['dispatchpricetype']==1) { ?>checked<?php  } ?> /> 复杂配送方式
					</label>
				</div>
				<div id="complexpricetype">
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">地区设置</label>
						<div class="col-sm-9 col-xs-12 nopoint">
							<label class="checkbox-inline" style="font-weight:bold;">
								<input type="checkbox" name="chk_all" id="chk_all" value="0" onclick="check_all(this, 'regions[]')" />全选/反选
							</label>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
						
						<div class="col-sm-9 col-xs-12 nopoint">
							<?php for($i = 0; $i < count($allregions); $i ++) {?>
								<label class="checkbox-inline">
									<?php $in = in_array($allregions[$i],$r); ?>
									<input type="checkbox" class="checkpoint" name="regions[]" id="regions[]" value="<?php echo $allregions[$i];?>" <?php if(!empty($in)) {?>checked="checked"<?php } ?> /><?php echo $allregions[$i];?>
								</label>
							<?php }?> 
						</div>

					</div>					
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">重量设置</label>
						<div class="col-sm-9 col-xs-12">
							<div class="input-group input-medium">
								<span class="input-group-addon">首重重量</span>
								<select name="firstweight" id="firstweight" class='form-control input-medium'>
									<option value="500" <?php  if($dispatch['firstweight']==500) { ?>selected<?php  } ?>>0.5</option>
									<option value="1000" <?php  if($dispatch['firstweight']==1000 || empty($dispatch['firstweight'])) { ?>selected<?php  } ?>>1</option>
									<option value="1200" <?php  if($dispatch['firstweight']==1200) { ?>selected<?php  } ?>>1.2</option>
									<option value="2000" <?php  if($dispatch['firstweight']==2000) { ?>selected<?php  } ?>>2</option>
									<option value="5000" <?php  if($dispatch['firstweight']==5000) { ?>selected<?php  } ?>>5</option>
									<option value="10000" <?php  if($dispatch['firstweight']==10000) { ?>selected<?php  } ?>>10</option>
									<option value="20000" <?php  if($dispatch['firstweight']==20000) { ?>selected<?php  } ?>>20</option>
									<option value="50000" <?php  if($dispatch['firstweight']==50000) { ?>selected<?php  } ?>>50</option>
								</select>
								<span class="input-group-addon">KG</span>
							</div>
							<br>
							<div class="input-group  input-medium">
								<span class="input-group-addon">续重重量</span>
								<select name="secondweight" id="secondweight" class='form-control input-medium'>
									<option value="500" <?php  if($dispatch['secondweight']==500) { ?>selected<?php  } ?>>0.5</option>
									<option value="1000" <?php  if($dispatch['secondweight']==1000 || empty($dispatch['secondweight'])) { ?>selected<?php  } ?>>1</option>
									<option value="1200" <?php  if($dispatch['secondweight']==1200) { ?>selected<?php  } ?>>1.2</option>
									<option value="2000" <?php  if($dispatch['secondweight']==2000) { ?>selected<?php  } ?>>2</option>
									<option value="5000" <?php  if($dispatch['secondweight']==5000) { ?>selected<?php  } ?>>5</option>
									<option value="10000" <?php  if($dispatch['secondweight']==10000) { ?>selected<?php  } ?>>10</option>
									<option value="20000" <?php  if($dispatch['secondweight']==20000) { ?>selected<?php  } ?>>20</option>
									<option value="50000" <?php  if($dispatch['secondweight']==50000) { ?>selected<?php  } ?>>50</option>
								</select> <span class="input-group-addon">KG</span>
							</div>
						</div>
					</div>
					
					<div class="form-group nopoint">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">价格设置</label>
						<div class="col-sm-9 col-xs-12">
							<div class="input-group  input-medium">
								<span class="input-group-addon">首重价格</span>
								<input type="text" name="firstprice" id='firstprice' class="form-control input-medium" value="<?php  echo $dispatch['firstprice']; ?>" />
								<span class="input-group-addon">元</span>
							</div>
							<br>
							<div class="input-group  input-medium">
								<span class="input-group-addon">续重价格</span>
								<input type="text" name="secondprice" id='secondprice' class="form-control input-medium" value="<?php  echo $dispatch['secondprice']; ?>" />
								<span class="input-group-addon">元</span>
							</div>
							<span class='help-block'>根据重量来计算运费，当物品不足《首重重量》时，按照《首重费用》计算，超过部分按照《续重重量》和《续重费用》乘积来计算</span>
						</div>						
					</div>
				
				</div>
				
				<div class="form-group" style="margin-top:50px;border-top: 1px dashed #ccc;">
					<label class="checkbox-inline" style="margin-left:65px;">
						<input type="checkbox" name="isdispathpoint[]" id="isdispathpoint" onclick="dispathpointcheck(this)" value="isdispathpoint"  <?php if($dispatch_point['dispid']==-2){?> checked="checked" <?php }?> />是否使用积分方式（如果使用积分,则用户可以通过积分代替运费）
					</label>
				</div>
				<div class="pointdisplay" style="<?php if($dispatch_point['dispid']!=-2){?> display:none; <?php } ?> ">
				<div class="form-group" >
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">
						<input type='radio' name='dispatchpricetype_point' id='simpleradio_point' value='0' <?php  if($dispatch_point['dispatchpricetype']==0) { ?>checked<?php  } ?> /> 简单积分方式
					</label>
				</div>
				<div class="form-group" id="simplepricetype_point">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
					<div class="col-sm-9 col-xs-12">
					<div class="input-group  input-medium ispoint">
						<span class="input-group-addon">积分设置</span>
						<input type="text" name="dispatchprice_point" id='dispatchprice_point' class="form-control input-medium" value="<?php  echo intval($dispatch_point['dispatchprice']); ?>" />
						<span class="input-group-addon">分</span>
					</div>
						<span class='help-block'>无条件的积分设置</span>
					</div>
				</div>				
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">
						<input type='radio' name='dispatchpricetype_point' id='complexradio_point' value='1' <?php  if($dispatch_point['dispatchpricetype']==1) { ?>checked<?php  } ?> /> 复杂积分方式
					</label>
				</div>
				<div id="complexpricetype_point">
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">地区设置</label>
						<div class="col-sm-9 col-xs-12 ispoint">
							<label class="checkbox-inline" style="font-weight:bold;">
								<input type="checkbox" name="chk_all_point" id="chk_all_point" value="0" onclick="check_all(this, 'regions_point[]')" />全选/反选
							</label>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
						<div class="col-sm-9 col-xs-12 ispoint">
							<?php for($i = 0; $i < count($allregions); $i ++) {?>
								<label class="checkbox-inline">
									<?php $in = in_array($allregions[$i],$r_point); ?>
									<input type="checkbox" class="checkpoint_point" name="regions_point[]" id="regions_point[]" value="<?php echo $allregions[$i];?>" <?php if(!empty($in)) {?>checked="checked"<?php }?> /><?php echo $allregions[$i];?>
								</label>
							<?php }?> 
						</div>
						
					</div>					
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">重量设置</label>
						<div class="col-sm-9 col-xs-12">
							<div class="input-group input-medium">
								<span class="input-group-addon">首重重量</span>
								<select name="firstweight_point" id="firstweight_point" class='form-control input-medium'>
									<option value="500" <?php  if($dispatch_point['firstweight']==500) { ?>selected<?php  } ?>>0.5</option>
									<option value="1000" <?php  if($dispatch_point['firstweight']==1000 || empty($dispatch_point['firstweight'])) { ?>selected<?php  } ?>>1</option>
									<option value="1200" <?php  if($dispatch_point['firstweight']==1200) { ?>selected<?php  } ?>>1.2</option>
									<option value="2000" <?php  if($dispatch_point['firstweight']==2000) { ?>selected<?php  } ?>>2</option>
									<option value="5000" <?php  if($dispatch_point['firstweight']==5000) { ?>selected<?php  } ?>>5</option>
									<option value="10000" <?php  if($dispatch_point['firstweight']==10000) { ?>selected<?php  } ?>>10</option>
									<option value="20000" <?php  if($dispatch_point['firstweight']==20000) { ?>selected<?php  } ?>>20</option>
									<option value="50000" <?php  if($dispatch_point['firstweight']==50000) { ?>selected<?php  } ?>>50</option>
								</select>
								<span class="input-group-addon">KG</span>
							</div>
							<br>
							<div class="input-group  input-medium">
								<span class="input-group-addon">续重重量</span>
								<select name="secondweight_point" id="secondweight_point" class='form-control input-medium'>
									<option value="500" <?php  if($dispatch_point['secondweight']==500) { ?>selected<?php  } ?>>0.5</option>
									<option value="1000" <?php  if($dispatch_point['secondweight']==1000 || empty($dispatch_point['secondweight'])) { ?>selected<?php  } ?>>1</option>
									<option value="1200" <?php  if($dispatch_point['secondweight']==1200) { ?>selected<?php  } ?>>1.2</option>
									<option value="2000" <?php  if($dispatch_point['secondweight']==2000) { ?>selected<?php  } ?>>2</option>
									<option value="5000" <?php  if($dispatch_point['secondweight']==5000) { ?>selected<?php  } ?>>5</option>
									<option value="10000" <?php  if($dispatch_point['secondweight']==10000) { ?>selected<?php  } ?>>10</option>
									<option value="20000" <?php  if($dispatch_point['secondweight']==20000) { ?>selected<?php  } ?>>20</option>
									<option value="50000" <?php  if($dispatch_point['secondweight']==50000) { ?>selected<?php  } ?>>50</option>
								</select> <span class="input-group-addon">KG</span>
							</div>
						</div>
					</div>
					
					<div class="form-group ispoint">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">积分设置</label>
						<div class="col-sm-9 col-xs-12">
							<div class="input-group  input-medium">
								<span class="input-group-addon">首重积分</span>
								<input type="text" name="firstprice_point" id='firstprice_point' class="form-control input-medium" value="<?php echo intval($dispatch_point['firstprice']); ?>" />
								<span class="input-group-addon">分</span>
							</div>
							<br>
							<div class="input-group  input-medium">
								<span class="input-group-addon">续重积分</span>
								<input type="text" name="secondprice_point" id='secondprice_point' class="form-control input-medium" value="<?php echo intval($dispatch_point['secondprice']); ?>" />
								<span class="input-group-addon">分</span>
							</div>
							<span class='help-block'>根据重量来计算积分，当物品不足《首重重量》时，按照《首重积分》计算，超过部分按照《续重积分》和《续重积分》乘积来计算</span>
						</div>						
					</div>
				</div>
				</div>
				
			</div>
		</div>
		<div class="form-group col-sm-12">
			<input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1" />
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
		</div>
	</form>
</div>
<script language='javascript'>
	function formcheck(){
		if ($("#dispatchname").isEmpty()){
			alert("请填写配送方式名称!");
			return false;
		}
		//if($('#expressname option:selected').text()=='') {
		//	alert("请选择物流公司！");
		//	return false;
		//}
		if($(':radio[name=dispatchtype]:checked').val()!='2') {
			if($(':radio[name=dispatchpricetype]:checked').val() == '0') {
				if(!$("#dispatchprice").isNumber()) {
					alert("请正确填写运费价格！");
					return false;
				}
			} else {
				if (!$("#firstprice").isNumber()) {
					alert("请正确填写数字首重价格!");
					return false;
				}
				if (!$("#secondprice").isNumber()) {
					alert("请正确填写数字续重价格!");
					return false;
				}
				var isError = true;
				$("input:checkbox[class=checkpoint]:checked").each(function() {
					isError = false;
				});
				if(isError) {
					alert("请选择运费地区！");
					return false;
				}
			}
			
			if($("#isdispathpoint").is(':checked')) {
		
				if($(':radio[name=dispatchpricetype_point]:checked').val() == '0') {
					if(!(/^\+?(0|[1-9][0-9]*)$/.test($("#dispatchprice_point").val()))) {
						alert("请正确填写积分！");
						return false;
					}
				} else {
					if(!(/^\+?(0|[1-9][0-9]*)$/.test($("#firstprice_point").val()))) {
						alert("请正确填写数字首重积分!");
						return false;
					}
					if(!(/^\+?(0|[1-9][0-9]*)$/.test($("#secondprice_point").val()))) {
						alert("请正确填写数字续重积分!");
						return false;
					}
					var isError = true;
					$("input:checkbox[class=checkpoint_point]:checked").each(function() {
						isError = false;
					});
					if(isError) {
						alert("请选择积分地区！");
						return false;
					}
				}
		
			}
		}
		
		return true;
	}
	
	
	$(function() {
		$("#common_corp").change(function() {
			var obj = $(this);
			var sel = obj.find("option:selected");
			$("#dispatch_name").val(sel.attr("data-name"));
			$("#dispatch_url").val(sel.attr("data-url"));
		});
	})
	function dispathpointcheck(obj){
		if($("#isdispathpoint").is(':checked')) {
			$(".pointdisplay").show();
		}else{
			$(".pointdisplay").hide();
		}
	}
	function check_all(obj, rg) {
		var checkboxs = document.getElementsByName(rg);
		for(var i=0;i<checkboxs.length;i++){checkboxs[i].checked = obj.checked;}		
	}
	
	
	
</script>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>