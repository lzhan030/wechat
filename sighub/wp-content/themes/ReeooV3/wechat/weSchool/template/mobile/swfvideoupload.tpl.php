<?php defined('IN_IA') or exit('Access Denied');?>
<?php //include $this -> template('header');?>

<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes" />
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/uploadexcel.css">
		<link href="<?php bloginfo('template_directory'); ?>/js/swfuploadjs/default.css" rel="stylesheet" type="text/css" />
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/swfuploadjs/swfupload.js"></script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/swfuploadjs/swfupload.swfobject.js"></script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/swfuploadjs/swfupload.queue.js"></script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/swfuploadjs/fileprogress.js"></script>
        
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
		<style type="text/css">
			a:visited {
				color: #FF00FF
			}
			body {
			    background: #ECECEC;
				font: 16px/1.5 "Microsoft Yahei","微软雅黑",Tahoma,Arial,Helvetica,STHeiti;
			}
			
		</style>	
		<title><?php bloginfo('name'); ?></title>
		<script type="text/javascript">
		
		var swfu;

		SWFUpload.onload = function () {
			var settings = {
				flash_url : "<?php bloginfo('template_directory'); ?>/js/swfuploadjs/swfupload.swf",
				upload_url: "http://up.qiniu.com",
				//upload_url: "<?php echo $this -> createMobileUrl('VideoCreate',array( 'GWEID' => $_GPC['GWEID'] , 'fromuser' => $_GPC['fromuser'] ))?>",
				post_params: {
					"token" : "<?php echo $upToken;?>"
				},
				file_post_name : "file",
				file_size_limit : "100 MB",
				file_types : "*.mp4;*.avi;*.3gp;*.rm;*.rmvb;*.wmv;*.flv",
				file_types_description : "All Files",
				file_upload_limit : 1,  
				file_queue_limit : 1,
				custom_settings : {
					progressTarget : "fsUploadProgress",
					cancelButtonId : "btnCancel"
				},
				debug: false,

				// Button Settings
				button_image_url : "<?php bloginfo('template_directory'); ?>/images/XPButtonUploadText_61x22.png",
				button_placeholder_id : "spanButtonPlaceholder",
				button_width: 120,
				button_height: 34,

				// The event handler functions are defined in handlers.js
				swfupload_loaded_handler : swfUploadLoaded,
				file_queued_handler : fileQueued,
				file_queue_error_handler : fileQueueError,
				file_dialog_complete_handler : fileDialogComplete,
				upload_start_handler : uploadStart,   
				upload_progress_handler : uploadProgress,
				upload_error_handler : uploadError,
				upload_success_handler : uploadSuccess,
				upload_complete_handler : uploadComplete,
				queue_complete_handler : queueComplete,	// Queue plugin event
				
				// SWFObject settings
				minimum_flash_version : "9.0.28",
				swfupload_pre_load_handler : swfUploadPreLoad,
				swfupload_load_failed_handler : swfUploadLoadFailed
			};

			swfu = new SWFUpload(settings);
		}
		
		function uploadSuccess(file, serverData) {
			try {
				var progress = new FileProgress(file, this.customSettings.progressTarget);
				progress.setComplete();
				progress.setStatus("上传成功.");
				progress.toggleCancel(false);
				//alert(serverData);
				//document.getElementById('swfvdupload').submit();
				var videokey = $("#video_key");
		        var videopkey = $("#video_pkey");
				var result = jQuery.parseJSON(serverData);
				
				videokey.val(result.key);
				videopkey.val(result.persistentId); 
				
				$("#swfvdupload").ajaxSubmit({
					dataType: 'text',
					
					success: function(data) {
					   
					   setTimeout(function () {
							alert("您的视频文件待处理成功后将显示在视频列表中");
							//location.href = '<?php echo $this->createMobileUrl('videolist',array('GWEID' => $gweid, 'fromuser' =>$fromuser));?>';
							location.href = '<?php echo $this->createMobileUrl('videolist',array('gweid' => $gweid));?>';
						}, 2000);
									
					},
					error:function(xhr){
						alert("上传失败");
						//files.html(xhr.responseText);
					}
				});
				
				//location.href = "<?php echo $this -> createMobileUrl('videolist',array( 'GWEID' => $_GPC['GWEID'] , 'fromuser' => $_GPC['fromuser'] ))?>";

			} catch (ex) {
				this.debug(ex);
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

			<!--<form id="swfvdupload" action="<?php //echo $this -> createMobileUrl('VideoCreate',array( 'GWEID' => $_GPC['GWEID'] , 'fromuser' => $_GPC['fromuser'] ))?>" method="post" enctype="multipart/form-data">-->
			<form id="swfvdupload" action="<?php echo $this -> createMobileUrl('VideoCreate',array( 'gweid' => $_GPC['gweid']))?>" method="post" enctype="multipart/form-data">
				
			<!--<table width="100%" height="180" border="0" cellpadding="10px" style="margin-left:-15%; margin-top:30px;" id="table2">-->
            <table width="95%" height="180" border="0" cellpadding="10px" style="margin-top:30px;" id="table2">			
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
				<table width="95%" height="50" border="0" cellpadding="10px" style="margin-left:15%; margin-top:0px;" id="tablehide">
					<tr>
						<td>
							<!--<p style="margin-top:25px;">
							<span id="spanButtonPlaceholder"></span>-->
							<input id="btnCancel" type="button" value="Cancel All Uploads" disabled="disabled" style="margin-left: 2px; height: 22px; font-size: 8pt; display:none;" />
							<!--<br />
							</p>-->
						</td>
						<td>
							<!--<div class="fieldset  flash" id="fsUploadProgress">-->
							<div id="fsUploadProgress" style="margin-left:8%;">
							<!--<span class="legend">Upload Queue</span>-->
							</div>
							<!--<p id="divStatus">0 Files Uploaded</p>-->
						</td>
					</tr>
				</table>
				
				<input type="hidden" name="video_key" id="video_key">	
				<input type="hidden" name="video_pkey" id="video_pkey">
				<input type="hidden" name="token" id="token" value="<?php echo $upToken;?>">
				
				<noscript>
					<div class="alert alert-info" style="width:95%; margin-top:-10%;">
						很抱歉，上传控件无法加载。使用该控件需要脚本的支持。正在切换到<span class="label label-important">普通上传</span>模式....
					</div>
				</noscript>
				<div id="divLoadingContent" class="alert alert-success" style="width:95%; display:none; margin-top:-10%;" >
				    正在加载上传控件。请稍等....
				</div>
				<div id="divLongLoading" class="alert alert-info" style="width:95%; display:none; margin-top:-10%;">
				    上传控件加载超时或失败。请确保flash插件正常并且版本最新。正在切换到<span style="font-size:16px;" class="label label-important">普通上传</span>模式....
				</div>
				<div id="divAlternateContent" class="alert alert-danger" style="width:95%; display:none; margin-top:-10%;">
				    很抱歉，上传控件无法加载。您需要安装或者升级flash插件。可以通过访问
					 <a class="alert-link" href="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash">Adobe网站</a>下载安装。正在切换到<span style="font-size:16px;" class="label label-success">普通上传</span>模式....
				</div>
			</form>
	        </div>
				
			<div style="margin-top:3%; margin-left:20%;">
			
				<!--<input type="button" onclick="uploadfile();" class="btn btn-primary" value="选择图片并上传" id="checkaccount" style="width:120px">-->
				<span id="spanButtonPlaceholder"></span>
				<!--<input type="button" onclick="uploadfile();" class="btn btn-primary"  value="选择视频并上传" id="spanButtonPlaceholder" style="width:120px">-->
				
				<!--<input type="button" onclick="location.href='<?php //echo $this -> createMobileUrl('videolist',array( 'GWEID' => $_GPC['GWEID'] , 'fromuser' => $_GPC['fromuser'] ))?>'" class="btn btn-default" value="取消" id="sub3" style="width:70px; margin-left:3%;">-->
				<input type="button" onclick="location.href='<?php echo $this -> createMobileUrl('videolist',array( 'gweid' => $_GPC['gweid']))?>'" class="btn btn-default" value="取消" id="sub3" style="width:70px; margin-left:3%;">
			</div>
			
			</div>
		<!--</div>-->
		</div>
		</div>
		
		<script>
		
		    function swfUploadPreLoad() {
				var self = this;
				var loading = function () {
					//document.getElementById("divSWFUploadUI").style.display = "none";
					document.getElementById("divLoadingContent").style.display = "";

					var longLoad = function () {
						document.getElementById("divLoadingContent").style.display = "none";
						document.getElementById("divLongLoading").style.display = "";
						
						//如果加载控件失败,确定和取消按钮都隐藏
						document.getElementById("spanButtonPlaceholder").style.display = "none";
						document.getElementById("sub3").style.display = "none";
						//10秒后页面跳转
						setTimeout(function () {
								//location.href = "<?php echo $this -> createMobileUrl('videoupload',array( 'GWEID' => $gweid , 'fromuser' => $fromuser ))?>";
								location.href = "<?php echo $this -> createMobileUrl('videoupload',array( 'gweid' => $gweid))?>";
							},
							6 * 1000
						);
					};
					this.customSettings.loadingTimeout = setTimeout(function () {
							longLoad.call(self)
						},
						15 * 1000
					);
				};
				
				this.customSettings.loadingTimeout = setTimeout(function () {
						loading.call(self);
					},
					1*1000
				);
			}
			function swfUploadLoaded() {
				var self = this;
				clearTimeout(this.customSettings.loadingTimeout);
				//document.getElementById("divSWFUploadUI").style.visibility = "visible";
				//document.getElementById("divSWFUploadUI").style.display = "block";
				document.getElementById("divLoadingContent").style.display = "none";
				document.getElementById("divLongLoading").style.display = "none";
				document.getElementById("divAlternateContent").style.display = "none";
				
				//document.getElementById("btnBrowse").onclick = function () { self.selectFiles(); };
				document.getElementById("btnCancel").onclick = function () { self.cancelQueue(); };
			}
			   
			function swfUploadLoadFailed() {
				clearTimeout(this.customSettings.loadingTimeout);
				//document.getElementById("divSWFUploadUI").style.display = "none";
				document.getElementById("divLoadingContent").style.display = "none";
				document.getElementById("divLongLoading").style.display = "none";
				document.getElementById("divAlternateContent").style.display = "";
				
				//如果加载控件失败,确定和取消按钮都隐藏
				document.getElementById("spanButtonPlaceholder").style.display = "none";
				document.getElementById("sub3").style.display = "none";
				//10秒后页面跳转
				setTimeout(function () {
						//location.href = "<?php echo $this -> createMobileUrl('videoupload',array( 'GWEID' => $gweid , 'fromuser' => $fromuser ))?>";
						location.href = "<?php echo $this -> createMobileUrl('videoupload',array( 'gweid' => $gweid ))?>";
					},
					6 * 1000
				);
				
			}
			   
			   
			function fileQueued(file) {
				try {
					var progress = new FileProgress(file, this.customSettings.progressTarget);
					progress.setStatus("等待上传...");
					progress.toggleCancel(true, this);

				} catch (ex) {
					this.debug(ex);
				}

			}

			function fileQueueError(file, errorCode, message) {
				try {
					if (errorCode === SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED) {
						//alert("You have attempted to queue too many files.\n" + (message === 0 ? "You have reached the upload limit." : "You may select " + (message > 1 ? "up to " + message + " files." : "one file.")));
						alert("您一次只能上传一个文件");
						return;
					}

					var progress = new FileProgress(file, this.customSettings.progressTarget);
					progress.setError();
					progress.toggleCancel(false);

					switch (errorCode) {
					case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
						progress.setStatus("文件太大.");
						this.debug("错误码: 文件太大, 文件名: " + file.name + ", 文件大小: " + file.size + ", 消息: " + message);
						break;
					case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
						progress.setStatus("不能上传0字节文件.");
						this.debug("错误码: 0字节文件, 文件名: " + file.name + ",文件大小: " + file.size + ", Message: " + message);
						break;
					case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
						progress.setStatus("无效的文件类型.");
						this.debug("错误码: 无效的文件类型, 文件名: " + file.name + ", 文件大小: " + file.size + ", 消息: " + message);
						break;
					default:
						if (file !== null) {
							progress.setStatus("未处理的错误");
						}
						this.debug("错误码: " + errorCode + ", 文件名: " + file.name + ", 文件大小: " + file.size + ", 消息: " + message);
						break;
					}
				} catch (ex) {
					this.debug(ex);
				}
			}

			function fileDialogComplete(numFilesSelected, numFilesQueued) {
				try {
					if (numFilesSelected > 0) {
						document.getElementById(this.customSettings.cancelButtonId).disabled = false;
					}
					
					/* I want auto start the upload and I can do that here */
					this.startUpload();
				} catch (ex)  {
					this.debug(ex);
				}
			}

			//普通上传开始 uploadStart
			function uploadStart(file) {
				try {
					/* I don't want to do any file validation or anything,  I'll just update the UI and
					return true to indicate that the upload should start.
					It's important to update the UI here because in Linux no uploadProgress events are called. The best
					we can do is say we are uploading.
					 */
					if($("#video_title").val() == "")
					{
					   alert("视频标题不能为空");
					   window.location.reload();
					}
					else
					{
						/* swfu.addPostParam("video_title", $("#video_title").val());
						swfu.addPostParam("video_desp", $("#video_desp").val());
						swfu.addPostParam("video_gradeclass", $("#video_gradeclass").val()); */
						$("#fsUploadProgress").empty(); 
						var progress = new FileProgress(file, this.customSettings.progressTarget);
						progress.setStatus("上传中...");
						progress.toggleCancel(true, this);
					}
				}
				catch (ex) {}
				
				return true;
			}

			function uploadProgress(file, bytesLoaded, bytesTotal) {
				try {
					var percent = Math.ceil((bytesLoaded / bytesTotal) * 100);

					var progress = new FileProgress(file, this.customSettings.progressTarget);
					progress.setProgress(percent);
					progress.setStatus("上传中...");
				} catch (ex) {
					this.debug(ex);
				}
			}

			function uploadError(file, errorCode, message) {
				try {
					var progress = new FileProgress(file, this.customSettings.progressTarget);
					progress.setError();
					progress.toggleCancel(false);

					switch (errorCode) {
					case SWFUpload.UPLOAD_ERROR.HTTP_ERROR:
						progress.setStatus("上传错误: " + message);
						this.debug("错误码: HTTP错误, 文件名: " + file.name + ", 消息: " + message);
						break;
					case SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED:
						progress.setStatus("上传失败.");
						this.debug("错误码: 上传失败, 文件名: " + file.name + ", 文件大小: " + file.size + ", 消息: " + message);
						break;
					case SWFUpload.UPLOAD_ERROR.IO_ERROR:
						progress.setStatus("服务器 (IO) 错误");
						this.debug("错误码: IO错误, 文件名: " + file.name + ", 消息: " + message);
						break;
					case SWFUpload.UPLOAD_ERROR.SECURITY_ERROR:
						progress.setStatus("安全错误");
						this.debug("错误码: 安全错误, 文件名: " + file.name + ", 消息: " + message);
						break;
					case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
						progress.setStatus("上传超过限制.");
						this.debug("错误码: 上传超过限制, 文件名: " + file.name + ", 文件大小: " + file.size + ", 消息: " + message);
						break;
					case SWFUpload.UPLOAD_ERROR.FILE_VALIDATION_FAILED:
						progress.setStatus("验证失败.  跳过上传.");
						this.debug("错误码: 文件验证失败, 文件名: " + file.name + ", 文件大小: " + file.size + ", 消息: " + message);
						break;
					case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
						// If there aren't any files left (they were all cancelled) disable the cancel button
						if (this.getStats().files_queued === 0) {
							document.getElementById(this.customSettings.cancelButtonId).disabled = true;
						}
						progress.setStatus("取消");
						progress.setCancelled();
						break;
					case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
						progress.setStatus("停止");
						break;
					default:
						progress.setStatus("未处理的错误: " + errorCode);
						this.debug("错误码: " + errorCode + ", 文件名: " + file.name + ", 文件大小: " + file.size + ", 消息: " + message);
						break;
					}
				} catch (ex) {
					this.debug(ex);
				}
			}

			function uploadComplete(file) {
				if (this.getStats().files_queued === 0) {
					document.getElementById(this.customSettings.cancelButtonId).disabled = true;
				}
			}

			// This event comes from the Queue Plugin
			function queueComplete(numFilesUploaded) {
				//var status = document.getElementById("divStatus");
				//status.innerHTML = numFilesUploaded + " file" + (numFilesUploaded === 1 ? "" : "s") + " uploaded.";
			}

		
		</script>
	</body>
</html>
<?php include $this -> template('footer');?>