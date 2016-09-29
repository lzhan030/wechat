<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/init.css">
<style>
	.panel{border-radius: 0px; -webkit-box-shadow: 0 0px 0px rgba(0,0,0,0.05); box-shadow: 0 0px 0px rgba(0,0,0,0.05);}
	.sltfield{height: 34px;padding: 6px 12px;font-size: 14px;line-height: 1.428571429;color: #555;vertical-align: middle;background-color: #fff;background-image: none;border: 1px solid #ccc;border-radius: 4px;-webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);-webkit-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;}
</style>

<div class="main_auto">
	<div class="main-title">
	<div class="title-1">当前位置：<font class="fontpurple">砸蛋</font></div>
</div>
<div class="panel panel-default" style="margin-right:50px; margin-top:20px">
	<div class="panel-heading">
		<form name ="content" onSubmit="return validateform()" action="" method="get" enctype="multipart/form-data">	
			 <select id="range" name="range" class="sltfield" style="margin-right:3px">
				<option value="">请选择
				<option value="all">全部</option>
				<option value="id">编号</option>
				<option value="name">名称</option>
			 </select>
			<input type="hidden" id="module" name="module" value="<?php echo $_GET['module'];?>" />
			<input type="hidden" id="do" name="do" value="<?php echo $_GET['do'];?>" />
			<input type="hidden" id="gweid" name="gweid" value="<?php echo $_GET['gweid'];?>" />
			<input id="indata" class="sltfield" name="indata" value="<?php echo $_GET['indata'];?>" />
			<input type="hidden" name="beIframe" value="1">
			<input id="search1" class="btn btn-sm btn-warning" type="submit" value="查询" />
			<input type="button" class="btn btn-sm btn-primary" onclick="location.href='<?php echo $this -> createWebUrl('fieldsFormDisplay',array())?>'" name="add" id="buttonadd" value="创建新砸蛋活动">
		</form>
	</div>

	<table class="table table-striped" width="800" border="0" align="center">
		<tbody>
			<tr>
				<td scope="col" width="50" align="center" style="font-weight:bold">编号</td>
				<td scope="col" width="70" align="center" style="font-weight:bold">名称</td>
				<td scope="col" width="200"  align="center" style="font-weight:bold">URL</td>
				<td scope="col" width="100" align="center" style="font-weight:bold">操作</td>
			</tr>
			<?php
				if(is_array($list) && !empty($list)){
					foreach($list as $element){
				 ?>
			<tr>
				<td align="center"><?php echo $element['id']; ?> </td>
				<td align="center"><?php echo $element['name']; ?></td>
				<td align="center"><input type="text" class="form-control" readonly="readonly"  value="<?php echo $this->createMobileUrl('Lottery',array('id' => $element['id'],'gweid' => $gweid)); ?>"></td>
				<td align="center">
				    <input type="button" class="btn btn-sm btn-warning" onclick="eggdel('<?php echo $element['id']; ?>',this)" value="删除">
					<input type="button" class="btn btn-sm btn-info" onclick="location.href='<?php echo $this -> createWebUrl('fieldsFormDisplay',array('id' => $element['id']))?>'" value="编辑">
					<input type="button" class="btn btn-sm btn-default" onclick="location.href='<?php  echo $this->createWebUrl('awardlist', array('name' => 'egg', 'id' => $element['id']))?>'" value="查看中奖名单">
				</td>
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
					$("#indata_state").hide();
					$("#indata_reason").hide();
				}else if($(this).val() == 'refund_state'){
					$("#indata").hide();//隐藏
					$("#indata").val("");  
					$("#indata_state").show();
					$("#indata_reason").hide();
				}else if($(this).val() == 'refund_reason'){
					$("#indata").hide();//隐藏
					$("#indata").val("");  
					$("#indata_state").hide();
					$("#indata_reason").show();
				}else{
					$("#indata").show();//显示
					$("#indata_state").hide();	
					$("#indata_reason").hide();					
				}
				
			})
		}
	);
	$('#range').val('<?php echo $_GET['range'];?>');

	<?php if(!empty($search_condition)&&$search_condition=='refund_state'){?>
	$("#indata_state").show();
	$("#indata").hide();
	$('#indata_state').val('<?php echo $_GET['indata_state'];?>');
	$("#indata_reason").hide();
	<?php }elseif(!empty($search_condition)&&$search_condition=='refund_reason'){?>
	$("#indata_state").hide();
	$("#indata").hide();
	$("#indata_reason").show();
	$('#indata_reason').val('<?php echo $_GET['indata_state'];?>');
	<?php }else{ ?>
	$("#indata_reason").hide();
	$("#indata_state").hide();
	$("#indata").show();
	<?php } ?>
	function validateform()
	{
		var range = $('#range').val();
		var data = "";
		if(range == "all")
			return true;
			
		if(range == ""){
			alert("请选择查询条件！");
			return false;
		}
		
		if(range == "refund_reason")
			data = $('#indata_reason').val();
		else if(range == "refund_state")
			data = $('#indata_state').val();
		else
			data = $('#indata').val();
			
		if(data==""){
			alert("请输入查询内容");
			return false;
		}
		return true;
		  //alert(value);
	} 	
	//删除某一砸蛋活动
	isSubmitting=false;
	function eggdel(id,obj){
		
		if(isSubmitting)
		return false;
		isSubmitting = true;
		$deletedobj = $(obj).parent().parent();
		
		if(confirm("确定删除吗？")){	
			$.ajax({
				url:window.location.href, 
				type: "POST",
				data:{'egg_del':'isDel','eggid':id},
				success: function(data){
					if (data.status == 'error'){
						alert(data.message);
					}else if (data.status == 'success'){
						alert(data.message);
						$deletedobj.remove();
					}
					isSubmitting = false;
				},
				 error: function(data){
					alert("出现错误");
					isSubmitting = false;
				},
				dataType: 'json'
			});
		}else{
			isSubmitting = false;
		}
	}
	<?php if(empty($list)&&!empty($search_condition)){?>
	alert('没有符合该条件的查询结果');
	<?php } ?>
</script>