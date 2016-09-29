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
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery-1.11.1.min.js"></script>
	<link rel="stylesheet" href="../../css/wsite.css"/>
	<link rel="stylesheet" href="../../css/bootstrap.min.css">
	<script src="../../js/bootstrap.min.js"></script>
	<script src="<?php bloginfo('template_directory') ?>/js/tinymce/jquery.tinymce.min.js"></script>
<script src="<?php bloginfo('template_directory') ?>/js/tinymce/tinymce.min.js"></script>
	<title>创建新页面</title>
</head>

<body >
<?php echo $htmlData; ?>
<div class="dlg-panel panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title dlg-title">文章编辑</h3>
  </div>
  <div class="panel-body">
	<form name ="content" onSubmit="return validateform()" action="<?php echo constant("CONF_THEME_DIR"); ?>/wesite/common/post_insert.php?beIframe&siteId=<?php echo $siteId ?>&artType=<?php echo $artType ?>&refreshOpener=<?php echo $refreshOpener ?>&addAttachment=<?php echo $addAttachment ?>&postid=<?php echo $postid ?>" method="post" enctype="multipart/form-data">
		<table class="gridtable" width="700" height="375" border="0" align="center" style="margin-top:25px;">
			<tr>
				<td>
					<label for="name">文章标题</label>		
					<input type="text" id="name" name="post_title" value="<?php echo $post_title ?>" ></input>
					<!--<img src="..." onclick="my_picktrue()"/>-->
					<a href="#" id="clicktoupload" class="btn btn-primary btn-sm active" role="button" style="margin-left:20px">点此上传图片</a>
					<br />
				</td>
			</tr>
			<tr>
				<td>
					<textarea id="conUrl" name="content1"><?php echo $post_content; ?></textarea>
				</td>
			</tr>
			<tr>
				<td>
					<div width="150" align="right">
						<input type="submit" class="btn btn-primary" value="确定" style="width:120px;margin-top:25px"/>
						<input type="cancel" class="btn btn-default" value="取消" onclick="close2()" style="width:120px;margin-top:25px"/>
					</div>
				</td>
			</tr>
        </table>		
	</form>
  </div>
</div>
</body>
<?php tinymce_js("#conUrl"); ?>
<script language='javascript'>
	$("#clicktoupload").click(function(){
		tinymce.activeEditor.buttons.jbimages.onclick();
	});
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

	function close2(){
		window.opener=null;
		setTimeout("self.close()",0);
	}
</script>
</html>