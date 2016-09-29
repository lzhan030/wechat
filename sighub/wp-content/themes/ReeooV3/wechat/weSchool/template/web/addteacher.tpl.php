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
	    if(document.getElementById('teacher_name').value == "")
		{
		    alert("用户名不能为空");
			return false;
		}
		else if(document.getElementById('in').value == "")
		{
		    alert("所在班级不能为空");
			return false;
		}
		else if(document.getElementById('teacher_vericode').value == "")
		{
		    alert("验证码不能为空");
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

<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/calendar.js"></script>
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/init.css">
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/gradeclass.css" />
<div>
	<form id="useradd" action="" method="post" onsubmit="return checkinputinfo();">
	
	<div class="main-titlenew">
		<div class="title-1">当前位置：教师管理 > <font class="fontpurple">添加教师信息 </font>
		</div>
	</div>
	<div class="bgimg"></div>
	<?php
		if( isset($_POST['teacher_name']) && ($vericodenumber == 0)){
		?>
		<p style="background-color:#ffffe0;border-color:#e6db55;width: 80px;font-size: 18px;margin-left: 300px;">	提交成功!<br>
		</p>
	<?php
	} else if( isset($_POST['teacher_name']) && ($vericodenumber != 0)){?>
		<p style="background-color:#ffffe0;border-color:#e6db55;width: 90%;font-size: 18px;margin-left: 230px;">	验证码有重复，请重新输入!<br>
		</p>
	<?php }?>
	
	<table width="380" height="300" border="0" cellpadding="20px" style="margin-left: 150px; margin-top:30px;" id="table2">
		<tbody>
			<tr>
				<td><label for="teaname">教师姓名: </label></td>
				<td width="280"><input type="text" value="" class="form-control" id="teacher_name" name="teacher_name"></td>
				<td></td>
			</tr>
			<tr>
				<td><label for="teasex">教师性别: </label></td>	
				<td>			    
					 <input type="radio" id="teacher_sex" name="teacher_sex" value="1" checked="checked"/>男
					 <input type="radio" id="teacher_sex" name="teacher_sex" value="0" />女			
				</td>
			</tr>
			<!--<tr>
				<td><label for="teagradeclass">年级/班级: </label>	</td>	
				<td width="280"><input type="text" value="" class="form-control" id="teacher_gradeclass" name="teacher_gradeclass"></td>
				<td></td>
			</tr>-->
				<tr>
				<td><label for="content">年级/班级:</label></td>		
                 <td>
				 <div id="contain">
				 <input type="text" value="" class="form-control" id="in" name="in" >
				 <select id="se" name="stu_gradeclass" class="form-control" size="1" type="text;margin-left:500px;" value=''  maxlength="20" >
					<?php
					echo "<option value=''>请选择</option>";
					foreach($all_tgc as $allgtc)
					echo "<option value='".$allgtc['tea_gradeclass']."' >".$allgtc['tea_gradeclass']."</option>";
					?>
				</select>
				</div>
				</td>							
			</tr>
			
			<!--<tr>
				<td>所在年级/班级: </td>
				<td>
				    <select name="teacher_gradeclass" class="form-control" size="1" type="text;margin-left:500px;" id="teacher_gradeclass" value="5" maxlength="20" onchange="selClass(this.options[this.selectedIndex].value)">
					
					<?php //foreach($teachergradeclass as $grade){?>
						<option value="<?php //echo $grade->tea_gradeclass;?>" selected="selected"><?php //echo $grade->tea_gradeclass;?></option>
					<?php //}?>
						
					</select>
				</td>
			</tr>-->
			
			<!--<tr>
				<td>所在年级: </td>
				<td>
				    <select name="teacher_ygrade" class="form-control" size="1" type="text;margin-left:500px;" id="teacher_ygrade" value="5" maxlength="20" onchange="selClass(this.options[this.selectedIndex].value)">
					<?php //$teacherugrade = web_get_teacher_ugrade();?>
					<?php //foreach($teacherugrade as $ugrade){?>
						<option value="<?php //echo $ugrade->tea_ugrade;?>" selected="selected"><?php //echo $ugrade->tea_ugrade;?></option>
					<?php //}?>
						
					</select>
				</td>
			</tr>
			<!--所在班级是根据年级动态获取到的，这里需要采用ajax请求-->
			<!--<tr>
				<td>所在班级: </td>
				<td>
				    <select name="teacher_class" class="form-control" size="1" type="text;margin-left:500px;" id="teacher_class" value="5" maxlength="20">
				   
					<?php //foreach($teacherugrade as $ugrade){?>
						<option value="<?php //echo $ugrade->tea_ugrade;?>" selected="selected"><?php //echo $ugrade->tea_ugrade;?></option>
					<?php //}?>
						
					</select>
				</td>
			</tr>-->
			
			<tr>
				<td><label for="teavericode">教师验证码: </label></td>	
				<td><input type="text" value="<?php echo wp_generate_password( $length=6, $include_standard_special_chars=false ) ?>" class="form-control" id="teacher_vericode" name="teacher_vericode">
				</td>
			</tr>
			
			
		</tbody>
	</table>
	
	<div style="margin-top:3%; margin-left:275px;">
	    <input type="submit" class="btn btn-primary" value="保存" id="sub3" style="width:70px">
	    <input type="button" onclick="location.href='<?php echo $this->createWebUrl('teachermanage',array());?>'" class="btn btn-default" value="取消" id="sub3" style="width:70px; margin-left:20px;">
	</div>
	</form>
</div>
<?php //} ?>