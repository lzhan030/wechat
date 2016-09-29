<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>创建新分组</title>
		<link rel="stylesheet" href="css/wsite.css">
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<script src="js/checkname.js"></script>
		<script language='javascript'>
			function checkinputinfo()
			{
				if(document.getElementById('group_name').value == "")
				{
					alert("分组名称不能为空!");
					return false;
				}
				if(document.getElementById('checkbox').innerHTML.length == 39)
				{
					alert("分组名不能重复！");
					return false;
				}
				return true;
			}
		</script>
	</head>
	<body>
		<div class="mainpop">
			<form id="groupedit" name="mygroup" action="cgi-bin/group_new_add.php" method="post" onsubmit="return checkinputinfo();">
			<div class="main-title">
				<div class="title-1" style="font-weight:bold;">添加分组 </font>
				</div>
			</div>
			<table width="480" height="100" border="0" style="margin-left:50px; margin-top:10px;" id="table2">
				<tbody>
					<tr>
						<td width="100"><label for="func_name">分组名称: </label></td>
						<td width="240"><input type="text" value="" onchange="checkgrpname()" class="form-control" id="group_name" name="group_name" autofocus="autofocus"></td>
						<td width="180"><span id="checkbox" style="font-size:12px;font-family:'微软雅黑';"></span></td>
					</tr>
					<tr>
						<td><label for="func_description">描述: </label></td>
						<td width="240"><input type="text" value="" class="form-control" id="group_description" name="group_description" /></td>
						<td></td>
					</tr>			
				</tbody>
			</table>
			
			<div style="margin-top:3%; margin-left:180px;">
				<input type="submit" class="btn btn-primary" value="提交" id="sub3" style="width:70px">
				<input type="button" onclick="window.close()" class="btn btn-default" value="返回" id="sub3" style="width:70px; margin-left:20px;">
			</div>
			</form>
		</div>
	</body>
</html>