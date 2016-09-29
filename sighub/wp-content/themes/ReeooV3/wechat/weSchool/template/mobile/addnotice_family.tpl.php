<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
		
<?php 
if($flag)
{?>
   <script>
	 alert("公告发布成功");
	 //location.href='<?php echo $this->createMobileUrl('noticefamilylist',array('GWEID' => $gweid,'fromuser'=>$fromuser));?>'
	 location.href='<?php echo $this->createMobileUrl('noticefamilylist',array('gweid' => $gweid));?>'
   </script>
 <?php
}
?>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=3.0, user-scalable=yes" />
		<!--<link type="text/css" rel="stylesheet" href="<?php //bloginfo('template_directory'); ?>/we7/style/bootstrap.css" />-->
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/we7/style/font-awesome.css" />
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/we7/style/common.css?v=<?php echo TIMESTAMP;?>" />
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/video.css">
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/we7/script/jquery-1.7.2.min.js"></script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/we7/script/bootstrap.js"></script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/we7/script/common.js?v=<?php echo TIMESTAMP;?>"></script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/we7/script/emotions.js"></script>
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite_mobile.css">

<style type="text/css">
	label {
		width:40px;
	}
	#tselected{
		width:65px;
	}
	table{
		margin:5% 3% 2% 3%;
	}
	a:visited {
			color: #FF00FF
	}
	body {
		background: #ECECEC;
		font: 16px/1.5 "Microsoft Yahei","微软雅黑",Tahoma,Arial,Helvetica,STHeiti;
	}
	#notice_content{
		margin:20px 2% 0 0;
	}
	#notice_title{
		margin-right:2%;
	}
	
</style>
<script>
	function noticeregister()
	{     
		if($("#notice_title").val()=="")
		{
			alert("标题是必填项");
			return false;
		}else if($("#notice_content").val()=="")
		{
			alert("内容是必填项");
			return false;
		}
			return true;
	}
</script>
	
<div>
	<form id="noticeregister" onSubmit="return noticeregister()" action="" method="post">
	<div class="mobile-div img-rounded">
		<div class="mobile-hd">公告发布 > <font class="fontpurple">公告内容填写 </font></div>
	<table width="90%" height="150" border="0" cellpadding="10px" style="" id="table2">
		<tbody>
			<tr>
				<td width="10%"><label for="notice_title">标题: </label></td>
				<td width="65%"><input type="text" value="" class="form-control" id="notice_title" name="notice_title" ></td>
				<td></td>
			</tr>
			<tr style="margin-top:8px; position:relative;">
				<td><label for="user_nikename">内容: </label></td>
				<td width="65%"><textarea cols="20%" rows="8%"  value="" class="form-control" id="notice_content" name="notice_content" style="margin-top:20px;"></textarea></td>
				<td></td>
			</tr>
			
			<tr style="margin-top:8px;">
				<td><label for="user_mobile">评论开启: </label></td>
				<td>
				<input valign="middle" align="center" type="radio" name="commentSelected" onclick="check(this.value)" align="center" value="1" <?php if($notice_allowcomments == 1) {echo "checked";} ?> >是</input>
				<input valign="middle" align="center" type="radio" name="commentSelected" onclick="check(this.value)" align="center" value="0" <?php if($notice_allowcomments == 0) {echo "checked";} ?> >否<br /></td>
			</tr>
			
			<tr>
				<td><label for="user_email">发布对象: </label></td>
				<td nowrap='nowrap'>
				<input type="radio" name="radioteacher" id="radioteacher" value="0" onclick="disableout()" checked="checked">所属班级
				<input name="home_gradeclass" id="home_gradeclass" type="text" value="<?php echo $familu_g ; ?>" class="" readonly="true" maxlength="20" style="width:70px;">
				<!--<select name="home_gradeclass" class="" size="1" type="text;margin-left:500px;" id="home_gradeclass" value='' onchange='this.options[this.selectedIndex].value' maxlength="20">
				<?php
					echo "<option value='*'>*</option>";
					foreach($all_teacher as $allgc){
						if($familu_g == $allgc['tea_gradeclass']){
							echo "<option value='".$allgc['tea_gradeclass']."' selected='selected' disabled>".$allgc['tea_gradeclass']."</option>";
						}else{
							echo "<option value='".$allgc['tea_gradeclass']."' disabled >".$allgc['tea_gradeclass']."</option>";
						}
					}
				?>
				</select>-->
				</td>
				</tr>
				<tr>
				<td>
				</td>
				<td>
				<input type="radio" name="radioteacher" id="radioteacher" value="1" onclick="disableIn()">选择老师(可选项) 
				<select name="teacher_name" class="" size="1.3" type="text;margin-left:500px;" style="visibility:hidden;font-family:Trebuchet MS;height:25px;margin-top:3px;" id="teacher_name" value='' onchange='this.options[this.selectedIndex].value' maxlength="20">
				<?php
					echo "<option value='*'>该班级所有老师</option>";
					foreach($all_teaname as $alltea){
						if($familu_g == $alltea['tea_gradeclass']){
							echo "<option value='".$alltea['tea_id']."' selected='selected'>".$alltea['tea_name']."</option>";
						}else{
							echo "<option value='".$alltea['tea_id']."' >".$alltea['tea_name']."</option>";
						}
					}
				?>
				</select>
	
				</td>
			</tr>
			
		</tbody>
	</table>
	
	
	<div style="margin-top:3%; margin-left:35%;">
	    <input type="submit" class="btn btn-primary" value="发布" id="checkaccount" style="width:70px">
	    <!--<input type="button" onclick="location.href='<?php //echo $this->createMobileUrl('noticefamilylist',array('GWEID' => $gweid,'fromuser'=>$fromuser));?>'" class="btn btn-default" value="取消" id="sub3" style="width:70px; margin-left:20px;">-->
		<input type="button" onclick="location.href='<?php echo $this->createMobileUrl('noticefamilylist',array('gweid' => $gweid));?>'" class="btn btn-default" value="取消" id="sub3" style="width:70px; margin-left:20px;">
	</div>
	</form>
	</div>
</div>
<script language='javascript'>
function disableIn() {
		document.getElementById('teacher_name').style.visibility='visible';
	}
function disableout() {
		document.getElementById('teacher_name').style.visibility='hidden';
	}
</script>
</html>
<?php include $this -> template('footer');?>