<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css" />
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/gradeclass.css" />
		<title>学生编辑</title>
		<script>
		    function checkaccountinfo()
			{
			    
				var studentnumber = document.getElementById('student_number').value;
				var studentname = document.getElementById('student_name').value;
				var studentbirth = document.getElementById('student_birth').value;
				var studentgradeclass = document.getElementById('in').value;
				var studentvericode = document.getElementById('student_vericode').value;
				if(studentnumber == "")
				{
				    alert("学生学号不能为空");
				}
				else if(studentname =="")
				{
				    alert("不能为空");
				}
				else if(studentbirth =="")
				{
				    alert("生日不能为空");
				}
				else if( studentgradeclass=="")
				{
				    alert("所在年级/班级不能为空");
				}
				else if(studentvericode =="")
				{
				    alert("验证码不能为空");
				}
				else
				{
				   
					document.getElementById('accountedit').submit();  
							
				}
			 
			}
		//input与select数据切换
		window.onload=function(){var input=document.getElementById("in"),
	
		s=document.getElementById("se");

		s.onchange=function(){

        input.value=this.value;

        }}
		</script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/calendar.js"></script>
	</head>
<div>
	<form id="accountedit" action="" method="post">
	
	<div class="main-title" style="margin-left:30px;">
		<div class="title-1">当前位置：学生管理 > <font class="fontpurple">学生信息更新 </font>
		</div>
	</div>
	<div class="bgimg"></div>
	<?php
		if( isset($_GPC['student_number'])&&($stunumber==0)&&($vericodenumber==0)){
		?>
		<div style="background-color:#ffffe0; border-color:#e6db55; width:95%; font-size:18px; margin-left:18px;"><p style="padding-left:0%;">提交成功!<br>
		</p></div>
	<?php
	} else if( isset($_GPC['student_number'])&&($stunumber!=0)&&($vericodenumber!=0)){?>
		<div style="background-color:#ffffe0; border-color:#e6db55; width:95%; font-size:18px; margin-left:18px;"><p style="padding-left:0%;">学生学号及学生验证码修改重复，请重新输入!<br>
		</p></div>
	<?php } else if( isset($_GPC['student_number'])&&($stunumber!=0)){?>
		<div style="background-color:#ffffe0; border-color:#e6db55; width:95%; font-size:18px; margin-left:18px;"><p style="padding-left:0%;">学生学号修改重复，请重新输入!<br>
		</p></div>
	<?php } else if( isset($_GPC['student_number'])&&($vericodenumber!=0)){?>
		<div style="background-color:#ffffe0; border-color:#e6db55; width:95%; font-size:18px; margin-left:18px;"><p style="padding-left:0%;">学生验证码修改重复，请重新输入!<br>
		</p></div>
	<?php } ?>
		<table width="400" height="300" border="0" cellpadding="20px" style="margin-left: 23%; margin-top:15px;" id="table2">
			<!--<tr>
				<td width="100"><label for="stufromuser">学生名称: </label></td>	
				<td><input type="text" id="student_fromuser" class="form-control" name="student_fromuser" value="<?php echo $studentfromuser?>" disabled="disabled"> </td>
			</tr>-->
			<tr>
				<td><label for="stunumber">学生学号: </label></td>
				<td><input type="text" id="student_number" class="form-control" name="student_number" value=<?php echo $studentnumber?> > </td>
			</tr>
			<tr>
				<td><label for="stuname">学生姓名: </label></td>		
				<td><input type="text" id="student_name" class="form-control" name="student_name" value=<?php echo $studentname?> > </td>
			</tr>
			<tr>
				<td><label for="stusex">学生性别: </label></td>	
				<!--<td><input type="text" id="stusex" class="form-control" name="stusex" value=<?php echo $stusex?'女':'男'?> > </td>-->
				<td><input type="radio" name="student_sex" value="0"  <?php if($studentsex == 0) echo 'checked="checked"';?>>男 <input type="radio" name="student_sex" value="1"  <?php if($studentsex == 1) echo 'checked="checked"';?> style="margin-left:25px;">女 </td>
			</tr>
			<tr>
				<td><label for="stubirth">学生生日: </label></td>		
				<td><input type="text" id="student_birth" class="form-control" name="student_birth" value=<?php echo $studentbirth?> onclick="new Calendar().show(this);" > </td>
				
			</tr>
			<!--<tr>
				<td><label for="stugrade">学生年级: </label></td>	
				<td><input type="text" id="stugrade" class="form-control" name="stugrade" value=<?php echo $studentgrade?> > </td>
			</tr>
			<tr>
				<td><label for="stuclass">学生班级: </label>	</td>	
				<td><input type="text" id="stuclass" class="form-control" name="stuclass" value=<?php echo $studentclass?> > </td>
			</tr>
			<tr>
				<td><label for="stugradeclass">年级/班级: </label>	</td>	
				<td><input type="text" id="student_gradeclass" class="form-control" name="student_gradeclass" value=<?php echo $studentgradeclass?> > </td>
			</tr>-->
			<tr>
				<td><label for="stugradeclass">年级/班级:</label></td>		
                 <td>
				 <div id="contain">
				 <input type="text" value="<?php echo $studentgradeclass; ?>" class="form-control" id="in" name="in" >
				 <select id="se" name="student_gradeclass" class="form-control" size="1" type="text;margin-left:500px;" value=""  maxlength="20" >
					<?php
					echo "<option value=''>请选择</option>";
					foreach($all_sgc as $allgsc)
					echo "<option value='".$allgsc['stu_gradeclass']."'>".$allgsc['stu_gradeclass']."</option>";
					?>
				</select>
				</div>
				</td>							
			</tr>
				<tr>
				<td><label for="stuvericode">学生验证码: </label></td>	
				<td><input type="text" id="student_vericode" class="form-control" name="student_vericode" value=<?php echo $studentvericode?> > </td>
			</tr>
        </table>
		<div style="margin-top:3%; margin-left:36%;">
	    <input type="button" onclick="checkaccountinfo();" class="btn btn-primary" value="保存" id="checkaccount" style="width:70px">
		<a href="<?php echo $this->createWebUrl('studentmanage',array());?>"><input type="button" class="btn btn-default" value="取消" id="sub3" style="width:70px; margin-left:20px;"></a>
	</div>
	</form>
</div>
</html>