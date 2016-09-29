<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css" />
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/gradeclass.css" />
		<title>告警详情</title>
		<style>
			label{font-weight:normal;}
		</style>
	</head>
<div style="width: 93%;">

	<div class="main-title" style="margin-left:30px;">
		<div class="title-1">当前位置：<a href="<?php echo $this->createWebUrl('index',array());?>">微支付</a> > <a href="<?php echo $this->createWebUrl('Alarmmanage',array());?>">告警管理</a> > <font class="fontpurple">告警详情 </font>
		</div>
	</div>
		<table width="400" height="200" border="0" cellpadding="5px" style="margin-left: 30%; margin-top:30px;" id="table2">
			<tr>
				<td style="width:80px;"><label for="proname">告警编号: </label></td>		
				<td><?php echo $alarm['id']; ?></td>
			</tr>
			<tr>
				<td><label for="proname">告警时间: </label></td>		
				<td><?php echo $alarm['timestamp']; ?></td>
			</tr>
			<tr>
				<td><label for="proname">错误描述: </label></td>		
				<td><?php echo $alarm['description']; ?></td>
			</tr>
			<tr>
				<td><label for="proname">错误代码: </label></td>		
				<td><?php echo $alarm['errortype']; ?></td>
			</tr>
			<tr>
				<td><label for="proname">错误详情: </label></td>		
				<td><?php echo $alarm['alarmcontent']; ?></td>
			</tr>
        </table>
		<a type="button" class="btn btn-default" id="sub3" style="width:100px; margin-left:39.5%; margin-top:20px;" href="<?php echo $this -> createWebUrl("Alarmmanage");?>">返回</a>
</div>
</html>