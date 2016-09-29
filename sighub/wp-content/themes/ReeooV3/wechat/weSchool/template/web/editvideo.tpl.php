<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css" />
		<title>视频、照片编辑</title>
		<script>
			function returnlast()
			{
			   //var url="<?php echo get_template_directory_uri(); ?>";
			   var url="histroy";
			   location.href=url;
			}
		</script>
		<script>
		    function checkaccountinfo()
			{
			    
			    var videotitle = document.getElementById('video_title').value;
				var videogradeclass = document.getElementById('video_gradeclass').value;
				var videopublisher = document.getElementById('video_publisher').value;
				
				if(videotitle == "")
				{
				    alert("标题不能为空");
				}
				else if(videogradeclass =="")
				{
				    alert("年级/班级不能为空");
				}
				else if(videopublisher =="")
				{
				    alert("发布者不能为空");
				}
				else
				{
				   
					document.getElementById('videoedit').submit();  
							
				}
			 
			}
			
		</script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/calendar.js"></script>
	</head>
<div>
	<form id="videoedit" action="" method="post">
	
	<div class="main-title" style="margin-left:30px;">
		<div class="title-1">当前位置：视频、照片管理 > <font class="fontpurple">视频、照片信息更新 </font>
		</div>
	</div>
	<div class="bgimg"></div>
	<?php
		if( isset($_GPC['video_title'])){
		?>
		<div style="background-color:#ffffe0; border-color:#e6db55; width:95%; font-size:18px; margin-left:18px;"><p style="padding-left:0%;">提交成功!<br>
		</p></div>
		<?php
	}?>
	<table width="480" height="300" border="0" cellpadding="20px" style="margin-left: 23%; margin-top:40px;" id="table2">
		<tbody>
			<tr>
				<td><label for="videotitle">标题:</label></td>
				<td width="300"><input type="text" value="<?php echo $videotitle; ?>" class="form-control" id="video_title" name="video_title"></td>
				<td></td>
			</tr>
			<tr>
				<td><label for="videodesp">描述: </label></td>
				<td><textarea id="video_desp" name="video_desp"  class="form-control" style="width:400px;height:80px;"><?php echo $videodesp; ?></textarea></td>
				<!--<td width="300"><input type="text" value="<?php echo $videodesp; ?>" class="form-control" id="video_desp" name="video_desp"></td>-->
				<td></td>
			</tr>
			<tr>
				<td><label for="videourl">url:</label></td>
				<td width="300"><input type="text" value="<?php echo $videourl; ?>" class="form-control" id="video_url" name="video_url" disabled="disabled"></td>
				<td></td>
			</tr>
				<tr>
				<td><label for="videotime">上传时间:</label></td>
				<td><input name="video_time" type="text" class="form-control" id="video_time" size="10" maxlength="10" value="<?php echo $videotime; ?>" onclick="new Calendar().show(this);" /></td>
				
			</tr>
			<tr>
				<td><label for="videogradeclass">年级/班级: </label></td>
				<!--<td width="280"><input type="text" value="<?php echo $videogradeclass; ?>" class="form-control" id="video_gradeclass" name="video_gradeclass"></td>-->
				<td width="280">
				    <select name="video_gradeclass" class="form-control" size="1" type="text;margin-left:500px;" id="video_gradeclass" value='' onchange='this.options[this.selectedIndex].value' maxlength="20">
						<?php
						    
							echo "<option value='*'>所有年级</option>";
							foreach($all_g as $allg)
							{
								//echo "<option value='".$allg['allgrade']."*' >".$allg['allgrade']."*</option>";
								if($videogradeclass == $allg['allgrade']){
								    echo "<option value='".$allg['allgrade']."' selected='selected'>".$allg['allgrade']."年级所有班级</option>";
							    }else{
							   	    echo "<option value='".$allg['allgrade']."'>".$allg['allgrade']."年级所有班级</option>";
							    } 
								
							}
							foreach($all_gc as $allgc){
								if($videogradeclass == $allgc['tea_gradeclass']){
									echo "<option value='".$allgc['tea_gradeclass']."' selected='selected' >".$allgc['tea_gradeclass']."</option>";
								}else{
									echo "<option value='".$allgc['tea_gradeclass']."' >".$allgc['tea_gradeclass']."</option>";
								}
							}
						?>
					</select>
				</td>
				<td></td>
			</tr>
			
			<!--<tr>
				
				<td>所在年级: </td>
				<td>
				    <select name="teacher_ygrade" class="form-control" size="1" type="text;margin-left:500px;" id="teacher_ygrade" value="5" maxlength="20" onchange="selClass(this.options[this.selectedIndex].value)">
					<?php //$teacherugrade = web_get_teacher_ugrade();?>
					<?php //foreach($teacherugrade as $ugrade){?>
						<option value="<?php //echo $ugrade->tea_ugrade;?>" <?php //if($ugrade->tea_ugrade === $teacher->ugrade) echo 'selected="selected"'?>><?php //echo $ugrade->tea_ugrade;?></option>
					<?php //}?>
						
					</select>
				</td>
			</tr>
			
			<tr>
				<td>所在班级:</td>				
				<td> 
				    <select name="teacher_class" class="form-control" size="1" type="text;margin-left:500px;" id="teacher_class" value="5" maxlength="20">
				   
					<?php //foreach($teacherugrade as $ugrade){?>
						<option value="<?php //echo $ugrade->tea_ugrade;?>" <?php //if($ugrade->tea_class === $teacher->tea_class) echo 'selected="selected"'?>><?php //echo $ugrade->tea_ugrade;?></option>
					<?php //}?>
						
					</select>
				</td>
				
			</tr>-->
			<tr>
				<td><label for="videopublisher">发布者:</label></td>
				<td><input name="video_publisher" type="text" class="form-control" id="video_publisher" size="10" maxlength="10" value="<?php echo $videopublisher; ?>" disabled="disabled"/></td>
				
			</tr>
			
		</tbody>
	</table>
	
	
	<div style="margin-top:3%; margin-left:36%;">
	    <input type="button" onclick="checkaccountinfo();" class="btn btn-primary" value="保存" id="checkaccount" style="width:70px">
		<a href="<?php echo $this->createWebUrl('videomanage',array());?>"><input type="button" class="btn btn-default" value="取消" id="sub3" style="width:70px; margin-left:20px;"></a>
	</div>
	</form>
</div>
</html>
