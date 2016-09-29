<?php
session_start();
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');

include '../common/dbaccessor.php';
include '../common/web_constant.php';

$siteId=$_GET["siteId"];
$artType=$_GET["artType"];

//通过menu的url获取id
$menuiUrl=$_GET['menuiUrl'];
 if($menuiUrl!=null){
	$explode=explode('=', $menuiUrl);
	$postid=$explode[1];
}else{
  $postid=$_GET["postid"];//获取文章的postid
}  
	if(web_admin_post_exist($postid)){
		$post=web_admin_get_post($postid);
		foreach($post as $postinfo){
			$post_title= $postinfo->post_title;
			$post_content=$postinfo->post_content;
		}
	}
$post_content=web_admin_post_content($post_content);

$refreshOpener=$_GET["refreshOpener"];
$addAttachment=$_GET["addAttachment"];

//qiniu video upload
require_once '../common/qiniu/rs.php';
$bucket = "wevideo";
$key = "pic.jpg";
$accessKey = 'BnEuL9EBya39evSshr9Z5uUZYdWaElRZlDuC1c7b';
$secretKey = 'kQntsPFbLqaQLDEN_dOBm3c8VUiyrVIylkNBq__b';

Qiniu_SetKeys($accessKey, $secretKey);

$putPolicy = new Qiniu_RS_PutPolicy($bucket);
$putPolicy -> Expires = 3600*24;
$putPolicy -> PersistentOps = 'avthumb/mp4';

$putPolicy -> PersistentNotifyUrl=home_url().'/wp-content/themes/ReeooV3/wesite/common/video_transcode_callback.php';
$upToken = $putPolicy->Token(null);
?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/js/editor/themes/default/default.css" />
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/js/editor/plugins/code/prettify.css" />
<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js"></script>
<script charset="utf-8" src="<?php bloginfo('template_directory'); ?>/js/editor/kindeditor.js"></script>
<script charset="utf-8" src="<?php bloginfo('template_directory'); ?>/js/editor/lang/zh_CN.js"></script>
<script charset="utf-8" src="<?php bloginfo('template_directory'); ?>/js/editor/plugins/code/prettify.js"></script>
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/video.css">

<link rel="stylesheet" href="../../css/wsite.css"/>
<link rel="stylesheet" href="../../css/bootstrap.min.css">
<script src="../../js/jquery.min.js"></script>
<script src="../../js/bootstrap.min.js"></script>
<script src="<?php bloginfo('template_directory'); ?>/js/jquery.form.js" type="text/javascript" ></script>
<link href="<?php bloginfo('template_directory'); ?>/js/swfuploadjs/default.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/swfuploadjs/swfupload.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/swfuploadjs/swfupload.swfobject.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/swfuploadjs/swfupload.queue.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/swfuploadjs/fileprogress.js"></script>
<title>创建新页面</title>
</head>

<body >
<?php echo $htmlData; ?>
<div class="dlg-panel panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title dlg-title">视频编辑</h3>
  </div>
  <div class="panel-body">
	
		<table class="gridtable" width="450" height="200" border="0" align="center" style="margin-left: 190px; margin-top:50px">
			<tr>
				<td style="padding-bottom: 15px;width: 100px;">
					<label for="name">视频标题</label>	
					<!--<form name ="content" onSubmit="return validateform()" action="<?php echo constant("CONF_THEME_DIR"); ?>/wesite/mobilevideotheme/post_insert.php?beIframe&siteId=<?php echo $siteId ?>&artType=<?php echo $artType ?>&refreshOpener=<?php echo $refreshOpener ?>&addAttachment=<?php echo $addAttachment ?>&postid=<?php echo $postid ?>" method="post" enctype="multipart/form-data">
						<input type="text" id="name" name="post_title" value="<?php echo $post_title ?>" ></input>
					<input type="hidden" name="video_key" id="video_key">	
					<input type="hidden" name="video_pkey" id="video_pkey">
					<input type="hidden" name="token" id="token" value="<?php echo $upToken;?>">
					<div id="fsUploadProgress" style="margin-left:8%;"></div>
					</form>
					<br />-->
				</td>
				<td>
					<form name ="content" onSubmit="return validateform()" action="<?php echo constant("CONF_THEME_DIR"); ?>/wesite/mobilevideotheme/post_insert.php?beIframe&siteId=<?php echo $siteId ?>&artType=<?php echo $artType ?>&refreshOpener=<?php echo $refreshOpener ?>&addAttachment=<?php echo $addAttachment ?>&postid=<?php echo $postid ?>" method="post" enctype="multipart/form-data">
						<input type="text" id="name" name="post_title" value="<?php echo $post_title ?>" style="width: 220px;"></input>
						<input type="hidden" name="video_key" id="video_key">	
						<input type="hidden" name="video_pkey" id="video_pkey">
						<input type="hidden" name="token" id="token" value="<?php echo $upToken;?>">
						<!--<div id="fsUploadProgress" style="margin-left:8%;"></div>-->
					</form>
					<br />
				</td>
			</tr>
			<tr>
			    <td width="80px"></td>
				<td><div id="fsUploadProgress" style=""></div></td>
			</tr>
			
        </table>
		<noscript>
			<div class="alert alert-info" style="width:95%; margin-top:-10%;">
				很抱歉，上传控件无法加载。使用该控件需要脚本的支持。正在切换到<span style="font-size:16px;" class="label label-important">普通上传</span>模式....
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
			 <a class="alert-link" href="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash">Adobe网站</a>下载安装。正在切换到<span class="label label-success">普通上传</span>模式....
		</div>
		<div style="margin-top:3%; margin-left:230px;">
	
			<!--<input type="button" onclick="uploadfile();" class="btn btn-primary" value="选择图片并上传" id="checkaccount" style="width:120px">-->
			<span id="spanButtonPlaceholder"></span>
			<!--<input type="button" onclick="uploadfile();" class="btn btn-primary"  value="选择视频并上传" id="spanButtonPlaceholder" style="width:120px">-->

			<input type="button" onclick="location.href=''" class="btn btn-default" value="取消" id="btnCancel" style="width:70px; margin-left:3%;">
		</div>
  </div>
</div>
</body>
	
<script language='javascript'>

	//提交时，判断内容是否为空
	function checknull(obj, warning){
	  if (obj.value == "") {
		alert(warning);
		obj.focus();
		return true;
	  }
	return false;
	}

	function validateform(){
	  if (checknull(document.content.name, "请填写视频标题!") == true) {
		return false;
	  }
	  return true; 
	}

					
	function my_picktrue(obj) { 
		editor1.clickToolbar("image");  
	}
	
	function close2(){
		window.opener=null;
		setTimeout("self.close()",0);
	}
</script>
<script>
var swfu;

SWFUpload.onload = function () {
	var settings = {
		flash_url : "<?php bloginfo('template_directory'); ?>/js/swfuploadjs/swfupload.swf",
		upload_url: "http://up.qiniu.com",
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
		
		alert("您的视频文件待处理成功<?php if(web_admin_get_site_resource($siteId, "mobilethemeNeedApproval","false")=='true'){ echo "并审核通过"; }?>后，会显示在视频列表中");
		$("form[name='content']").submit();

	} catch (ex) {
		this.debug(ex);
	}
}

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
						//10秒后页面跳转
			setTimeout(function () {
					location.href = location.href.replace('_fast','_normal');;
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
	//document.getElementById("sub3").style.display = "none";
	//10秒后页面跳转
	setTimeout(function () {
			location.href = location.href.replace('_fast','_normal');
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
</html>