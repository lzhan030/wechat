<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>



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
<script src="<?php bloginfo('template_directory'); ?>/js/wescholl_calendar.js"></script>
	<link rel="stylesheet" href="../../css/wsite.css"/>
	<link rel="stylesheet" href="../../css/bootstrap.min.css">
	<script src="../../js/jquery.min.js"></script>
	<script src="../../js/bootstrap.min.js"></script>

	<title>微公告</title>
		<script>
			KindEditor.ready(function(K) {
				window.editor = K.create('#conUrl', {
				items:["emoticons","link","unlink"],
				width:'650px',
				height:'215px'}); 
				});
	</script>
</head>

<body >
<?php echo $htmlData; ?>
<div class="dlg-panel panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title dlg-title">公告编辑</h3>
  </div>
  <div class="panel-body">
	<form name ="content" onSubmit="return validateform()" action="" method="post" enctype="multipart/form-data">
		<table class="gridtable" width="700" height="375" border="0" align="center" style="margin-top:25px;">
			<tr>
				<td>
					<label for="name">公告标题</label>		
					<input type="text" id="notice_title" name="notice_title" value=<?php echo $notice_title ?> ></input>
					<!--<img src="..." onclick="my_picktrue()"/>-->
					<br />
				</td>
			</tr>
			<tr>
				<td>
					<label for="name">公告内容</label>
					<textarea id="conUrl" name="content1" style="width:500px;margin-left:65px;height:200px;visibility:hidden;"><?php echo htmlspecialchars($htmlData); ?><?php echo 	$notice_content ;?>
					</textarea>					
				</td>
			</tr>
			<tr>
				<td>
					<label for="name">评论开启</label>
					<input valign="middle" align="center" type="radio" name="commentSelected" onclick="check(this.value)" align="center" value="1" <?php if($notice_allowcomments == 1) {echo "checked";} ?> >是</input>
					<input valign="middle" align="center" type="radio" name="commentSelected" onclick="check(this.value)" align="center" value="0" <?php if($notice_allowcomments == 0) {echo "checked";} ?> >否<br />
				</td>
			</tr>
			<tr>
				<td>
					<label for="name">发布对象</label>
					<select name="notice_gradeclass" class="form-control" size="1" type="text;margin-left:500px;" id="notice_gradeclass" value='' onchange='this.options[this.selectedIndex].value' maxlength="20">
					<?php
						echo "<option value='*'>所有年级</option>";
						foreach($all_gc as $gc){
							$sub_tea=$gc['sub_tea']."*";
							if($notice_rights == $sub_tea){
								echo "<option value='".$gc['sub_tea']."*' selected='selected'>".$gc['sub_tea']."年级所有班级</option>";
							}else{
								echo "<option value='".$gc['sub_tea']."*'>".$gc['sub_tea']."年级所有班级</option>";
							} 
						}
						foreach($allgradeclass as $allgc){
							if($notice_rights == $allgc['tea_gradeclass']){
								echo "<option value='".$allgc['tea_gradeclass']."' selected='selected'>".$allgc['tea_gradeclass']."</option>";
							}else{
								echo "<option value='".$allgc['tea_gradeclass']."'>".$allgc['tea_gradeclass']."</option>";
							}
						}
					?>
					</select>
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
	
<script language='javascript'>

	//提交时，判断内容是否为空
		function validateform(){
		if(document.getElementById('notice_title').value == ""){
			alert("公告标题不能为空");
			return false;
		}else if(editor.isEmpty()){
			alert("公告内容不能为空");
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
</script>
</html>