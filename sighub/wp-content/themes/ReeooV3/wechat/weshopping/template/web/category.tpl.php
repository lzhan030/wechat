<?php defined('IN_IA') or exit('Access Denied');?>
<?php //(!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common', TEMPLATE_INCLUDEPATH)) : (include template('common', TEMPLATE_INCLUDEPATH));?>
<?php include $this -> template('header');?>

<div class="main-title">
	<div class="title-1">当前位置：<a href="<?php echo $this->createWebUrl('index',array());?>">微商城</a> > <font class="fontpurple">分类管理</font></div>
</div>
<div class="shop_content">
<ul class="nav nav-tabs">
	<li <?php  if($operation == 'display') { ?>  class="active"  <?php  } ?>><a href="<?php  echo $this->createWebUrl('category', array('op' => 'display'))?>">管理分类</a></li>
	<li <?php  if($operation == 'post' && empty($category['id'])) { ?> class="active"  <?php  } ?>><a href="<?php  echo $this->createWebUrl('category', array('op' => 'post','parentid' => $parentid))?>">添加分类</a></li>
	<li <?php  if($operation == 'post' && !empty($category['id'])) { ?>  class="active"><a href="<?php  echo $this->createWebUrl('category', array('op' => 'post','id' => $category['id'],'parentid' => $parentid))?>">编辑分类</a></li><?php  } ?>
</ul>
</div>
<script>
	require(['bootstrap'],function($){
		$('.btn').hover(function(){
			$(this).tooltip('show');
		},function(){
			$(this).tooltip('hide');
		});
	});
</script>
<!--CATEGORY POST-->
<?php  if($operation == 'post') { ?>

<div class="main" style="width:98%">
	<form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" id="form1" onsubmit='return fcheck()'>
		<div class="panel panel-default">
			<div class="panel-heading">
				商品分类
			</div>
			<div class="panel-body">
				<?php  if(!empty($parentid)) { ?>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">上级分类</label>
					<div class="col-sm-9 col-xs-12 control-label" style="text-align:left;"><?php  echo $parent['name'];?></div>
				</div>
				<?php  } ?>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">排序</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" id="displayorder" name="displayorder" class="form-control" value="<?php  echo $category['displayorder'];?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red">*</span>分类名称</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" id="catename" name="catename" class="form-control" value="<?php  echo $category['name'];?>" />
					</div>
				</div>

				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">分类图片</label>
					<div class="col-sm-9 col-xs-12">
						<div>
							<img id="pic" style="max-width: 150px; max-height: 150px;" src="<?php echo $cathumb ?>" onerror="this.src='<?php bloginfo('template_directory'); ?>/images/nopic.jpg'; this.title='图片未找到'" class="img-responsive img-thumbnail">
							<em id='picurl' class="close" style="position:absolute; top: 0px; margin-left:9px;display:none;" title="删除这张图片" onclick="delImage()">×</em>
						</div>
						<input name='delimgid' type='hidden' id='delimg_id' value='-1'/>
						<input type="file" class="form-control" name="file" id="file" onchange="previewImage(this)" style="margin-top:5px;"/>
						<div class="help-block">图片建议尺寸：宽50像素 * 高50像素 </div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">分类描述</label>
					<div class="col-sm-9 col-xs-12">
						<textarea name="description" class="form-control" cols="70"><?php  echo $category['description'];?></textarea>
					</div>
				</div>
				<?php  if(empty($parentid)) { ?>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">首页显示</label>
					<div class="col-sm-9 col-xs-12">
						<label class='radio-inline'>
							<input type='radio' name='indexenabled' value='1' <?php  if($category['indexenabled']!='0') { ?>checked<?php  } ?> /> 是
						</label>
						<label class='radio-inline'>
							<input type='radio' name='indexenabled' value='0' <?php  if($category['indexenabled']=='0') { ?>checked<?php  } ?> /> 否
						</label>
						<span class="help-block"> 若首页显示设为“是”，则该分类会显示在商城首页上。</span>
					</div>
				</div>
				<?php  } ?>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">首页推荐</label>
					<div class="col-sm-9 col-xs-12">
						 <label class='radio-inline'>
							 <input type='radio' name='isrecommend' value='1' onclick="setrecommend($(this),value)" <?php  if($category['isrecommend']==1) { ?>checked<?php  } ?> /> 是
						 </label>
						 <label class='radio-inline'>
							 <input type='radio' name='isrecommend' value='0' onclick="setrecommend($(this),value)" <?php  if($category['isrecommend']==0) { ?>checked<?php  } ?> /> 否
						 </label>
						<span class="help-block"> 若首页推荐设为“是”，则该分类下的商品会显示在商城首页上。</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">首页排序</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" id="isrecommendorder"  name="isrecommendorder" class="form-control" <?php if($category['isrecommend']==0) echo 'readonly = "readonly "'?> value="<?php  echo $category['isrecommendorder'];?>" />
					</div>
				</div>
				
				 <div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">是否显示</label>
					<div class="col-sm-9 col-xs-12">
						<label class='radio-inline'>
							<input type='radio' name='enabled' value='1' <?php  if($category['enabled']!='0') { ?>checked<?php  } ?> /> 是
						</label>
						<label class='radio-inline'>
							<input type='radio' name='enabled' value='0' <?php  if($category['enabled']=='0') { ?>checked<?php  } ?> /> 否
						</label>
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
<!--CATEGORY POST END-->
<!--CATEGORY MANAGEMENT-->
<?php  } else if($operation == 'display') { ?>
<div class="main" style="width:98%">
	<div class="category" style="width:101%">
		<form action="" method="post" onsubmit="return formcheck(this)">
			<div class="panel panel-default">
				<div class="panel-body table-responsive">
					<table class="table table-hover">
						<thead>
							<tr>
								<th style="width:10px;"></th>
								<th style="width:80px;">显示顺序</th>
								<th style="width:150px;">分类名称</th>
								<th style="width:50px;">状态</th>
								<th style="width:60px;">首页状态</th>
								<th style="width:220px;">访问链接</th>
								<th style="width:80px;">操作</th>
							</tr>
						</thead>
						<tbody>
						<?php  if(is_array($category)) { foreach($category as $row) { 
						
									/*处理分类图片显示*/
									$upload =wp_upload_dir();
									if((empty($row['thumb']))||(stristr($row['thumb'],"http")!==false)){
										$categorythumb=$row['thumb'];
									}else{
										$categorythumb=$upload['baseurl'].$row['thumb'];
									}
						?>
						<tr>
							<td><?php  if(count($children[$row['id']]) > 0) { ?><a href="javascript:;"><i class="fa fa-chevron-down"></i></a><?php  } ?></td>
							<td><input type="text" class="form-control" name="displayorder[<?php  echo $row['id'];?>]" value="<?php  echo $row['displayorder'];?>"></td>
							<td>
								<!--<img src="<?php echo $categorythumb ?>" width='50' height="50" onerror="$(this).remove()" style='padding:1px;border: 1px solid #ccc;float:left;' />-->
								<div class="type-parent"><?php  echo $row['name'];?>&nbsp;&nbsp;
									<?php  if(empty($row['parentid'])) { ?>
									<a href="<?php  echo $this->createWebUrl('category', array('parentid' => $row['id'], 'op' => 'post'))?>"><i class="fa fa-plus-circle"></i> 添加子分类</a><?php  } ?>
								</div>
							</td>
							<td>
								<?php  if($row['enabled']==1) { ?>
								<span class='label label-success'>显示</span>
								<?php  } else { ?>
								<span class='label label-danger'>隐藏</span>
								<?php  } ?>
							</td>
							<td>
								<?php  if(empty($row['parentid'])) { ?>
									<?php  if($row['indexenabled']==1) { ?>
									<span class='label label-success'>显示</span>
									<?php  } else { ?>
									<span class='label label-danger'>隐藏</span>
								<?php  }} ?>
							</td>
							<td><input type="text" class="form-control" readonly="readonly"  value="<?php echo $this->createMobileUrl('list2',array('ccate' => '0','pcate' => $row['id'],'gweid'=>$gweid)); ?>"></td>
							<td>
								<a href="<?php  echo $this->createWebUrl('category', array('op' => 'delete', 'id' => $row['id']))?>" onclick="return confirm('确认删除此分类吗？');return false;" class="btn btn-warning btn-sm" data-toggle="tooltip" data-placement="bottom" >删除</a>
								<a href="<?php  echo $this->createWebUrl('category', array('op' => 'post', 'id' => $row['id']))?>" class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="bottom">编辑</a>&nbsp;&nbsp;
							</td>
						</tr>
						<?php  if(is_array($children[$row['id']])) { foreach($children[$row['id']] as $row) {
									
									/*处理分类图片显示*/
									$upload =wp_upload_dir();
									if((empty($row['thumb']))||(stristr($row['thumb'],"http")!==false)){
										$subcategorythumb=$row['thumb'];
									}else{
										$subcategorythumb=$upload['baseurl'].$row['thumb'];
									}
						?>
						<tr>
							<td></td>
							<td><input type="text" class="form-control" name="displayorder[<?php  echo $row['id'];?>]" value="<?php  echo $row['displayorder'];?>"></td>
							<td>
								<!--<img src="<?php echo $subcategorythumb ?>" width='50' height="50" onerror="$(this).remove()" style='padding:1px;border: 1px solid #ccc;float:left;' />-->
								<div class="type-child" style="padding-left:55px;background:url('<?php bloginfo('template_directory'); ?>/images/bg_repno.gif') no-repeat -248px -550px;">
									<?php  echo $row['name'];?>&nbsp;&nbsp;
								</div>
							</td>
							<td>
								<?php  if($row['enabled']==1) { ?>
								<span class='label label-success'>显示</span>
								<?php  } else { ?>
								<span class='label label-danger'>隐藏</span>
								<?php  } ?>
							</td>
							<td>
								
							</td>
							<td><input type="text" class="form-control" readonly="readonly"   value="<?php echo $this->createMobileUrl('list2',array('ccate' => $row['id'],'gweid'=>$gweid)); ?>"></td>
							<td>
								<a href="<?php  echo $this->createWebUrl('category', array('op' => 'delete', 'id' => $row['id'],'parentid' => $row['parentid']))?>" onclick="return confirm('确认删除此分类吗？');return false;" class="btn btn-warning btn-sm" data-toggle="tooltip" data-placement="bottom">删除</i></a>
								<a href="<?php  echo $this->createWebUrl('category', array('op' => 'post', 'id' => $row['id'],'parentid' => $row['parentid']))?>" class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="bottom">编辑</a>&nbsp;&nbsp;
							</td>
						</tr>
						<?php  } } ?>
						<?php  } } ?>
						<tr>
							<td></td>
							<td colspan="5">
								<a href="<?php  echo $this->createWebUrl('category', array('op' => 'post'))?>"><i class="fa fa-plus-sign-alt"></i> 添加新分类</a>
							</td>
						</tr>
						<tr>
							<td></td>
							<td colspan="4">
								<input name="submit" type="submit" class="btn btn-primary" value="提交">
								<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
							</td>
						</tr>
						</tbody>
					</table>
				</div>
			</div>
		</form>
	</div>
</div>
<?php  } ?>
<!--CATEGORY MANAGEMENT END-->
<script type="text/javascript">
function fcheck(){
	if(($("#displayorder").val()!="") && (!$("#displayorder").isNumber())) {
		alert("请填写数字排序！");
		return false;
	}
	if ($("#catename").isEmpty()){
		alert("请填写分类名称!");
		return false;
	}
	if($(':radio[name=isrecommend]:checked').val()=='1') {
		if(($("#isrecommendorder").val()!="") && (!$("#isrecommendorder").isNumber())) {
			alert("请填写数字首页排序！");
			return false;
		}
	}
	
	return true;
}
function formcheck(){
	var isError = true;
	$("input[name^='displayorder']").each(function() {
		if(($(this).val()!="") && (!$(this).isNumber())){
			isError = false;
		}
	});
	if(!isError) {
		alert("请输入数字显示顺序");
		return false;
	}
	return true;
}
function previewImage(file){  
    $("#picurl").show();
    document.getElementById("delimg_id").value="";   //是否更新图片
	var picsrc = document.getElementById('pic');  
	if (file.files && file.files[0]) {//chrome   
			var reader = new FileReader();
			reader.readAsDataURL(file.files[0]);  
			reader.onload = function(ev){
			picsrc.src = ev.target.result;
			$("#pic").show();
		}   
	
	}  else{
		//IE下，使用滤镜 出现问题
		picsrc.style.maxwidth="50px";
		picsrc.style.maxheight = "12px";
		picsrc.style.overflow="hidden";
		var picUpload = document.getElementById('file'); 
		picUpload.select();
		var imgSrc = document.selection.createRange().text;  
		picsrc.filters.item("DXImageTransform.Microsoft.AlphaImageLoader").src = imgSrc;
		picsrc.style.filter="progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale,src=\""+imgSrc+"\")";
	}                    
}  
function delImage(){	  
	$("#pic").attr('src',""); 
	$("#picurl").css('display','none');//$("#picurl").hide();
	document.getElementById("delimg_id").value=-2;
	document.getElementById("file").value="";  //清空file input的内容
}
function setrecommend(e,value){
	if(value==0){
		$("#isrecommendorder").attr("readonly",true);
	}else{
		$("#isrecommendorder").attr("readonly",false);
	}	
}	
$(function(){ 
    <?php 
	if(!empty($cathumb)){
	?>
		$("#picurl").show();
	<?php }?> 
}); 
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>
