<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
<?php
//20140624 janeen update
//$weid=$_SESSION['WEID'];
$gweid=$_SESSION['GWEID'];
//end
?>
 <script language="javascript">
	top.alarmReminder();	
</script>
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/uploadexcel.css">
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/init.css">
<style>
	.panel{border-radius: 0px; -webkit-box-shadow: 0 0px 0px rgba(0,0,0,0.05); box-shadow: 0 0px 0px rgba(0,0,0,0.05);}
	.sltfield{height: 34px;padding: 6px 12px;font-size: 14px;line-height: 1.428571429;color: #555;vertical-align: middle;background-color: #fff;background-image: none;border: 1px solid #ccc;border-radius: 4px;-webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);-webkit-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;}
</style>

<div class="main_auto">
<div class="main-title">
	<div class="title-1">当前位置：<a href="<?php echo $this->createWebUrl('index',array());?>">微支付</a> > <font class="fontpurple">告警管理</font></div>
</div>
<div class="panel panel-default" style="margin-right:50px; margin-top:20px">
	<div class="panel-heading">		
		<form name ="content" onSubmit="return validateform()" action="" method="get" enctype="multipart/form-data">	
			 <select id="range" name="range" class="sltfield" style="margin-right:3px">
				<option value="">请选择
				<option value="all">全部</option>
				<option value="alarm_id">告警编号</option>
				<option value="error_type">错误类型</option>
			 </select>
			<input type="hidden" id="module" name="module" value="<?php echo $_GET['module'];?>" />
			<input type="hidden" id="do" name="do" value="<?php echo $_GET['do'];?>" />
			<input type="hidden" id="gweid" name="gweid" value="<?php echo $_GET['gweid'];?>" />
			<input id="indata" class="sltfield" name="indata" value="<?php echo $_GET['indata'];?>" />
			<input type="hidden" name="beIframe" value="1">
			<input class="btn btn-info btn-sm" type="submit" value="查询"/>
		</form>
	</div>
	<table class="table table-striped" width="800" border="0" align="center">
		<tbody>
			<tr>
				<td scope="col" width="100" align="center" style="font-weight:bold">告警编号</td>
				<td scope="col" width="100" align="center" style="font-weight:bold">告警时间</td>
				<td scope="col" width="100" align="center" style="font-weight:bold">错误类型</td>
				<td scope="col" width="100" align="center" style="font-weight:bold">操作</td>
			</tr>
			<?php
				if(is_array($list) && !empty($list)){
					foreach($list as $element){
				 ?>
			<tr>
				<td align="center"><?php if($element['read'] == 0){ ?><span style="color: red;">[新]</span><?php } ?><?php echo $element['id']; ?></td>
				<td align="center"><?php echo $element['timestamp']; ?> </td>
				<td align="center"><?php echo $element['description']; ?></td>
				<td class="row" align="center"><input type="hidden"  value="308" maxlength="100"> 
				<input type="button" class="btn btn-sm btn-info" onclick="location.href='<?php echo $this->createWebUrl('AlarmDetail',array('id' => $element['id']));?>'" name="upd" id="buttonupd" value="查看详情"></td>	
			</tr>
			<?php }}?>
		</tbody>
	</table>
</div>
<?php echo $pager;?>
 <script language="javascript">
 	$(function(){
		if( $('#range').val() == 'all')
			$("#indata").hide();
		$('#range').change( function(){
				if($(this).val() == 'all'){
				$("#indata").hide();//隐藏
				$("#indata").val("");  
				}
				else 
				$("#indata").show();//显示
			})
		}
	);
	$('#range').val('<?php echo $_GET['range'];?>');
	<?php if(empty($list)&&!empty($search_condition)){?>
	alert('没有符合该条件的查询结果');
	<?php }?>
	function checknull(obj, warning){
		if (obj.value == "") {
			alert(warning);
			obj.focus();
			return true;
		}
		return false;
	}
	function validateform()
	{
	  var selectone = document.getElementById("range"); 
	  var index = selectone.selectedIndex;
	  var value = selectone.options[index].value; 
	  //alert(value);
	  if(value != "all")
	  {
		  if (checknull(document.content.indata, "请输入查询内容!") == true) {
			return false;
		  }
		  return true; 
	  }
	  else
	     return true;
	} 	
</script>