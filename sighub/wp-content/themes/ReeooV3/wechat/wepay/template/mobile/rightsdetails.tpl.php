<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css" />
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/gradeclass.css" />
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/video.css">
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/shopping.mobile.css" />
		<title>添加维权详情</title>
		<style>
			.info{color:#333; margin-left:10px;}
			#file_1{margin-bottom:5px;}
		</style>
	</head>	
<div class="mobile-div img-rounded" style="margin-bottom:15%;">	
<div class="mobile-hd">需要维权的订单</div>
<div style="margin-right:3%;margin-left:3%;">
	<div class="mobile-content">
		<div><label>商品名称:</label><font class="info"><?php if( Empty( $goodsinfos )){echo $rsdetails['description'];} else { foreach($goodsinfos as $ginfo) echo $ginfo['title'];}?></font></div>
		<div><label>交易金额:</label><font class="info"><?php  echo '￥'.$rsdetails['fee'];?></font></div>
		<div><label>交易完成时间:</label><font class="info"><?php echo $rsdetails['time_end']; ?></font></div>
		<div><label>交易单号:</label><font class="info"><?php echo $rsdetails['out_trade_no']; ?></font></div>
	<form id="pictureupload" action="<?php echo $this -> createMobileUrl('rightslists',array( 'gweid' => $gweid,'out_trade_no'=> $rsdetails['out_trade_no'],'goodsgid' => $goodsgid));?>" enctype="multipart/form-data" method="post" onsubmit="return checkinputinfo();">
			<div style="margin-bottom:10px;">
				<label for="reason">维权原因（必填）:</label>
				<select name="reason" class="form-control" size="1" type="text" id="reason" maxlength="30" style="height:34px;">
					<option value="" >请选择</option>
					<option value="1" <?php if($reason == 1) echo 'selected="selected"'; ?>>商品质量有问题</option>
					<option value="2" <?php if($reason == 2) echo 'selected="selected"'; ?>>商品与实际购买不符</option>
					<option value="3" <?php if($reason == 3) echo 'selected="selected"'; ?>>商品发货延迟</option>
					<option value="4" <?php if($reason == 4) echo 'selected="selected"'; ?> >其他原因</option>
				</select>
			</div>
			<div style="margin-bottom:10px;">
				<label for="right_solution">您希望的处理方式（必填）：</label>
				<select name="right_solution" class="form-control" size="1" type="text" id="right_solution" maxlength="30" style="height:34px;">
					<option value="" >请选择</option>
					<option value="1" <?php if($right_solution == 1) echo 'selected="selected"'; ?>>退款退货</option>
					<option value="2" <?php if($right_solution == 2) echo 'selected="selected"'; ?>>退款不退货</option>
				</select>
			</div>
			<div style="margin-bottom:10px;">
				<label for="rights_notes">备注:</label>
				<input type="text" id="rights_notes" class="form-control" name="rights_notes" placeholder="请在50字以内描述相关详情"  value="<?php echo $rights_notes?>" /> 
			</div>
			<div>
				<label for="picurl">上传图片凭证（可上传5张图片）</label>
				<ol id="holder">
					<li id="li_1" style="width:100%; margin-left:-15%;margin-bottom:10px;">
						<input type="file" name="uploadinput[]" class="form-control" id="file_1" multiple onchange="handleFiles(this)" />
						<a href="javascript:void(0)" onClick="delInput(1)">[删除]</a>
						<a href="javascript:void(0)" onClick="addInput()">[增加]</a> 点击增加，最多5个
					</li>
				</ol>	
				<?php
					$picurl = explode(";",$right_picurl);
					foreach($picurl as $purl){
						$upload =wp_upload_dir();
						$url=$upload['baseurl'].$purl;
						if(!Empty($purl)){
					?> 
					<img src='<?php echo $url;?>' width='80' height='80'>
				<?php } }?>				
			</div>
			<div><div id="fileList"></div></div>
		</div>
		<div style="float:right;margin:15px 0;width:100%">
		<input type="submit" class="btn btn-success btn-submit" style="width:100%;" value="提交">
		</div>
	</form>
</div>
<div class="footerbar">
	<a style="width:33%;padding-top:15px" onclick="href='<?php echo $this->createMobileUrl('goodsinfo',array('gweid'=>$gweid,'goodsgid' => $goodsgid));?>'">首页</a>
	<a style="width:33%;padding-top:15px" onclick="href='<?php echo $this->createMobileUrl('myorderlist',array('gweid' => $gweid,'goodsgid' => $goodsgid)); ?>'">我的订单</a>
	<a style="width:33%;padding-top:15px" onclick="href='<?php echo $this->createMobileUrl('rightslists',array('gweid' => $gweid,'goodsgid' => $goodsgid));?>'">我的维权</a>
</div>
<script>
		var addmax = 5;
		var holder = document.getElementById("holder");
		function addInput(){
			
			var total = holder.getElementsByTagName("li").length; 
			if(total >= addmax)
			{return false;}
			var html = '<input type="file" name="uploadinput[]" id="file_{1}" class="form-control" style="border:1px solid #CCC;background-color:#FFF;margin-left:-15%;margin-bottom:5px;" multiple onchange="handleFiles(this)"> <a href="javascript:void(0)" onClick="delInput({1})">[删除]</a>';
			var li = document.createElement("li");
			li.id = "li_" + (total + 1);
			html = html.replace(/\{1\}/g, total + 1);
			li.innerHTML = html;
			holder.appendChild(li);
		}
		function delInput(n){
		var obj = document.getElementById("li_" + n);
		holder.removeChild(obj);
		}
		window.URL = window.URL || window.webkitURL;
				var fileList = document.getElementById("fileList");
			function handleFiles(obj) {
				var files = obj.files,
					img = new Image();
				if(window.URL){
					  img.src = window.URL.createObjectURL(files[0]); //创建一个object URL，并不是你的本地路径
					  img.width = 70;
					  img.onload = function(e) {
						 window.URL.revokeObjectURL(this.src); //图片加载后，释放object URL
					  }
					fileList.appendChild(img);  
				}else if(window.FileReader){
					//opera不支持createObjectURL/revokeObjectURL方法。我们用FileReader对象来处理
					var reader = new FileReader();
					reader.readAsDataURL(files[0]);
					reader.onload = function(e){
						img.src = this.result;
						img.width = 200;
						fileList.appendChild(img);
					}
					
				}else{
					//ie
					obj.select();
					obj.blur();
					var nfile = document.selection.createRange().text;
					document.selection.empty();
					img.src = nfile;
					img.width = 200;
					img.onload=function(){
					}
					fileList.appendChild(img);
			}
		}
	function checkinputinfo(){
		if($('#reason').val()==""){
			alert("请选择维权原因！");
			return false;
		}
		if($('#right_solution').val()==""){
			alert("请选择您希望的处理方式！");
			return false;
		}
	}
</script>
</html>
<?php  include $this -> template('footer');?>