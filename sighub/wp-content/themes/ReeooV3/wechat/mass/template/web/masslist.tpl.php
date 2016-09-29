<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
<div class="main_auto">
<div class="main-title">
	<div class="title-1">当前位置： <font class="fontpurple">群发管理</font></div>
</div>
<input type="button" class="btn btn-primary" onclick="location.href='<?php if($fromflag == 0){echo $this->createWebUrl('mass',array());}else{echo $this->createWebUrl('mass',array('fromflag'=>1));} ?>'" name="add" id="buttonadd" value="创建新群发消息" style="margin-top:20px;"/>
<form name ="content" onSubmit="return validateform()" action="<?php echo $this->createWebUrl('masslist',array('gweid' => $gweid,'fromflag'=>1));?>" method="get" enctype="multipart/form-data">	
	<div class="panel panel-default" style="margin-right:30px; margin-top:20px">
		<div class="panel-heading">群发页面列表</div>
		<table class="table table-striped" width="800" bgoodsindex="1" align="center">
			<tbody>
				<tr>
					<td colspan=10 scope="col" width="100" align="left" >
						<select id="range" name="range" class="sltfield" style="margin-right:3px">
							<option value="">请选择
							<option value="all">全部</option>
							<option value="id">群发编号</option>
							<option value="mass_name">群发名称</option>
						</select>
						<input type="hidden" id="module" name="module" value="<?php echo $_GET['module'];?>" />
						<input type="hidden" id="do" name="do" value="<?php echo $_GET['do'];?>" />
						<input id="indata" class="sltfield" name="indata" value="<?php echo $_GET['indata'];?>" />
						<input type="hidden" name="beIframe" value="1">
						<input id="search1" class="btn btn-sm btn-warning" type="submit" value="查询" />
						<input type="hidden" name="fromflag" value="<?php echo $fromflag; ?>">
					</td>
				</tr>
				<tr>
					<td scope="col" width="10%" align="center" style="font-weight:bold">群发编号</td>
					<td scope="col" width="15%" align="center" style="font-weight:bold">群发名称</td>
					<td scope="col" width="15%" align="center" style="font-weight:bold">类型</td>
					<td scope="col" width="15%" align="center" style="font-weight:bold">操作</td>
				</tr>
				<?php
				if(is_array($list) && !empty($list)){
					foreach($list as $mass){
				 ?>			
				<tr>
					<td align="center"><?php echo $mass['id']; ?></td>
					<td align="center"><?php echo $mass['mass_name']; ?></td>
					<td align="center">
					<?php if ($mass['mass_type'] == '1') {
								echo "图文消息";
							} else {
								echo "文本消息";
					} ?>
					</td>
					<td class="row" align="center">
						<input type="button" class="btn btn-sm btn-warning" onclick="massdel('<?php echo $mass['id']; ?>',this)" value="删除"></button>
						<input type="button" class="btn btn-sm btn-info" onclick="location.href='<?php echo $this->createWebUrl('mass',array('massid' => $mass['id'],'fromflag' => $fromflag));?>'" name="massupdate" id="massupdate" value="更新">
						<input type="button" class="btn btn-sm btn-success" onclick="location.href='<?php echo $this->createWebUrl('statisticslist',array('massid' => $mass['id'],'fromflag' => $fromflag));?>'" name="statisticslist" id="statisticslist" value="群发详情">
					</td>		
				</tr>
				<?php
				}}
			?>
			</tbody>
		</table>
	</div>
</form>	
	<?php echo $pager;?>
</div>

 <script language="javascript" type="text/javascript">
	$(function(){
		if( $('#range').val() == 'all')
			$("#indata").hide();
		$('#range').change( function(){
				if($(this).val() == 'all'){
					$("#indata").hide();//隐藏
					$("#indata").val("");  	
				}else{
					$("#indata").show();//显示				
				}
				
			})
		}
	);
	
	$('#range').val('<?php echo $_GET['range'];?>');

	function checknull(obj, warning){
		if (obj.value == "") {
			alert(warning);
			obj.focus();
			return true;
		}
		return false;
	}
	isSubmitting=false;
	function massdel(id,obj){
		
		if(isSubmitting)
		return false;
		isSubmitting = true;
		$deletedobj = $(obj).parent().parent();
		
		if(confirm("确定删除吗？")){	
			$.ajax({
				url:window.location.href, 
				type: "POST",
				data:{'mass_del':'isDel','massid':id},
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

	function validateform(){
		
		var selectone = document.getElementById("range"); 
		var index = selectone.selectedIndex;
		var value = selectone.options[index].value; 
		
		if(value != "all"){
			if (checknull(document.content.indata, "请输入查询内容!") == true) {
				return false;
			}
			return true; 
		}else{
			return true;
		}
	}
	<?php if(empty($list)&&!empty($search_condition)){?>
	alert('没有符合该条件的查询结果');
	<?php } ?>
	</script>
</html>