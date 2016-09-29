<?php defined('IN_IA') or exit('Access Denied');?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common', TEMPLATE_INCLUDEPATH)) : (include template('common', TEMPLATE_INCLUDEPATH));?>
<?php include $this -> template('header');?>
<script src="<?php bloginfo('template_directory'); ?>/js/jquery.form.js"></script>
<script src="<?php bloginfo('template_directory') ?>/js/tinymce/jquery.tinymce.min.js"></script>
<script src="<?php bloginfo('template_directory') ?>/js/tinymce/tinymce.min.js"></script>
<style type="text/css">
.red {float:left;color:red}
.white{float:left;color:#fff}
.tooltipbox {
	background:#fef8dd;border:1px solid #c40808; position:absolute; left:0;top:0; text-align:center;height:20px;
	color:#c40808;padding:2px 5px 1px 5px; border-radius:3px;z-index:1000;
}
.red { float:left;color:red}
</style>
<div class="main-title">
	<div class="title-1">当前位置：<a href="<?php echo $this->createWebUrl('index',array());?>">微商城</a> > <a href="<?php echo $this->createWebUrl('weshoptemselect',array('gweid' => $gweid));?>">微商城基本设置</a> > <a href="<?php echo $this->createWebUrl('weshoppingsiteset',array('gweid' => $gweid,'slid' => $slide));?>">微商城高级设置</a> >
	<font class="fontpurple">小店介绍</font></div>
</div>
<div class="main" style="width:98%">
	<form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" id="form1" style="margin-top:20px" onsubmit='return formcheck()'>
		<div class="panel panel-default">
			<div class="panel-heading">小店信息</div>
			<input type="hidden" name="id" class="form-control" value="<?php  echo $shop['id'];?>" />
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 control-label"><span style="color:red">*</span>小店名称</label>
					<div class="col-sm-10 col-xs-12">
						<input type="text" id="shopname" name="shopname" class="form-control" value="<?php  echo $shop['name'];?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 control-label">官方网址</label>
					<div class="col-sm-10 col-xs-12">
						<input type="text" name="site" class="form-control" value="<?php  echo $shop['site'];?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 control-label">小店图片</label>
					<div class="col-sm-10 col-xs-12">
						<div>
							<img id="pic" style="max-width: 150px; max-height: 150px;" src="<?php echo $shopimg ?>" onerror="this.src='<?php bloginfo('template_directory'); ?>/images/nopic.jpg'; this.title='图片未找到'" class="img-responsive img-thumbnail">
							<em id='picurl' class="close" style="position:absolute; top: 0px; margin-left:9px;display:none;" title="删除这张图片" onclick="delImage()">×</em>
						</div>
						<input type="file" class="form-control" name="file" id="file" onchange="previewImage(this)" style="margin-top:5px;"/>
						<div class="help-block">大图片建议尺寸：宽500像素 * 高400像素 </div>	
						<input name='delimgid' type='hidden' id='delimg_id' value='-1'/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 control-label">联系电话</label>
					<div class="col-sm-10 col-xs-12">
						<input type="text" name="phone" class="form-control" value="<?php  echo $shop['phone'];?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 control-label">联系邮箱</label>
					<div class="col-sm-10 col-xs-12">
						<input type="text" id="noticeemail" name="noticeemail" class="form-control" value="<?php  echo $shop['email'];?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 control-label">所在地址</label>
					<div class="col-sm-10 col-xs-12">
						<input type="text" name="address" class="form-control" value="<?php  echo $shop['address'];?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 control-label">小店介绍</label>
					<div class="col-sm-10 col-xs-12">
						<textarea name="description" class="form-control richtext" id="description"><?php  echo $shopdescription;?></textarea>
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
<?php tinymce_js("#description"); ?>
<script language='javascript'>

function formcheck(){

var emailreg = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/;	
	if($("#shopname").isEmpty()){
		alert("小店名称不能为空，请输入小店名称!");
		return false;
	}
	if($("#noticeemail").val() !="" && !emailreg.test($("#noticeemail").val())){
		alert("您的邮箱格式不正确，请重新输入!");
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
	$(function(){ 
    <?php 
	if(!empty($shopimg)){
	?>
		$("#picurl").show();
	<?php }?> 
}); 
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>