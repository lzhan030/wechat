<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/init.css">
<style>
	.panel{border-radius: 0px; -webkit-box-shadow: 0 0px 0px rgba(0,0,0,0.05); box-shadow: 0 0px 0px rgba(0,0,0,0.05);}
	.sltfield{height: 34px;padding: 6px 12px;font-size: 14px;line-height: 1.428571429;color: #555;vertical-align: middle;background-color: #fff;background-image: none;border: 1px solid #ccc;border-radius: 4px;-webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);-webkit-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;}
</style>

<div class="main_auto">
	<div class="main-title">
	<div class="title-1">当前位置：<font class="fontpurple">微投票</font></div>
</div>
<input type="button" class="btn btn-primary" onclick="location.href='<?php echo $this -> createWebUrl('edit',array())?>'" name="add" id="buttonadd" value="创建新微投票活动" style="margin-top:20px;">
<div class="panel panel-default" style="margin-right:50px; margin-top:20px">
	<div class="panel-heading">
		<form name ="content" onSubmit="return validateform()" action="" method="get">	
			 <select id="range" name="range" class="sltfield" style="margin-right:3px">
				<option value="">请选择
				<option value="all">全部</option>
				<option value="vote_id">微投票编号</option>
				<option value="vote_name">微投票名称</option>
			 </select>
			<input type="hidden" id="module" name="module" value="<?php echo $_GET['module'];?>" />
			<input type="hidden" id="do" name="do" value="<?php echo $_GET['do'];?>" />
			<input id="indata" class="sltfield" name="indata" value="<?php echo $_GET['indata'];?>" />
			<input type="hidden" name="beIframe" value="1">
			<input class="btn btn-info btn-sm" type="submit" value="查询"/>
		</form>
	</div>
	<table class="table table-striped" width="800" border="0" align="center">
		<tbody>
			<tr>
				<td scope="col" align="center" style="font-weight:bold">编号</td>
				<td scope="col" align="center" style="font-weight:bold">名称</td>
				<td scope="col" align="center" style="font-weight:bold">手机端URL</td>
				<td scope="col" align="center" style="font-weight:bold">投票数</td>
				<td scope="col" align="center" style="font-weight:bold">操作</td>
			</tr>
			<?php
				if(is_array($list) && !empty($list)){
					foreach($list as $element){
				 ?>
			<tr data-vote-id="<?php echo $element['id']; ?>">
				<td align="center"><?php echo $element['id']; ?> </td>
				<td align="center"><?php echo $element['title']; ?></td>
				<td align="center"><input type="text" class="form-control" readonly="readonly"  value="<?php echo $this->createMobileUrl('index',array('gweid'=> $_W['gweid'],'id'=>$element['id'])); ?>"></td>
				<td align="center"><?php echo $element['votenum']; ?></td>
				<td align="center">
					<a type="button" name="delete" class="btn btn-sm btn-warning" href="#">删除</a>
					<a type="button" class="btn btn-sm btn-info" href="<?php echo $this->createWebUrl('edit',array('id'=>$element['id'])) ?>">修改活动</a>
					<a type="button" class="btn btn-sm btn-success" href="<?php echo $this->createWebUrl('display',array('id'=>$element['id'])) ?>">票数统计</a>
				</td>
			</tr>
			<?php }}?>
		</tbody>
	</table>
	
</div>
<?php echo $pager;?>
 <script language="javascript">
$(function(){
		if( $('#range').val() == 'all' || $('#range').val() == '')
			$("#indata").hide();
		$('#range').change( function(){
				if($(this).val() == 'all'){
					$("#indata").hide();//隐藏
					$("#indata").val("");  
				}else{
					$("#indata").show();//显示
				}
				
			})
		$("a[name='delete']").click(function(){
			$deleteobj = $(this).parent().parent();
			if(confirm("确定要删除这个微投票活动吗？"))
				jQuery.post(
					"<?php echo $this->createWebUrl('VoteDelete',array())?>",
					{vote_id:$(this).parent().parent().data('vote-id')},
					function(data){
						if(data.status == 'success'){
							$deleteobj.remove();
							window.location.reload();
						}else{
							alert("网络异常，请重试");
							window.location.reload();
						}
					},
					'json'
				).fail(function(){
					alert("网络异常，请重试");
					window.location.reload();
				});
		});
	}
);
	$('#range').val('<?php echo $_GET['range'];?>');

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
		data = $('#indata').val();
		
	if(data==""){
		alert("请输入查询内容");
		return false;
	}
	return true;
	  //alert(value);
} 	

	<?php if(empty($list)&&!empty($search_condition)){?>
	alert('没有符合该条件的查询结果');
	<?php } ?>
</script>