<?php defined('IN_IA') or exit('Access Denied');?>
<?php //include $this -> template('header');?>

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
		<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js"></script>
	    <script src="<?php bloginfo('template_directory'); ?>/js/jquery.form.js" type="text/javascript" ></script>
		<title><?php bloginfo('name'); ?></title>
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
		
		var progress = $(".progress");
		progress.hide();
		
		//显示要上传的文件名
		function showfile()
		{
		    //先清空当前已经选择的视频文件
			$(".filedesp").empty();
		    //判断上传的文件是否符合视频的类型
		    var val= $("#fileupload").val();   
			//获取上传文件名，windows系统和android系统上的路径不同
			var videos = [];
			var videoname = "";
			if(val.indexOf("\\")>=0)
			{
				videos = val.split("\\");
				videoname = videos[videos.length - 1];  //获取上传文件名
			}
			else if(val.indexOf("/")>=0)
			{
				videos = val.split("/");
				videoname = videos[videos.length - 1];  //获取上传文件名
			}
			$(".filedesp").append('<span>'+videoname+'</span>');
		}

		function uploadfile()
		{
		   if(document.getElementById("video_title").value == "")
		   {
		       alert("请输入视频标题");
		   }
		   else
		   {
		        //判断上传的文件是否符合视频的类型
		        var val= $("#fileupload").val();    
				var filext = (val.substr(val.indexOf("."))).toLowerCase();     //获取文件的扩展名全转化为小写
				
				
				if(val == "")
				{
					alert("请先选择视频文件");
				}
		        else if((filext != ".mp4") && (filext != ".avi") && (filext != ".wmv") && (filext != ".rm") && (filext != ".rmvb") && (filext != ".flv") && (filext != ".3gp"))
		        {
				    alert("视频格式不正确，请重新上传视频文件!");
				}
				else
				{
				   
					var bar = $('.bar');
					var percent = $('.percent');
					var showimg = $('#showimg');
					var progress = $(".progress");
					var files = $(".files");
					var btn = $(".btnupload span");
					var loading = $("#loading");
					
					var videokey = $("#video_key");
					var videopkey = $("#video_pkey");
					
					//提交myupload对应的form
					$("#uploadvideo").ajaxSubmit({
						dataType: 'text',
						//dataType:  'text',
						beforeSend: function() {
							showimg.empty();
							progress.show();
							var percentVal = '0%';
							bar.width(percentVal);
							percent.html(percentVal);
							btn.html("上传中...");
							$(".filedesp").hide();
							loading.show();
							
						},
						success: function(data) {
							var result = jQuery.parseJSON(data);
							/* btn.html("上传成功");
							loading.hide();  */
							videokey.val(result.key);
							videopkey.val(result.persistentId);
							
							//提交myupload对应的form
							$("#teacherupload").ajaxSubmit({
								dataType: 'text',
								//dataType:  'text',
								
								success: function(data) {
									//var status = jQuery.parseJSON(data);
									//alert(status.statuscode);
									btn.html("上传成功");
									loading.hide();
									setTimeout(function () {
										alert("您的视频文件待处理成功后将显示在视频列表中");
										//location.href = '<?php echo $this->createMobileUrl('videolist',array('GWEID' => $gweid, 'fromuser' =>$fromuser));?>';
										location.href = '<?php echo $this->createMobileUrl('videolist',array('gweid' => $gweid));?>';
									}, 3000);
									
								},
								error:function(xhr){
									btn.html("上传失败");
									bar.width('0');
									loading.hide();
									//files.html(xhr.responseText);
								}
							});
							
						},
						error:function(xhr){
							btn.html("上传失败");
							bar.width('0');
							loading.hide();
							//files.html(xhr.responseText);
						}
					});
				
				}
		    }
		}
		</script>
	</head>
    <body>
        <!--<div id="maintest" class="main">			
			<div class="main-title">
				<div class="title-1">当前位置：视频管理> <font class="fontpurple">视频上传 </font>
				</div>
			</div>
			<div class="bgimg"></div>-->
		<div class="mobile-div img-rounded">
			<div class="mobile-hd">视频管理> <font class="fontpurple">视频上传</font></div>
			<div class="mobile-content">
			<div id="nav-main" style="margin-left:5%;">				
				<div>    
				<!--<form id="teacherupload" action="<?php //echo $this -> createMobileUrl('VideoCreate',array( 'GWEID' => $_GPC['GWEID'] , 'fromuser' => $_GPC['fromuser'] ))?>" method="post" enctype="multipart/form-data">-->
				<form id="teacherupload" action="<?php echo $this -> createMobileUrl('VideoCreate',array( 'gweid' => $_GPC['gweid']))?>" method="post" enctype="multipart/form-data">
					<table width="95%" height="180" border="0" cellpadding="10px" style="margin-left:0px; margin-top:30px;" id="table2">			
						<tr>
							<td><label for="title">视频标题:</label></td>
							<td width="65%"><input type="text" value="" class="form-control" id="video_title" name="video_title" /></td>
						</tr>
						<tr>
							<td><label for="desp">视频描述:</label></td>
							<td width="65%"><textarea id="video_desp" name="video_desp"  class="form-control" style="height:80px;" ></textarea></td>						
						</tr>
						<tr>
							<td><label for="content">年级/班级:</label></td>		
                            <td><select name="video_gradeclass" class="form-control" size="1" type="text;margin-left:500px;" id="video_gradeclass" value='' onchange='this.options[this.selectedIndex].value' maxlength="20">
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
							<input type="hidden" name="video_key" id="video_key">	
							<input type="hidden" name="video_pkey" id="video_pkey">
						</form>
						<form id="uploadvideo" action="http://up.qiniu.com" method="post" enctype="multipart/form-data">
						<table width="95%" height="50" border="0" cellpadding="10px" style="margin-left:0px; margin-top:0px;" id="table2">
						<tr>
							<!--<td><label for="content">选择文件:</label></td>-->
							<td>
								<div class="upload"> 
									<img id="pic" src="" alt="图片预览" style="display:none;"/><br>
									<div class="btnupload">
										<span>添加视频</span>
										<input id="fileupload" type="file" name="file" onchange="showfile()">
										
									</div>
									<img id="loading" src="<?php bloginfo('template_directory'); ?>/images/loading2.jpg" width="20%" style="margin-left:10%;display:none;">
									<div class="filedesp" style="margin-left:0%;"></div>
									<!--<div class="progress">
									<span class="bar"></span><span class="percent">0%</span >
									
									</div>-->
									<div class="files"></div>
									<div id="showimg"></div>	
                                   	<input type="hidden" name="token" id="token" value="<?php echo $upToken;?>">										
								</div>	
							</td>	
							
						</tr>
					</table>
				</form>
				
			    </div>
				
				<div style="margin-top:3%; margin-left:35%;">
					<input type="button" onclick="uploadfile();" class="btn btn-primary" value="确定" id="checkaccount" style="width:70px">
					
					<!--<input type="button" onclick="location.href='<?php //echo $this -> createMobileUrl('videolist',array( 'GWEID' => $_GPC['GWEID'] , 'fromuser' => $_GPC['fromuser'] ))?>'" class="btn btn-default" value="取消" id="sub3" style="width:70px; margin-left:20px;">-->
					<input type="button" class="btn btn-default" value="取消" id="sub3" style="width:70px; margin-left:20px;">
				</div>
				
			</div>
			</div>
		</div>
		<!--</div>-->
	</body>
</html>
<?php include $this -> template('footer');?>