<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
		
<?php 
if($flag)
{?>
   <script>
	 alert("公告发布成功");
	 //location.href='<?php echo $this->createMobileUrl('noticeteacherlist',array('GWEID' => $gweid,'fromuser'=>$fromuser));?>'
	 location.href='<?php echo $this->createMobileUrl('noticeteacherlist',array('gweid' => $gweid));?>'
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
			a:visited {
				color: #FF00FF
			}
			body {
			    background: #ECECEC;
				font: 16px/1.5 "Microsoft Yahei","微软雅黑",Tahoma,Arial,Helvetica,STHeiti;
			}
			
		</style>
<style type="text/css">
	label {
		width:40px;
	}
</style>
<script>
function noticeregister()
{     
   if($("#notice_title").val()=="")
	{
	   alert("标题是必填项");
	   return false;
	}	
	else if($("#notice_content").val()=="")
	{
	   alert("内容是必填项");
	   return false;
	}
	return true;
}
</script>

<div>
	<form id="vipregister" onSubmit="return noticeregister()" action="" method="post">
	
	<div class="mobile-div img-rounded">
		<div class="mobile-hd">公告发布 > <font class="fontpurple">公告内容填写 </font></div>
	<table width="95%" height="150" border="0" cellpadding="10px" style="margin-left: 1%; margin-top:15px;" id="table2">
		<tbody>
			<tr>
				<td width="10%"><label width="40" height="40" for="notice_title">标题: </label></td>
				<td width="65%"><input type="text" value="" class="form-control" id="notice_title" name="notice_title" ></td>
				<td></td>
			</tr>
			<tr>
				<td><label width="40" height="40" for="user_nikename">内容: </label></td>
				<td width="65%"><textarea cols="50%" rows="10%"  class="form-control" id="notice_content" name="notice_content" style="margin-top:20px;"></textarea></td>
				<td></td>
			</tr>
			
			<tr>
				<td><label width="40" height="40" for="user_mobile">评论开启: </label></td>
				<td>
				<input valign="middle" align="center" type="radio" name="commentSelected" onclick="check(this.value)" align="center" value="1" <?php if($notice_allowcomments == 1) {echo "checked";} ?> >是</input>
				<input valign="middle" align="center" type="radio" name="commentSelected" onclick="check(this.value)" align="center" value="0" <?php if($notice_allowcomments == 0) {echo "checked";} ?> >否<br />
				</td>
			</tr>
			
			<tr>
				<td><label width="40" height="40" for="user_email">发布对象: </label></td>
				<td>
				<select name="home_gradeclass" class="form-control" size="1" type="text" id="home_gradeclass" style="height:50px;" value='<?php echo $allgc['tea_gradeclass'] ?>' maxlength="20">
				<?php
					echo "<option value='*'>所有年级</option>";
						foreach($all_gc as $gc){
							if($teainfo == $gc['sub_tea']){
								echo "<option value='".$gc['sub_tea']."*' selected='selected'>".$gc['sub_tea']."年级所有班级</option>";
							}else{
								echo "<option value='".$gc['sub_tea']."*'>".$gc['sub_tea']."年级所有班级</option>";
							} 
						}
						foreach($allgradeclass as $allgc){
							if($teainfo == $allgc['tea_gradeclass']){
								echo "<option value='".$allgc['tea_gradeclass']."' selected='selected'>".$allgc['tea_gradeclass']."</option>";
							}else{
								echo "<option value='".$allgc['tea_gradeclass']."'>".$allgc['tea_gradeclass']."</option>";
							}
						}
				?>
				</select>
				</td>
			</tr>
			
		</tbody>
	</table>
	
	
	<div style="margin-top:3%; margin-left:33%;">
	    <input type="submit"  class="btn btn-primary" value="发布" id="checkaccount" style="width:70px">
	    <!--<input type="button" onclick="location.href='<?php echo $this->createMobileUrl('noticeteacherlist',array('GWEID' => $gweid,'fromuser'=>$fromuser));?>'" class="btn btn-default" value="取消" id="sub3" style="width:70px; margin-left:20px;">-->
		<input type="button" onclick="location.href='<?php echo $this->createMobileUrl('noticeteacherlist',array('gweid' => $gweid,'fromuser'=>$fromuser));?>'" class="btn btn-default" value="取消" id="sub3" style="width:70px; margin-left:20px;">
	</div>
	</form>
	</div>
</div>
</html>
<?php include $this -> template('footer');?>