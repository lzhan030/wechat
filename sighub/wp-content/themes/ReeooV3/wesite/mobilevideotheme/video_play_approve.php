<?php

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
			$post_status=$postinfo->post_status;
		}
	}
$post_content=web_admin_post_content($post_content);

$refreshOpener=$_GET["refreshOpener"];
$addAttachment=$_GET["addAttachment"];

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
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/video-js.min.css">
<!-- 加载 VideoJS js -->
<script src="<?php bloginfo('template_directory'); ?>/js/videojs/video.js" type="text/javascript" charset="utf-8"></script>

<!-- 皮肤 -->
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/video-js.min.css" type="text/css" media="screen" title="Video JS">
 <script>
	videojs.options.flash.swf = "video-js.swf";
 </script>     
<title>视频内容</title>
</head>

<body >
<?php echo $htmlData; ?>
<div class="dlg-panel panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title dlg-title">视频内容</h3>
  </div>
  <div class="panel-body">
	<?php $videourl=$wpdb->get_var($wpdb->prepare("SELECT url FROM {$wpdb->prefix}video WHERE persistentId = %s",$post_content));/*the_content();*/ ?>
		<video id="example_video_1" class="video-js vjs-default-skin" controls preload="none" width="100%" height="264"  src="<?php echo $videourl;?>" 
		data-setup="{}" >
		<source src="<?php echo $videourl;?>" type='video/mp4' />
		<track kind="captions" src="demo.captions.vtt" srclang="en" label="English"></track><!-- Tracks need an ending tag thanks to IE9 -->
		<track kind="subtitles" src="demo.captions.vtt" srclang="en" label="English"></track><!-- Tracks need an ending tag thanks to IE9 -->
		</video>
		<div align="center" style="margin-top: 40px;">
			<?php if($post_status == 'pending') {?>
			<a type="button"  name="stickyp" id="stickyp" style="width:70px;" class="btn btn-sm btn-info" href="javascript:approvePost('<?php echo $postid;?>')">审核通过</a>
			<?php }else{?>
			<a type="button"  name="stickyp" id="stickyp" style="width:125px;" class="btn btn-sm btn-info disabled">已通过，无需审核</a>
			<?php }?>
			<a  type="button" name="stickyp" id="stickyp" style="width:70px;" class="btn btn-sm btn-warning" href="javascript:deletePost('<?php echo $postid;?>')">删除</a>
		</div>
  </div>
</div>
</body>
	
<script language='javascript'>
	var xmlHttp;
    function createXMLHttpRequest(){
    if(window.ActiveXObject)
     xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
    else if(window.XMLHttpRequest)
     xmlHttp = new XMLHttpRequest();
    }
	function deletePost(ID){	   
		if(confirm("确定删除吗？")){
			createXMLHttpRequest();
			xmlHttp.open("GET","<?php echo constant("CONF_THEME_DIR"); ?>/wesite/mobilevideotheme/post_delete.php?beIframe&header=0&footer=0&postid="+ID,true);
			xmlHttp.onreadystatechange = function(){
				if(xmlHttp.readyState == 4 && xmlHttp.status == 200)
				{    
				   //alert("删除成功");
				   alert(xmlHttp.responseText);	
				   opener.location.reload();
				   close2();
				}	
				
			}
			xmlHttp.send(null);
		}
	}
	function approvePost(ID){	   
		if(confirm("确定审核通过吗？")){
			createXMLHttpRequest();
			xmlHttp.open("GET","<?php echo constant("CONF_THEME_DIR"); ?>/wesite/mobilevideotheme/post_approve.php?beIframe&header=0&footer=0&postid="+ID,true);
			xmlHttp.onreadystatechange = function(){
				if(xmlHttp.readyState == 4 && xmlHttp.status == 200)
				{    
				   //alert("删除成功");
				   alert(xmlHttp.responseText);	
				   opener.location.reload();
				   close2();
				}	
				
			}
			xmlHttp.send(null);
		}
	}
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
				
	function close2(){
		window.opener=null;
		setTimeout("self.close()",0);
	}
		
		
		</script>
</html>