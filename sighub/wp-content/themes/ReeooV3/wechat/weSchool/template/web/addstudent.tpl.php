<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
<script>
	function check_all(obj,cName)
	{
		var checkboxs = document.getElementsByName(cName);
		for(var i=0;i<checkboxs.length;i++){checkboxs[i].checked = obj.checked;}
	}
    function checkinputinfo()
	{
	    if(document.getElementById('student_number').value == "")
		{
		    alert("学生学号不能为空");
			return false;
		}
		else if(document.getElementById('student_name').value == "")
		{
		    alert("学生姓名不能为空");
			return false;
		}
		else if(document.getElementById('student_birth').value == "")
		{
		     alert("学生生日不能为空");
			 return false;
		}
			else if(document.getElementById('in').value == "")
		{
				alert("学生年级/班级不能为空");
				return false;	
		}
			else if(document.getElementById('student_vericode').value == "")
		{
				alert("学生验证码不能为空");
				return false;	
		}
		}
		//input与select数据切换
		window.onload=function(){var input=document.getElementById("in"),
	
		s=document.getElementById("se");

		s.onchange=function(){

        input.value=this.value;

        }}

		
</script>
<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js"></script>
<script src="<?php bloginfo('template_directory'); ?>/js/bootstrap.min.js"></script>
<script src="<?php bloginfo('template_directory'); ?>/js/calendar.js"></script>
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css" />
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/gradeclass.css" />
<div>
	<form id="studentsadd" action="" method="post" onsubmit="return checkinputinfo();">
	
	<div class="main-titlenew">
		<div class="title-1">当前位置：学生管理 > <font class="fontpurple">添加学生信息 </font>
		</div>
	</div>
	<div class="bgimg"></div>
		<?php
		if( isset($_GPC['student_number'])&&($stunumber==0)&&($vericodenumber==0)){
		?>
		<p style="background-color:#ffffe0;border-color:#e6db55;width: 80px;font-size: 18px;margin-left: 300px;">	提交成功!<br>
		</p>
	<?php
	} else if( isset($_GPC['student_number'])&&($stunumber!=0)&&($vericodenumber!=0)){?>
		<p style="background-color:#ffffe0;border-color:#e6db55;width: 90%;font-size: 18px;margin-left: 230px;">学生学号及学生验证码出现重复，请重新输入!<br>
		</p>
	<?php } else if( isset($_GPC['student_number'])&&($stunumber!=0)){?>
		<p style="background-color:#ffffe0;border-color:#e6db55;width: 90%;font-size: 18px;margin-left: 230px;">学生学号出现重复，请重新输入!<br>
		</p>
	<?php } else if( isset($_GPC['student_number'])&&($vericodenumber!=0)){?>
		<p style="background-color:#ffffe0;border-color:#e6db55;width: 90%;font-size: 18px;margin-left: 230px;">学生验证码出现重复，请重新输入!<br>
		</p>
	<?php } ?>
	
	<table width="380" height="300" border="0" cellpadding="20px" style="margin-left: 150px; margin-top:30px;" id="table2">
		<tbody>
			<tr>
				<td><label for="stunumber">学生学号: </label></td>
				<td width="280"><input type="text" value="" class="form-control" id="student_number" name="student_number"></td>
				<td></td>
			</tr>
			<tr>
				<td><label for="stuname">学生姓名: </label></td>
				<td width="280"><input type="text" value="" class="form-control" id="student_name" name="student_name"></td>
				<td></td>
			</tr>
			<!--<tr>
				<td>学生性别: </td>
				<td><input type="text" value="" class="form-control" id="student_sex" name="student_sex">
				</td>
			</tr>-->
				<tr>
				<td><label for="stusex">学生性别: </label></td>	
				<td>			    
					 <input type="radio" id="student_sex" name="student_sex" value="0" checked="checked"/>男
					 <input type="radio" id="student_sex" name="student_sex" value="1" />女			
				</td>
			</tr>
			<tr>
					<td><label for="stubirth">学生生日: </label></td>		
				<td><input type="text" value="" class="form-control" id="student_birth" name="student_birth" onclick="new Calendar().show(this);">
				</td>	
			</tr>
			<!--<tr>
				<td>学生年级:</td>
				<td><input name="student_grade" type="text" class="form-control" id="student_grade" size="10" maxlength="10"/></td>
				
			</tr>
			<tr>
				<td>学生班级:</td>
				<td><input name="student_class" type="text" class="form-control" id="student_class" size="10" maxlength="10"/></td>
				
			</tr>
			<tr>
			<tr>
				<td><label for="stugradeclass">年级/班级: </label>	</td>		
				<td><input type="text" id="stu_gradeclass" class="form-control" name="stu_gradeclass"> </td>
			</tr>-->
			<tr>
				<td><label for="content">年级/班级:</label></td>		
                 <td>
				 <div id="contain">
				 <input type="text" value="" class="form-control" id="in" name="in" >
				 <select id="se" name="stu_gradeclass" class="form-control" size="1" type="text;margin-left:500px;" value=''  maxlength="20" >
					<?php
					echo "<option value=''>请选择</option>";
					foreach($all_sgc as $allgsc)
					echo "<option value='".$allgsc['stu_gradeclass']."' >".$allgsc['stu_gradeclass']."</option>";
					?>
				</select>
				</div>
				</td>							
			</tr>
			<tr>
				<td><label for="stuvericode">学生验证码: </label></td>	
				<td><input type="text" value="<?php echo wp_generate_password( $length=6, $include_standard_special_chars=false ) ?>" class="form-control" id="student_vericode" name="student_vericode">
			</tr>
		</tbody>
	</table>
	
	
	<div style="margin-top:3%; margin-left:275px;">
	    <input type="submit" class="btn btn-primary" value="保存" id="sub3" style="width:70px">
		<!--<input type="button" class="personentry btn btn-primary" onclick="checkinputinfo()" title="<?php //echo $this->createWebUrl('addstudent');?>" value="保存" id="sub3" style="width:70px">-->
	    <input type="button" onclick="location.href='<?php echo $this->createWebUrl('studentmanage',array());?>'" class="btn btn-default" value="取消" id="sub3" style="width:70px; margin-left:20px;">
	</div>
	</form>
</div>
<?php //} ?>