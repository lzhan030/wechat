<?php defined('IN_IA') or exit('Access Denied');?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common', TEMPLATE_INCLUDEPATH)) : (include template('common', TEMPLATE_INCLUDEPATH));?>
<?php include $this -> template('header');?>
<div class="main-title">
	<div class="title-1">当前位置：<a href="<?php echo $this->createWebUrl('index',array());?>">微商城</a> > <a href="<?php echo $this->createWebUrl('weshoptemselect',array('gweid' => $gweid));?>">微商城基本设置</a> > <a href="<?php echo $this->createWebUrl('weshoppingsiteset',array('gweid' => $gweid,'slid' => $slide));?>">微商城高级设置</a> >
	<font class="fontpurple">幻灯片</font></div>
</div>
<div class="shop_content">
<ul class="nav nav-tabs" style="margin-top:23px;margin-right:11px;">
	<li <?php  if($operation == 'display') { ?> class="active" <?php  } ?>><a href="<?php  echo $this->createWebUrl('adv',array('op' =>'display'))?>">幻灯片</a></li>
	<li <?php  if(empty($adv['id']) && $operation == 'post') { ?> class="active" <?php  } ?>><a href="<?php  echo $this->createWebUrl('adv',array('op' =>'post'))?>">添加幻灯片</a></li>
	<?php  if(!empty($adv['id']) &&  $operation == 'post') { ?><li  class="active"><a href="<?php  echo $this->createWebUrl('adv',array('op' =>'post','id'=>$adv['id']))?>">编辑幻灯片</a></li><?php  } ?>
</ul>
<?php  if($operation == 'display') { ?>
	<div class="main panel panel-default" style="height: auto;">
		<div class="panel-heading">幻灯片列表</div>
		<div class="panel-body table-responsive">
			<table class="table table-hover" style="min-width:800px;">
				<thead>
					<tr>
						<th style="width:50px;">编号</th>
						<th style="width:100px;">显示顺序</th>					
						<th style="width:100px;">标题</th>
						<th style="width:300px;">链接</th>
						<th style="width:150px;" >操作</th>
					</tr>
				</thead>
				<tbody>
					<?php  if(is_array($list)) { foreach($list as $item) { ?>
					<tr>
						<td><?php  echo $item['id'];?></td>
						<td><?php  echo $item['displayorder'];?></td>
						<td><?php  echo $item['advname'];?></td>
						<td><input type="text" class="form-control" value="<?php  echo $item['link'];?>" readonly="readonly" /></td>
						<td style="text-align:left;">
							<a href="<?php  echo $this->createWebUrl('adv', array('op' => 'delete', 'id' => $item['id']))?>" onclick="return confirm('确认删除此幻灯片吗？');return false;" class="btn btn-warning btn-sm"  data-placement="bottom" >删除</a>
							<a href="<?php  echo $this->createWebUrl('adv', array('op' => 'post', 'id' => $item['id']))?>" class="btn btn-info btn-sm"  data-placement="bottom" >修改</a>
						</td>
					</tr>
					<?php  } } ?>
				</tbody>
			</table>
			<?php  echo $pager;?>
		</div>
	</div>
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
<?php  } else if($operation == 'post') { ?>

<div class="main" style="width:98%">
	<form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" onsubmit='return formcheck()'>
		<input type="hidden" name="id" value="<?php  echo $adv['id'];?>" />
		<div class="panel panel-default">
			<div class="panel-heading">
				幻灯片设置
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">排序</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="displayorder" class="form-control" value="<?php  echo $adv['displayorder'];?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red">*</span>幻灯片标题</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" id='advname' name="advname" class="form-control" value="<?php  echo $adv['advname'];?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">幻灯片图片</label>
					<div class="col-sm-9 col-xs-12">
						<div>
							<img id="pic" style="max-width: 150px; max-height: 150px;" src="<?php echo $advthumb ?>" onerror="this.src='<?php bloginfo('template_directory'); ?>/images/nopic.jpg'; this.title='图片未找到'" class="img-responsive img-thumbnail">
							<em id='picurl' class="close" style="position:absolute; top: 0px; margin-left:9px;display:none;" title="删除这张图片" onclick="delImage()">×</em>
						</div>
						<input type="file" class="form-control" name="file" id="file" onchange="previewImage(this)" style="margin-top:5px;"/>
						<div class="help-block">大图片建议尺寸：宽500像素 * 高300像素 </div>	
						<input name='delimgid' type='hidden' id='delimg_id' value='-1'/>
					</div>
				</div>
				 <div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">幻灯片链接</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="link" class="form-control" value="<?php  echo $adv['link'];?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">是否显示</label>
					<div class="col-sm-9 col-xs-12">
						<label class='radio-inline'>
							<input type='radio' name='enabled' value=1' <?php  if($adv['enabled']==1) { ?>checked<?php  } ?> /> 是
						</label>
						<label class='radio-inline'>
							<input type='radio' name='enabled' value=0' <?php  if($adv['enabled']==0) { ?>checked<?php  } ?> /> 否
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
<?php  } ?>
<script type="text/javascript">
function formcheck(){
	if($("#advname").isEmpty()){
		alert("幻灯片标题不能为空，请输入标题!");
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
		document.getElementById("isrecommendorder").readOnly=true;
	}else{
		document.getElementById("isrecommendorder").readOnly=false;
	}
	
}	
$(function(){ 
    <?php 
	if(!empty($advthumb)){
	?>
		$("#picurl").show();
	<?php }?> 
}); 
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>