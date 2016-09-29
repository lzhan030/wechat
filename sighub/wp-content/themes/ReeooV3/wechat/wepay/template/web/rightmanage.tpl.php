<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
<?php
	$gweid=$_SESSION['GWEID'];	
?>
 <script language="javascript">
	top.rightsreminder();	
</script>
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/uploadexcel.css">
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/init.css">
<style type="text/css">
	.sltfield{height: 34px;padding: 6px 12px;font-size: 14px;line-height: 1.428571429;color: #555;vertical-align: middle;background-color: #fff;background-image: none;border: 1px solid #ccc;border-radius: 4px;-webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);-webkit-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;}
	.panel{border-radius: 0px; -webkit-box-shadow: 0 0px 0px rgba(0,0,0,0.05); box-shadow: 0 0px 0px rgba(0,0,0,0.05);}
</style>
<div class="main_auto">
	<div class="main-title">
		<div class="title-1">当前位置：<a href="<?php echo $this->createWebUrl('index',array());?>">微支付</a> > <font class="fontpurple">维权信息列表</font></div>
	</div>	
<form name ="content" onSubmit="return validateform()" action="" method="get" enctype="multipart/form-data">
<div class="panel panel-default" style="width: 95%;margin-top:20px">
		<div class="panel-heading">维权列表</div>
		<div class="panel-heading">
				 <select id="range" name="range" class="sltfield" style="margin-right:3px">
					<option value="">请选择
					<option value="all">全部</option>
					<option value="id">维权编号</option>
					<option value="out_trade_no">交易单号</option>
					<option value="feedbackid">投诉单号</option>
					<option value="rights_status">维权状态</option>
					<option value="msgtype">用户反馈意见</option>
				 </select>
			<input type="hidden" id="module" name="module" value="<?php echo $_GET['module'];?>" />
			<input type="hidden" id="do" name="do" value="<?php echo $_GET['do'];?>" />
			<input type="hidden" id="gweid" name="gweid" value="<?php echo $_GET['gweid'];?>" />
			<input id="indata" class="sltfield" name="indata" value="<?php echo $_GET['indata'];?>" />
			<input type="hidden" name="beIframe" value="1">
			<select id="rights_status" name="rights_status" class="sltfield" style="margin-right:3px">
				<option value="">请选择</option>
				<?php foreach($this->RIGHT_STATUS as $status_key => $status_value){?>
				<option value="<?php echo $status_key;?>"><?php echo $status_value;?></option>
				<?php }?>
			 </select>
			 <select id="msgtype" name="msgtype" class="sltfield" style="margin-right:3px">
				<option value="">请选择</option>
				<?php foreach($this->RIGHT_MSGTYPE as $status_key => $status_value){?>
				<option value="<?php echo $status_key;?>"><?php echo $status_value;?></option>
				<?php }?>
			 </select>
			<input id="search1" class="btn btn-info btn-sm" type="submit" value="查询"/>
		</div>
	<table class="table table-striped">
		<tbody>
			<tr>
				<td scope="col" width="50" align="center" style="font-weight:bold">编号</td>
				<td scope="col" width="80" align="center" style="font-weight:bold">日期</td>
				<td scope="col" width="100" align="center" style="font-weight:bold">交易订单号</td>
				<td scope="col" width="100" align="center" style="font-weight:bold">投诉单号</td>
				<td scope="col" width="100" align="center" style="font-weight:bold">用户投诉原因</td>
				<td scope="col" width="80" align="center" style="font-weight:bold">维权状态</td>
				<td scope="col" width="100" align="center" style="font-weight:bold">用户最终反馈</td>
				<td scope="col" width="110" align="center" style="font-weight:bold">操作</td>
			</tr>
					<?php
					if(is_array($rslist) && !empty($rslist)){
						foreach($rslist as $right){
					?>
			<tr>
					<td align="center"><?php if($right['reds'] == 0){ ?><span style="color: red;">[新]</span><?php } ?><?php echo $right['id']; ?></td>
					<td align="center"><?php echo $right['create_time']; ?></td>
					<td align="center"><a href="<?php echo $this -> createWebUrl('orderinfo',array('orderid' => $right['out_trade_no']));?>"><?php echo $right['out_trade_no']; ?></a></td>
					<td align="center"><?php echo $right['feedbackid']; ?> </td>
					<td align="center"><?php echo $this -> RIGHT_REASON[$right['reason']]; ?> </td>
					<td align="center"><?php echo $this -> RIGHT_STATUS[$right['rights_status']]; ?> </td>
					<td align="center"><?php echo $this -> RIGHT_MSGTYPE[$right['msgtype']]; ?> </td>
				<td class="row" align="center"><input type="hidden"  value="308" maxlength="100"> 
				<input type="button" class="btn btn-sm btn-warning" onclick="delright('<?php echo $right['id'] ?>',this)" name="del" id="buttondel" value="删除"> 
				<input type="button" class="btn btn-sm btn-info" onclick="location.href='<?php echo $this->createWebUrl('checkrightdetail',array('id' => $right['id']));?>'" name="upd" id="buttonupd" value="详情"> 
				</td>	
			</tr>
		<?php }}?>
		</tbody>
	</table>
</div>
</form>
		<?php echo $pager;?>
<script language='javascript'>
 	$(function(){
		if( $('#range').val() == 'all')
			$("#indata").hide();
		$('#range').change( function(){
				if($(this).val() == 'all'){
					$("#indata").hide();//隐藏
					$("#indata").val("");  
					$("#rights_status").hide();
					$("#msgtype").hide();
				}else if($(this).val() == 'rights_status'){
					$("#indata").hide();//隐藏
					$("#indata").val("");  
					$("#rights_status").show();
					$("#msgtype").hide();
				}else if($(this).val() == 'msgtype'){
					$("#indata").hide();//隐藏
					$("#indata").val("");  
					$("#rights_status").hide();
					$("#msgtype").show();
				}else{
					$("#indata").show();//显示
					$("#rights_status").hide();	
					$("#msgtype").hide();					
				}
				
			})
		}
	);
	$('#range').val('<?php echo $_GET['range'];?>');

	<?php if(!empty($search_condition)&&$search_condition=='rights_status'){?>
	$("#rights_status").show();
	$("#indata").hide();
	$('#rights_status').val('<?php echo $_GET['rights_status'];?>');
	$("#msgtype").hide();
	<?php }elseif(!empty($search_condition)&&$search_condition=='msgtype'){?>
	$("#rights_status").hide();
	$("#indata").hide();
	$("#msgtype").show();
	$('#msgtype').val('<?php echo $_GET['msgtype'];?>');
	<?php }else{ ?>
	$("#msgtype").hide();
	$("#rights_status").hide();
	$("#indata").show();
	<?php } ?>
	$('#range').val('<?php echo $_GET['range'];?>');
	<?php if(empty($rslist)&&!empty($search_condition)){?>
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
		var range = $('#range').val();
		var data = "";
		if(range == "all")
			return true;
			
		if(range == ""){
			alert("请选择查询条件！");
			return false;
		}
		
		if(range == "rights_status")
			data = $('#rights_status').val();
		else if(range == "msgtype")
			data = $('#msgtype').val();
		else
			data = $('#indata').val();
			
		if(data==""){
			alert("请输入查询内容");
			return false;
		}
		return true;
	} 	
	isSubmitting=false;
	function delright(id,obj){
		if(confirm("确定删除吗？")){
			if(isSubmitting)
			return false;
			isSubmitting = true;
			$deletedobj = $(obj).parent().parent();
		$.ajax({
			url:window.location.href, 
			type: "POST",
			data:{'rihgtsindex_del':'isDel','rihgtsindexid':id},
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
		}
	}
</script>