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
<script src="<?php bloginfo('template_directory'); ?>/js/weschool_calendar.js"></script>
	<link rel="stylesheet" href="../../css/wsite.css"/>
	<link rel="stylesheet" href="../../css/bootstrap.min.css">
	<script src="../../js/jquery.min.js"></script>
	<script src="../../js/bootstrap.min.js"></script>

	<title>微作业</title>
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

<div class="dlg-panel panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title dlg-title">作业编辑</h3>
  </div>
  <div class="panel-body">
	<form name ="content" onSubmit="return validateform()" action="" method="post" enctype="multipart/form-data">
		<table class="gridtable" width="700" height="375" border="0" align="center" style="margin-top:25px;">
			<tr>
				<td>
					<label for="name">作业标题</label>		
					<input type="text" id="work_title" name="work_title" value=<?php echo $work_title ?> ></input>
					<!--<img src="..." onclick="my_picktrue()"/>-->
					<br />
				</td>
			</tr>
			<tr>
				<td>
					<label for="name">作业内容</label>
					<textarea id="conUrl" name="content1" style="width:500px;margin-left:65px;height:200px;visibility:hidden;"><?php echo htmlspecialchars($htmlData); ?><?php echo 	$work_content ;?>
					</textarea>					
				</td>
			</tr>
			<tr>
				<td>
					<label for="name">作业班级</label>
					<select name="home_gradeclass" class="form-control" size="1" type="text;margin-left:500px;" id="home_gradeclass" value='' onchange='this.options[this.selectedIndex].value' maxlength="20">
					<?php
						echo "<option value='*'>所有年级</option>";
						foreach($all_gc as $gc){
							$sub_tea=$gc['sub_tea']."*";
							if($home_gradeclass == $sub_tea){
								echo "<option value='".$gc['sub_tea']."*' selected='selected'>".$gc['sub_tea']."年级所有班级</option>";
							}else{
								echo "<option value='".$gc['sub_tea']."*'>".$gc['sub_tea']."年级所有班级</option>";
							} 
						}
						foreach($allgradeclass as $allgc){
							if($home_gradeclass == $allgc['tea_gradeclass']){
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
					作业截止时间:<input type="text" name="sDate1" id="sDate1" value="<?php echo $home_starttime ?>" size="10" onClick="return Calendar('sDate1','sDate2');" />至<input type="text" name="sDate2" id="sDate2" value="<?php echo $home_endtime ?>" size="10" onClick="return Calendar('sDate2');" />
				</td>
			</tr>
			<tr>
				<td>
					<div width="150" align="right">
						<input type="submit" class="btn btn-primary" value="保存" style="width:120px;margin-top:25px"/>
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

/* 	
	function checknull(obj, warning)
	{
	  if (obj.value == "") {
		alert(warning);
		obj.focus();
		return true;
	  }
	return false;
	} */



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
	
	function validateform(){
		if(document.getElementById('work_title').value == ""){
			alert("作业标题不能为空");
			return false;
		}else if(editor.isEmpty()){
			alert("作业内容不能为空");
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
</html>