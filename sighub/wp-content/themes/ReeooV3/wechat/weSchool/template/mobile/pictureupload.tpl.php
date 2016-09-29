<?php defined('IN_IA') or exit('Access Denied');?>

<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=3.0, user-scalable=yes" />
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/uploadexcel.css">
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/we7/style/font-awesome.css" />
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/we7/style/common.css?v=<?php echo TIMESTAMP;?>" />
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/video.css">
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/we7/script/jquery-1.7.2.min.js"></script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/we7/script/bootstrap.js"></script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/we7/script/common.js?v=<?php echo TIMESTAMP;?>"></script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/we7/script/emotions.js"></script>
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite_mobile.css">
		<style type="text/css">
			a:visited {
				color: #FF00FF
			}
			body {
			    background: #ECECEC;
				font: 16px/1.5 "Microsoft Yahei","微软雅黑",Tahoma,Arial,Helvetica,STHeiti;
			}
			
		</style>	
		<script>
		
		function uploadfile()
		{
		    if(document.getElementById("picture_title").value == "")
		    {
		       alert("请输入图片标题");
			   return false;
		    }
			else if(document.getElementById("fileupload").value == "")
			{
			    alert("请选择图片上传");
			    return false; 
			}
		    else
		    {
			    //判断上传的文件是否符合视频的类型
		        var val= $("#fileupload").val();  
				var hasd = val.indexOf(".");			
				if(hasd >=0)	
				{				
					var filext = (val.substr(hasd)).toLowerCase();     //获取文件的扩展名全转化为小写
					
					if((filext != ".gif") && (filext != ".jpg") && (filext != ".png") && (filext != ".jpeg"))
					{
						alert("图片格式不正确，请重新上传图片!");
						return false; 
					}
					else
					{
						document.getElementById('pictureupload').submit();
						return true;
					}
					
				}
				else
				{
				    document.getElementById('pictureupload').submit();
					return true;
				}
		    }
		}
		
		function previewImage(file){  	
			var picsrc = document.getElementById('pic');  
			if (file.files && file.files[0]){ //chrome 
					var reader = new FileReader();
						reader.readAsDataURL(file.files[0]);  
						reader.onload = function(ev){
							picsrc.src = ev.target.result;
							$("#pic").show();
							
						}   																		
			}else{
				//IE下，使用滤镜 出现问题
				picsrc.style.maxwidth="50px";
				picsrc.style.maxheight = "12px";
				picsrc.style.overflow="hidden";
				var picUpload = document.getElementById('picpath'); 
				picUpload.select();
				var imgSrc = document.selection.createRange().text;  
				picsrc.filters.item("DXImageTransform.Microsoft.AlphaImageLoader").src = imgSrc;
				picsrc.style.filter="progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale,src=\""+imgSrc+"\")";		
			}                    
		}  
		</script>
	<title><?php bloginfo('name'); ?></title>
	
	</head>
    <body>
        <!--<div id="maintest" class="main">		
			<div class="main-title">
				<div class="title-1">当前位置：图片管理> <font class="fontpurple">图片上传 </font>
				</div>
			</div>
			<div class="bgimg"></div>-->
	<div class="mobile-div img-rounded">
		<div class="mobile-hd">图片管理> <font class="fontpurple">图片上传</font></div>
		<div class="mobile-content">
			<div id="nav-main" style="margin-left:5%;">				
				<div>    
				<!--<form id="pictureupload" action="<?php //echo $this -> createMobileUrl('PictureCreate',array( 'GWEID' => $_GPC['GWEID'] , 'fromuser' => $_GPC['fromuser'] ))?>" method="post" enctype="multipart/form-data" onsubmit="return uploadfile();">-->
				<form id="pictureupload" action="<?php echo $this -> createMobileUrl('PictureCreate',array( 'gweid' => $_GPC['gweid']))?>" method="post" enctype="multipart/form-data" onsubmit="return uploadfile();">
					<table width="95%" height="180" border="0" cellpadding="10px" style="margin-left:0px; margin-top:30px;" id="table2">			
						<tr>
							<td><label for="title">图片标题:</label></td>
							<td width="65%"><input type="text" value="" class="form-control" id="picture_title" name="picture_title" /></td>
						</tr>
						<tr>
							<td><label for="desp">图片描述:</label></td>
							<td width="65%"><textarea id="picture_desp" name="picture_desp"  class="form-control" style="height:80px;" ></textarea></td>						
						</tr>
						<tr>
							<td><label for="content">年级/班级:</label></td>		
                            <td><select name="picture_gradeclass" class="form-control" size="1" type="text;margin-left:500px;" id="picture_gradeclass" value='' onchange='this.options[this.selectedIndex].value' maxlength="20">
									<?php
										echo "<option value='*'>所有年级</option>";
										foreach($all_g as $allg)
										{
										    if($teacher_gc == $allg['allgrade']){
												echo "<option value='".$allg['allgrade']."' selected='selected'>".$allg['allgrade']."年级所有班级</option>";
											}else{
												echo "<option value='".$allg['allgrade']."'>".$allg['allgrade']."年级所有班级</option>";
											} 
										}
										foreach($all_gc as $allgc){
											if($teacher_gc == $allgc['tea_gradeclass']){
												echo "<option value='".$allgc['tea_gradeclass']."' selected='selected' >".$allgc['tea_gradeclass']."</option>";
											}else{
												echo "<option value='".$allgc['tea_gradeclass']."' >".$allgc['tea_gradeclass']."</option>";
											}
										}
										
									?>
							    </select>
							</td>							
						</tr>
						</table>
						<table width="95%" height="50" border="0" cellpadding="10px" style="margin-left:0px; margin-top:0px;" id="table2">
							<tr>
								<!--<td><label for="content">选择文件:</label></td>-->
								<td>
									<div class="upload"> 
										<!--<img id="pic" src="" alt="图片预览" width="100" height="80"/><br>-->
										<br><br>
										<div class="btnupload">
											<span>添加图片</span>
											<!--<input id="fileupload" type="file" name="file" multiple accept="image/*" onchange="previewImage(this)">-->
											<input id="fileupload" type="file" name="file" multiple 
											onchange="handleFiles(this)">
											
										</div>							
									</div>	
								</td>	
								<!--<td align="center">-->
								<td>
								   <!-- <img id="pic" src="" alt="图片预览" width="120" height="80"/><br>-->
								   <div id="fileList" style="margin-left:90px;"></div>
									 <script>
										window.URL = window.URL || window.webkitURL;
										var fileupload = document.getElementById("fileupload"),
											fileList = document.getElementById("fileList");
										function handleFiles(obj) {
										    fileList.innerHTML="";  //如果已经选择过文件先清空
											var files = obj.files,
												img = new Image();
											if(window.URL){
												//File API
												  //alert(files[0].name + "," + files[0].size + " bytes");
												  img.src = window.URL.createObjectURL(files[0]); //创建一个object URL，并不是你的本地路径
												  img.width = 70;
												  img.onload = function(e) {
													 window.URL.revokeObjectURL(this.src); //图片加载后，释放object URL
												  }
												  
												  fileList.appendChild(img);//如果不清空，可以添加多张图片
											}else if(window.FileReader){
												//opera不支持createObjectURL/revokeObjectURL方法。我们用FileReader对象来处理
												var reader = new FileReader();
												reader.readAsDataURL(files[0]);
												reader.onload = function(e){
													//alert(files[0].name + "," +e.total + " bytes");
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
												  //alert(nfile+","+img.fileSize + " bytes");
												}
												
												fileList.appendChild(img);
												//fileList.style.filter="progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod='image',src='"+nfile+"')";
											}
										}
									</script>
								</td>
							</tr>
					    </table>
						
						<div style="margin-top:3%; margin-left:35%;">
						<input type="submit" class="btn btn-primary" value="确定" id="checkaccount" style="width:70px">
						
						<!--<input type="button" onclick="location.href='<?php //echo $this -> createMobileUrl('videolist',array( 'GWEID' => $_GPC['GWEID'] , 'fromuser' => $_GPC['fromuser'] ))?>'" class="btn btn-default" value="取消" id="sub3" style="width:70px; margin-left:20px;">-->
						<input type="button" onclick="location.href='<?php echo $this -> createMobileUrl('videolist',array( 'gweid' => $_GPC['gweid']))?>'" class="btn btn-default" value="取消" id="sub3" style="width:70px; margin-left:20px;">
						</div>
				</form>
				
			    </div>
				
				</div>
			</div>
		</div>
	<!--</div>-->
	</body>
</html>
<?php include $this -> template('footer');?>