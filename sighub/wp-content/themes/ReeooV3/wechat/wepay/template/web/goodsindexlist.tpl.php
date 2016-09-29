<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
<style>
	.sltfield{height: 34px;padding: 6px 12px;font-size: 14px;line-height: 1.428571429;color: #555;vertical-align: middle;background-color: #fff;background-image: none;border: 1px solid #ccc;border-radius: 4px;-webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);-webkit-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;}
	.panel{border-radius: 0px; -webkit-box-shadow: 0 0px 0px rgba(0,0,0,0.05); box-shadow: 0 0px 0px rgba(0,0,0,0.05);}
</style>
<div class="main_auto">
<div class="main-title">
	<div class="title-1">当前位置：<a href="<?php echo $this->createWebUrl('index',array());?>">微支付</a> > <font class="fontpurple">网页支付管理</font></div>
</div>
<input type="button" class="btn btn-primary" onclick="location.href='<?php echo $this->createWebUrl('goodsindexhandle',array());?>'" name="add" id="buttonadd" value="创建新网页支付" style="margin-top:20px;"/>
<form name ="content" onSubmit="return validateform()" action="<?php echo $this->createWebUrl('goodsindexlist',array('gweid' => $gweid));?>" method="get" enctype="multipart/form-data">	
	<div class="panel panel-default" style="margin-right:30px; margin-top:20px">
		<div class="panel-heading">网页支付页面列表</div>
		<table class="table table-striped" width="800" bgoodsindex="1" align="center">
			<tbody>
				<tr>
					<td colspan=10 scope="col" width="100" align="left" >
						<select id="range" name="range" class="sltfield" style="margin-right:3px">
							<option value="">请选择
							<option value="all">全部</option>
							<option value="id">链接编号</option>
							<option value="goodsindex_name">链接名称</option>
						</select>
						<input type="hidden" id="module" name="module" value="<?php echo $_GET['module'];?>" />
						<input type="hidden" id="do" name="do" value="<?php echo $_GET['do'];?>" />
						<input id="indata" class="sltfield" name="indata" value="<?php echo $_GET['indata'];?>" />
						<input type="hidden" name="beIframe" value="1">
						<input id="search1" class="btn btn-sm btn-warning" type="submit" value="查询" />
					</td>
				</tr>
				<tr>
					<td scope="col" width="10%" align="center" style="font-weight:bold">链接编号</td>
					<td scope="col" width="15%" align="center" style="font-weight:bold">链接名称</td>
					<td scope="col" width="60%" align="center" style="font-weight:bold">链接</td>
					<td scope="col" width="15%" align="center" style="font-weight:bold">操作</td>
				</tr>
				<?php
				if(is_array($list) && !empty($list)){
					foreach($list as $goodsindex){
				 ?>			
				<tr>
					<td align="center"><?php echo $goodsindex->id; ?></td>
					<td align="center"><?php echo $goodsindex->goodsindex_name; ?></td>
					<td align="center"><input type="text" class="form-control" readonly="readonly"  value="<?php echo $this->createMobileUrl('goodsinfo',array('gweid'=>$gweid,'goodsgid' => $goodsindex->id)); ?>"></td>
					<td class="row" align="center">
						<input type="button" class="btn btn-sm btn-warning" onclick="goodsindexdel('<?php echo $goodsindex->id; ?>',this)" value="删除"></button>
						<input type="button" class="btn btn-sm btn-info" onclick="location.href='<?php echo $this->createWebUrl('goodsindexhandle',array('goodsindexid' => $goodsindex->id));?>'" name="goodsindexupdate" id="goodsindexupdate" value="更新">
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
	function goodsindexdel(id,obj){
		
		if(isSubmitting)
		return false;
		isSubmitting = true;
		$deletedobj = $(obj).parent().parent();
		
		if(confirm("确定删除吗？")){	
			$.ajax({
				url:window.location.href, 
				type: "POST",
				data:{'goodsindex_del':'isDel','goodsindexid':id},
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