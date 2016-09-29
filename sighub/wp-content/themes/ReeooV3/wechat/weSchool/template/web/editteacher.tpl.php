<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css" />
		<title>教师编辑</title>
		<script>
		    function checkaccountinfo()
			{
			    
			    var teachername = document.getElementById('teacher_name').value;
				var teachergradeclass = document.getElementById('in').value;
				var teachervericode = document.getElementById('teacher_vericode').value;
				
				if(teachername == "")
				{
				    alert("教师名不能为空");
				}
				else if(teachergradeclass =="")
				 {
				     alert("所在年级/班级不能为空");
				 }
				else if(teachervericode =="")
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
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/gradeclass.css" />
	</head>
<div>
	<form id="accountedit" action="" method="post">
	
	<div class="main-title" style="margin-left:30px;">
		<div class="title-1">当前位置：教师管理 > <font class="fontpurple">教师信息更新 </font>
		</div>
	</div>
	<div class="bgimg"></div>
	<?php
		if( isset($_GPC['teacher_name']) && ($vericodenumber == 0)){
		?>
		<div style="background-color:#ffffe0; border-color:#e6db55; width:95%; font-size:18px; margin-left:18px;"><p style="padding-left:0%;">提交成功!<br>
		</p></div>
		<?php
	} else if( isset($_GPC['teacher_name']) && ($vericodenumber != 0)){?>
		<div style="background-color:#ffffe0; border-color:#e6db55; width:95%; font-size:18px; margin-left:18px;"><p style="padding-left:0%;">	验证码修改重复，请重新输入!<br>
		</p></div>
	<?php }?>
	<table width="400" height="300" border="0" cellpadding="20px" style="margin-left: 23%; margin-top:15px;" id="table2">
		<tbody>
			<tr>
				<td><label for="teaname">教师姓名: </label></td>
				<td width="300"><input type="text" value="<?php echo $teachername; ?>" class="form-control" id="teacher_name" name="teacher_name"></td>
				<td></td>
			</tr>
			<tr>
				<td><label for="teasex">教师性别: </label></td>	
				<td><input type="radio" name="teacher_sex" value="1"  <?php if($teachersex == 1) echo 'checked="checked"';?>>男 <input type="radio" name="teacher_sex" value="0"  <?php if($teachersex == 0) echo 'checked="checked"';?> style="margin-left:25px;">女 </td>
				<td></td>
			</tr>
			<tr>
				<td><label for="teagradeclass">年级/班级:</label></td>		
                 <td>
				 <div id="contain">
				 <input type="text" value="<?php echo $teachergradeclass; ?>" class="form-control" id="in" name="in" >
				 <select id="se" name="teacher_gradeclass" class="form-control" size="1" type="text;margin-left:500px;" value=""  maxlength="20" >
					<?php
					echo "<option value=''>请选择</option>";
					foreach($all_tgc as $allgtc)
					echo "<option value='".$allgtc['tea_gradeclass']."'>".$allgtc['tea_gradeclass']."</option>";
					?>
				</select>
				</div>
				</td>							
			</tr>
			
			<!--<tr>
				<td><label for="teagradeclass">年级/班级: </label>	</td>
				<td width="280"><input type="text" value="<?php echo $teachergradeclass; ?>" class="form-control" id="teacher_gradeclass" name="teacher_gradeclass"></td>
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
				<td><label for="teavericode">教师验证码: </label></td>	
				<td><input name="teacher_vericode" type="text" class="form-control" id="teacher_vericode" size="10" maxlength="10" value="<?php echo $teachervericode; ?>" /></td>
				
			</tr>
			
		</tbody>
	</table>
	
	
	<div style="margin-top:3%; margin-left:36%;">
	    <input type="button" onclick="checkaccountinfo();" class="btn btn-primary" value="保存" id="checkaccount" style="width:70px">
		<a href="<?php echo $this->createWebUrl('teachermanage',array());?>"><input type="button" class="btn btn-default" value="取消" id="sub3" style="width:70px; margin-left:20px;"></a>
	</div>
	</form>
</div>
</html>
