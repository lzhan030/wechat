<?php

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
global $wpdb;
include '../ReeooV3/wesite/common/dbaccessor.php';
include '../ReeooV3/wesite/common/web_constant.php';

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

$gweid =  $_GET['gweid'];

$mid =$_SESSION['gmid'][intval($gweid)]['mid'];
$auth =	$_SESSION['gmid'][intval($gweid)]['auth'];

/**
*@function:封装gweid
*/
if(!empty($siteId)){
	$site=web_admin_get_site($siteId);
	foreach($site as $siteinfo){
		$userid=$siteinfo->site_user;
		$gweid=$siteinfo->GWEID;
		$gweidt=$siteinfo->GWEID;
	}

	//20150417 sara new added
	//根据当前的gweid去查找有没有处在共享虚拟号下，如果是虚拟号下的，需要将gweid换为虚拟号的gweid
	$gweid = virtualgweid_open($gweid);
	
	$mid =$_SESSION['gmid'][intval($gweid)]['mid'];
	$auth =	$_SESSION['gmid'][intval($gweid)]['auth'];
}
/**
*@function:判断会员是否审核
*/
$vipauditinfo=web_admin_usechat_info_group($gweid);
foreach($vipauditinfo as $vaudit){
	$vipaudit=$vaudit->wechat_vipaudit;
}
/*获取fromuser*/
$fromuser=$_SESSION['gopenid'][intval($gweid)];

if(empty($mid))
	header("Location: ".home_url().'/wp-content/themes/ReeooV3/wesite/common/vip_login.php?gweid='.$gweid.'&redirect_url='.urlencode('http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"]).'&alert='.urlencode('登录后才能上传视频，请先登录。'));
$nickname = $wpdb -> get_var("SELECT nickname FROM {$wpdb->prefix}wechat_member WHERE mid='{$mid}'");

//qiniu video upload
require_once '../ReeooV3/wesite/common/qiniu/rs.php';
//$bucket = "wevideo";
$bucket = web_admin_get_site_resource($siteId, "qiniu_bucket","");
//$accessKey = 'BnEuL9EBya39evSshr9Z5uUZYdWaElRZlDuC1c7b';
$accessKey = web_admin_get_site_resource($siteId, "qiniu_access","");
//$secretKey = 'kQntsPFbLqaQLDEN_dOBm3c8VUiyrVIylkNBq__b';
$secretKey = web_admin_get_site_resource($siteId, "qiniu_secret","");

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
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=3.0, user-scalable=yes"> 
<meta content="application/xhtml+xml;charset=UTF-8" http-equiv="Content-Type">
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/../ReeooV3/css/video.css">
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/../ReeooV3/css/wsite.css"/>
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/../ReeooV3/css/bootstrap.min.css">
<script src="<?php bloginfo('template_directory'); ?>/../ReeooV3/js/jquery.min.js"></script>
<script src="<?php bloginfo('template_directory'); ?>/../ReeooV3/js/bootstrap.min.js"></script>
<script src="<?php bloginfo('template_directory'); ?>/../ReeooV3/js/jquery.form.js" type="text/javascript" ></script>

<title>视频上传</title>
</head>

<body >
<?php echo $htmlData; ?>
<div class="mobile-div img-rounded">
    <div class="mobile-hd"><font class="fontpurple">视频上传</font></div>
    <div class="mobile-content">
		<div id="nav-main" style="">		
			<table class="gridtable" width="100%" height="150" border="0" cellpadding="10px" style="margin-top:25px;">
				<form name ="content" onSubmit="return validateform()" method="post" enctype="multipart/form-data" id="commentform">
				<tr>
					<td style="min-width:80px;">
						<label for="name" style="line-height:50px;height:50px">视频标题</label>		
					</td>
					<td style="">
						<input type="text" id="name" class="form-control" name="post_title" value="<?php echo $post_title ?>" style="width:100%;"></input>
						<input type="hidden" name="video_key" id="video_key">	
						<input type="hidden" name="video_pkey" id="video_pkey">
					</td>
				</tr>
				<tr>
					<td style="min-width:80px;">
						<label for="name" style="line-height:40px;height:50px">作者姓名</label>		
					</td>
					<td>
						<?php if(($selCheck['wechatvip']==1)&&(($isShowVipmember == 'true')||(!empty($memberinfo)))){?>
								<p>微信昵称:<?php echo $nickname ;?></p>
								<p style="display: none">			   
								   <input class="author form-control" style="width: 100%;" type="text" value="<?php echo $nickname ;?>" name="blogAuthor" id="blogAuthor" size="22" tabindex="1" readonly="readonly"/><label for="author"><small><?php //if ($req) echo "(必填)"; ?></small></label>
								</p>
							<?php }else{?>
									<?php if(($selCheck['wechatvip']==1)&&(($isShowVipmember_editor == 'true')||(!empty($memberinfo)))){?>
									<p>
										会员姓名（只有会员登陆可进行发表）
									</p>
									<?php }else{?>
										<p <?php if(($selCheck['wechatvip']==1)&&(($isShowVipmember_editor == 'true')||(!empty($memberinfo)))){?> style="display: none" <?php }?> >
										<input class="author form-control" type="text" value="<?php echo $nickname;?>" name="blogAuthor" id="blogAuthor" size="22" tabindex="1" style="width: 100%;" readonly="readonly"/><label for="author"><small><?php //if ($req) echo "(必填)"; ?></small></label>
										</p>
									<?php }
								} ?>
					</td>
				</tr>
				</form>
				<tr>
					<td style="min-width:80px;">
						<label for="name" style="line-height:50px;">上传视频</label>		
					</td>
					<td>
						<form id="uploadvideo" action="http://up.qiniu.com" method="post" enctype="multipart/form-data">
							<input id="fileupload" type="file" name="file" onchange="showfile()" style="width:100%;">
							<!--<img id="loading" src="<?php bloginfo('template_directory'); ?>/../ReeooV3/images/loading2.jpg" width="20%" style="margin-left:10%;display:none;">-->
							<div class="files"></div>
							<div id="showimg"></div>
							<input type="hidden" name="token" id="token" value="<?php echo $upToken;?>">
						</form>
					</td>
				</tr>
				<tr>
					<td style="min-width:80px;height:76px">			
					</td>
					<td>
						<img id="loading" src="<?php bloginfo('template_directory'); ?>/../ReeooV3/images/loading2.jpg" width="20%" style="margin-left:10%;display:none;">
					</td>
				</tr>
			</table>		
			<div style="margin-left:90px;">
				<input type="submit" class="btn btn-primary" value="确定" style="width:70px;margin-top:25px;" onclick="uploadfile()"/>
				<input type="button" class="btn btn-default" value="取消" onclick="location.href='<?php echo home_url();?>/?site=<?php echo $siteId;?>'" style="width:70px;margin-top:25px;margin-left:20px;"/>
			</div>
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
	  if (checknull(document.content.name, "请填写文章标题!") == true) {
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
							alert("您的视频文件待处理成功后将显示在视频列表中");
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
<script type="text/javascript">
	
		$(function(){
			isSubmitting = false;
			var actionparm="mobiletheme";
			var ajax_option={			
				url:"<?php bloginfo('template_directory'); ?>/../mobilevideotheme/mobile_post.php?action="+actionparm+"&gweid=<?php echo $gweidt;?>"+"&siteId=<?php echo $siteId;?>"+"&redirect_url=<?php echo urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])?>#wechat_redirect",
				success: function(data){
					if (data.status == 'insertsuc'){
						location.reload();
					}else if (data.status == 'success'){
						alert(data.message);						
						location.href="<?php bloginfo('template_directory'); ?>"+data.url;
					}else if (data.status == 'error'){
						alert(data.message);
					}else{
						alert("出现错误");
					}
					isSubmitting = false;
				},
		       error: function(data){
					console.info(data);
					alert("出现错误了");
					isSubmitting = false;
				},
				dataType: 'json'
			}
			$('#commentform').submit(function(){
				//$(this).ajaxSubmit(ajax_option);
				$.post("<?php bloginfo('template_directory'); ?>/../mobilevideotheme/mobile_post.php?action="+actionparm+"&gweid=<?php echo $gweidt;?>"+"&siteId=<?php echo $siteId;?>"+"&redirect_url=<?php echo urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])?>#wechat_redirect",
				$('#commentform').serialize(),
				function(data){
					if (data.status == 'insertsuc'){
						location.href="<?php echo home_url();?>/?site=<?php echo $_GET['siteId'];?>";
					}else if (data.status == 'success'){
						alert(data.message);						
						location.href="<?php bloginfo('template_directory'); ?>"+data.url;
					}else if (data.status == 'error'){
						alert(data.message);
					}else{
						alert("出现错误");
					}
				},'json');
				return false;
			});
		});
</script>
</html>