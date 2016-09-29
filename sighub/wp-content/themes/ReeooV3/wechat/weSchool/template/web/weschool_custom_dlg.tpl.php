<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>

<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes" />
	</head>
    <body>
		<form name ="content" onSubmit="return validateform()" action="" method="post" enctype="multipart/form-data">
		<div>    
			<table width="320" height="200" border="0" cellpadding="10px" style="margin-left:30%; margin-top:30px;" id="table2">			
				<tr>
					<td><label for="video">视频列表展示个数:</label></td>
					<td><input type="text" value="<?php echo $video_num; ?>" class="form-control" id="video_number" name="video_number" /></td>
				</tr>
				<tr>
					<td><label for="homework">家庭作业展示个数:</label></td>
					<td><input type="text" value="<?php echo $homework_num; ?>" class="form-control" id="homework_number" name="homework_number" /></td>							
				</tr>
				<tr>
					<td><label for="notice">公告显示个数:</label></td>
					<td><input type="text" value="<?php echo $notice_num; ?>" class="form-control" id="notice_number" name="notice_number" /></td>					
				</tr>
		</table>
	</div>
	<div style="margin-top:20px;margin-left:46%" >
		<input class="newsadd btn btn-primary" type="submit"  value="确定"style="width:75px;height:32px;"/>	
		
	</div>
	</body>
<script language="javascript">
	function validateform(){
		if(document.getElementById('homework_number').value == ""){
			alert("家庭作业展示个数不能为空！");
			return false;
		}else if(document.getElementById('video_number').value == ""){
			alert("视频列表展示不能为空！");
			return false;
		}else if(document.getElementById('notice_number').value == ""){
			alert("公告显示个数不能为空！");
			return false;
		}else if($("#homework_number").val()!=""&&$("#video_number").val()!=""&&$("#notice_number").val()!=""){
			var testnum=/^[1-9]\d*$/;
			if(!testnum.test($("#homework_number").val())){
				alert("您需要填写数字，请重新输入！");
				return false;
			}else if(!testnum.test($("#video_number").val())){
				alert("您需要填写数字，请重新输入！");
				return false;
			}else if(!testnum.test($("#notice_number").val())){
				alert("您需要填写数字，请重新输入！");
				return false;
			}
		}
		return true;
	}
</script>
</html>