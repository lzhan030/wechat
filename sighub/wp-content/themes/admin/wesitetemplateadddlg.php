<?php
	require_once './wp-content/themes/ReeooV3/wesite/common/dbaccessor.php';
	require_once './wp-content/themes/ReeooV3/wesite/common/random.php';

	global $wpdb;
	$id = $_GET['id'];
?>

<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css" />
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/init.css">
<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js"></script>
<script src="<?php bloginfo('template_directory'); ?>/js/bootstrap.min.js"></script>
<style>
	.uploadbtn{position:absolute;top:0;filter:alpha(opacity=0);-moz-opacity:0;opacity:0;}
	.row{height:50px;}
	.submit{width:120px;float:right;}
	.floatright{float:right;}
</style>
<div>
	<div class="main-title">
		<div class="title-1">当前位置：模板管理 > <font class="fontpurple">添加微官网模板 </font>
		</div>
	</div>
	<form name="myForm" id="wesitetmpadd" action="?admin&page=cgi-bin/wesite_template_add" method="post" onsubmit="return validationform();" enctype="multipart/form-data">
	<div style="width: 80%; margin-top:50px; margin-left:30px;">	
		<div class="row clearfix">
			<div class="col-xs-3 col-md-3 column"><label class="floatright"><font color="red">* </font>模板名称：</label></div>
			<div class="col-xs-9 col-md-9 column"><input class="form-control" type="text" name="title" /></div>
		</div>	
		<div class="row clearfix">
			<div class="col-xs-3 col-md-3 column"><label class="floatright">是否启用：</label></div>
			<div class="col-xs-2 col-md-2 column">
				<input type="radio" name="activate" value="1" checked="checked" > 启用
			</div>
			<div class="col-xs-7 col-md-7 column">
				<input type="radio" name="activate" value="0" > 禁止
			</div>
		</div>	
		<div class="row clearfix">
			<div class="col-xs-3 col-md-3 column"><label class="floatright">是否有背景图片：</label></div>
			<div class="col-xs-2 col-md-2 column">
				<input type="radio" name="background" value="1" checked="checked" > 有
			</div>
			<div class="col-xs-7 col-md-7 column">
				<input type="radio" name="background" value="0" > 无
			</div>
		</div>
		<div class="row clearfix">
			<div class="col-xs-3 col-md-3 column"><label class="floatright">是否支持幻灯片：</label></div>
			<div class="col-xs-2 col-md-2 column">
				<input type="radio" name="slide" value="1" checked="checked" > 支持
			</div>
			<div class="col-xs-7 col-md-7 column">
				<input type="radio" name="slide" value="0" > 不支持
			</div>
		</div>	
		<div class="row clearfix">
			<div class="col-xs-3 col-md-3 column"><label class="floatright">是否支持菜单：</label></div>
			<div class="col-xs-2 col-md-2 column">
				<input type="radio" name="menu" value="1" checked="checked" > 支持
			</div>
			<div class="col-xs-7 col-md-7 column">
				<input type="radio" name="menu" value="0" > 不支持
			</div>
		</div>	
		<div class="row clearfix">
			<div class="col-xs-3 col-md-3 column"><label class="floatright">是否支持菜单背景颜色：</label></div>
			<div class="col-xs-2 col-md-2 column">
				<input type="radio" name="menu_bg" value="1" > 支持
			</div>
			<div class="col-xs-7 col-md-7 column">
				<input type="radio" name="menu_bg" value="0" checked="checked" > 不支持
			</div>
		</div>			
		<div class="row clearfix">
			<div class="col-xs-3 col-md-3 column"><label class="floatright">是否支持菜单图片：</label></div>
			<div class="col-xs-2 col-md-2 column">
				<input type="radio" name="image_icon" value="1" checked="checked" > 支持
			</div>
			<div class="col-xs-7 col-md-7 column">
				<input type="radio" name="image_icon" value="0" > 不支持
			</div>
		</div>					
		<div class="row clearfix">
			<div class="col-xs-3 col-md-3 column"><label class="floatright"><font color="red">* </font>模板代码的zip文件：</label></div>
			<div class="col-xs-6 col-md-6 column"><input class="form-control" type="text" name="txt" id="filename" disabled="disabled" /></div>
			<div class="col-xs-3 col-md-3 column">
				<button class="btn btn-default">请选择上传的文件</button>
				<input type="file" name="uploadfile" id="uploadfile" onchange="changeupload(this)" class="uploadbtn" accept=".zip" />
			</div>	
		</div>
		<div class="row clearfix help-block">
			<div class="col-xs-3 col-md-3 column"></div>
			<div class="col-xs-9 col-md-9 column">
				注：上传的zip文件中，请至少包含index.tpl.php和preview.jpg两个文件。<br/>
				其中，index.tpl.php是微官网的网页文件，preview.jpg为网页模板的缩略图。在两个文件的同级目录下，生成zip文件，zip文件的文件名请不要含有特殊字符。同时请确保文件中没有svn相关文件。
			</div>
		</div>
		<div class="row clearfix"></div>
		<div class="row clearfix">
			<div class="col-xs-12 col-md-12 column">
				<input type="submit" class="btn btn-primary submit" value="提交" />
			</div>
		</div>
	</div>
	</form>
</div>
<script>
	function validationform()
	{
		if(checknull(document.myForm.uploadfile, "上传模板代码不能为空！") == true) {
			return false;
		}
		if(checknull(document.myForm.title, "模板名称不能为空！")==true) {
			return false;
		}
		return true;
	}
	function checknull(obj, warning) {
		if(obj.value == "") {
			alert(warning);
			return true;
		}
		return false;
	}
	function changeupload(e){
		var uploadfile = e.value.replace(/^.*\\/, "");
		document.myForm.txt.value = uploadfile;
	}
</script>