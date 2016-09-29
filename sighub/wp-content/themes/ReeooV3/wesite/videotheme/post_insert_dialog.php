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
			$post_content_array = split("\n",$post_content);
			$post_content_link = $post_content_array[0];
			$post_content_length = $post_content_array[1];
		}
	}


$refreshOpener=$_GET["refreshOpener"];

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
<script charset="utf-8" src="/wp-content/themes/ReeooV3/js/editor/plugins/code/prettify.js"></script>
	<link rel="stylesheet" href="../../css/wsite.css"/>
	<link rel="stylesheet" href="../../css/bootstrap.min.css">
	<script src="../../js/jquery.min.js"></script>
	<script src="../../js/bootstrap.min.js"></script>

	<title>创建新页面</title>
</head>

<body >
<?php echo $htmlData; ?>
<div class="dlg-panel panel panel-info">
  <div class="panel-heading">
    <h3 class="panel-title dlg-title">文章编辑</h3>
  </div>
  <div class="panel-body">
	<form action="<?php echo constant("CONF_THEME_DIR"); ?>/wesite/videotheme/post_insert.php?beIframe&siteId=<?php echo $siteId ?>&artType=<?php echo $artType ?>&refreshOpener=<?php echo $refreshOpener ?>&postid=<?php echo $postid ?>" method="post" enctype="multipart/form-data">
		<table class="gridtable" width="700" height="375" border="0" align="center" style="margin-top:25px;">
			<tr>
				<td>
					<label for="name">视频标题</label>		
					<input type="text" id="post_title" class="form-control" name="post_title" style="width:65%;" value=<?php echo $post_title ?> ></input>
					<!--<img src="..." onclick="my_picktrue()"/>-->
					<br />
				</td>
			</tr>
			<tr>
				<td>
					<label for="name">视频长度</label>		
					<input type="text" id="post_content_length" class="form-control" name="post_content_length" style="width:35%;" value=<?php echo $post_content_length ?> ></input>
					<!--<img src="..." onclick="my_picktrue()"/>-->
					<br />
				</td>
			</tr>
			<tr>
				<td>
					<label for="name">视频链接</label>    <a onclick="javascript:window.open('upload.html','_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')"><strong>上传到优酷</strong></a>		
					<input type="text" id="post_content_link" class="form-control" name="post_content_link"value=<?php echo $post_content_link ?> ></input>
					<!--<img src="..." onclick="my_picktrue()"/>-->
					<br />
				</td>
			</tr>
			<tr>
				<td>
					<div width="150" align="right">
						<input type="submit" class="btn btn-primary" value="确定" style="width:120px;margin-top:25px"/>
						<input type="cancel" class="btn btn-default" onclick="close2()" value="取消" style="width:120px;margin-top:25px"/>
					</div>
				</td>
			</tr>
        </table>		
	</form>
  </div>
</div>
</body>
	
<script language='javascript'>

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
</script>
</html>