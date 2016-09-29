<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>

<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes" />
		
		<?php 
		if($flag)
		{?>
		   <script>
	         alert("视频上传成功");
	         location.href="notice_family.php?GWEID=<?php echo $gweid;?>&fromuser=<?php echo $fromuser;?>";
	       </script>
		 <?php
		}
		?>
		
		<script>
	    function uploadverify()
	    {
	        
		    if($("#video_title").val()=="")
			{
			   alert("视频标题是必填项");
			}	
			else if($("#file").val()=="")
			{
				alert("请选择要上传的文件");
			}
			else 
			{
				
				$("#videoupload").submit();	
			}
			
	    }
		</script>
		
	</head>
    <body>
	<div>
	    <form id="videoupload" action="" method="post">
			<div id="maintest" class="main">			
				<div class="main-title">
					<div class="title-1">当前位置：视频管理> <font class="fontpurple">视频上传 </font>
					</div>
				</div>
				<div class="bgimg"></div>
				<div id="nav-main" style="margin-left:23%;">				
					<div>    
						<table width="95%" height="300" border="0" cellpadding="10px" style="margin-left:0px; margin-top:30px;" id="table2">			
							<tr>
								<td><label for="title">视频标题:</label></td>
								<td width="65%"><input type="text" value="" class="form-control" id="video_title" name="video_title" /></td>
							</tr>
							<tr>
								<td><label for="desp">视频描述:</label></td>
								<td width="65%"><textarea id="video_desp" name="video_desp"  class="form-control" style="height:80px;" ></textarea></td>						
							</tr>
							<tr>
								<td><label for="content">年级/班级:</label></td>
								<!--<td><input type="text" value="" class="form-control" id="video_gradeclass" name="video_gradeclass" /></td>	-->		
                                <td><select name="video_gradeclass" class="form-control" size="1" type="text;margin-left:500px;" id="video_gradeclass" value='' onchange='this.options[this.selectedIndex].value' maxlength="20">
									<?php
										echo "<option value='*'>*</option>";
										foreach($all_g as $allg)
										{
										    echo "<option value='".$allg['allgrade']."' >".$allg['allgrade']."</option>";
										}
										foreach($all_gc as $allgc){
											if($teacher_gc == $allgc['tea_gradeclass']){
												echo "<option value='".$allgc['tea_gradeclass']."' selected='selected' >".$allgc['tea_gradeclass']."</option>";
											}else{
												echo "<option value='".$allgc['tea_gradeclass']."' >".$allgc['tea_gradeclass']."</option>";
											}
										}
									?>
								</select></td>							
							</tr>
							<tr>
								<td><label for="content">选择文件:</label></td>
								<td><input type='file' class='form-control' name='file' id='file'/></td>							
							</tr>
						</table>
					</div>
					
					<div style="margin-top:3%; margin-left:35%;">
						<input type="button" onclick="uploadverify();" class="btn btn-primary" value="申请" id="checkaccount" style="width:70px">
						<input type="button" onclick="location.href='?admin&page=usermanage'" class="btn btn-default" value="取消" id="sub3" style="width:70px; margin-left:20px;">
					</div>
					
				</div>
			</div>
		</form>
	</div>
	</body>
</html>