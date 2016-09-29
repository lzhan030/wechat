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

<title>创建新页面</title>
</head>

<body >
<?php echo $htmlData; ?>
<div class="dlg-panel panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title dlg-title">视频编辑</h3>
  </div>
  <div class="panel-body">
	
		<table class="gridtable" width="500" height="245" border="0" align="center" style="margin-top:50px;margin-left:180px">
			<tr>
				<td style="padding-bottom: 15px;width:100px;height:100px">
					<label for="name">文章标题</label>	
					<!--<form name ="content" onSubmit="return validateform()" action="<?php echo constant("CONF_THEME_DIR"); ?>/wesite/mobilevideotheme/post_insert.php?beIframe&siteId=<?php echo $siteId ?>&artType=<?php echo $artType ?>&refreshOpener=<?php echo $refreshOpener ?>&addAttachment=<?php echo $addAttachment ?>&postid=<?php echo $postid ?>" method="post" enctype="multipart/form-data">
						<input type="text" id="name" name="post_title" value="<?php echo $post_title ?>" ></input>
						<input type="hidden" name="video_key" id="video_key">	
						<input type="hidden" name="video_pkey" id="video_pkey">
					</form>
					<br />-->
				</td>
				<td>
					<form name ="content" onSubmit="return validateform()" action="<?php echo constant("CONF_THEME_DIR"); ?>/wesite/mobilevideotheme/post_insert.php?beIframe&siteId=<?php echo $siteId ?>&artType=<?php echo $artType ?>&refreshOpener=<?php echo $refreshOpener ?>&addAttachment=<?php echo $addAttachment ?>&postid=<?php echo $postid ?>" method="post" enctype="multipart/form-data">
						<input type="text" id="name" name="post_title" value="<?php echo $post_title ?>" style="width:220px;"></input>
						<input type="hidden" name="video_key" id="video_key">	
						<input type="hidden" name="video_pkey" id="video_pkey">
					</form>
					<br />
				</td>
			</tr>
			<tr>
			    <td style="width: 100px;height:50px"></td>
				<td>
					<form id="uploadvideo" action="http://up.qiniu.com" method="post" enctype="multipart/form-data">
						<input id="fileupload" type="file" name="file" onchange="showfile()">
						<!--<img id="loading" src="<?php bloginfo('template_directory'); ?>/images/loading2.jpg" width="20%" style="margin-left:10%;display:none;">-->
						<div class="files"></div>
						<div id="showimg"></div>
						<input type="hidden" name="token" id="token" value="<?php echo $upToken;?>">
					</form>
				</td>
			</tr>
			<tr>
			    <td style="width: 100px;"></td>
				<td>
					<img id="loading" src="<?php bloginfo('template_directory'); ?>/images/loading2.jpg" width="20%" style="margin-left:10%;display:none;">	
				</td>
			</tr>
			<!--<tr>
				<td>
					<div width="150" align="right">
						<input type="submit" class="btn btn-primary" value="确定" style="width:120px;margin-top:25px" onclick="uploadfile()"/>
						<input type="cancel" class="btn btn-default" value="取消" onclick="close2()" style="width:120px;margin-top:25px"/>
					</div>
				</td>
			</tr>-->
        </table>		
	    <div width="150" style="margin-left:280px">
			<input type="submit" class="btn btn-primary" value="确定" style="width:80px;margin-top:25px" onclick="uploadfile()"/>
			<input type="cancel" class="btn btn-default" value="取消" onclick="close2()" style="width:80px;margin-top:25px;margin-left:15px;"/>
		</div>
  </div>
</div>
</body>
	
<script language='javascript'>

	//提交时，判断内容是否为空
	function checknull(obj, warning)
	{
	  if (obj.value == "") {
		alert(warning);
		obj.focus();
		return true;
	  }
	return false;
	}

	function validateform()
	{
	  if (checknull(document.content.name, "请填写视频标题!") == true) {
		return false;
	  }
	  return true; 
	}

	KindEditor.ready(function(K) {
			//var editor1 = K.create('textarea[name="content1"]', {
			window.editor1 = K.create('textarea[name="content1"]', {
				cssPath : '<?php bloginfo('template_directory'); ?>/js/editor/plugins/code/prettify.css',
				uploadJson : '<?php bloginfo('template_directory'); ?>/js/editor/php/sae_upload_json.php',
				fileManagerJson : '<?php bloginfo('template_directory'); ?>/js/editor/php/sae_file_manager_json.php',
				allowFileManager : true,
				afterCreate : function() {
					var self = this;
					K.ctrl(document, 13, function() {
						self.sync();
						K('form[name=example]')[0].submit();
					});
					K.ctrl(self.edit.doc, 13, function() {
						self.sync();
						K('form[name=example]')[0].submit();
					});
				}
			});
			
			prettyPrint();
		});
				
	function my_picktrue(obj) { 
		editor1.clickToolbar("image");  
	}
	
	function close2(){
		window.opener=null;
		setTimeout("self.close()",0);
	}
		
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
		   if($('#name').val() == "")
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
				}else
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
							alert("您的视频文件待处理成功<?php if(web_admin_get_site_resource($siteId, "mobilethemeNeedApproval","false")=='true'){ echo "并审核通过"; }?>后，会显示在视频列表中");
							$('form[name="content"]').submit();
							
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
</html>