<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
<div class="main_auto">
<div class="main-title">
	<div class="title-1">当前位置：群发消息> <a href="<?php echo home_url().'/module.php?module=mass&do=masslist&fromflag='.$fromflag; ?>">群发管理</a>> <font class="fontpurple">群发详情</font></div>
</div>
<form name ="content" onSubmit="return validateform()" action="<?php echo $this->createWebUrl('statisticslist',array('gweid' => $gweid,'fromflag' => $fromflag));?>" method="get" enctype="multipart/form-data">	
	<div class="panel panel-default" style="margin-right:30px; margin-top:20px">
		<div class="panel-heading">群发详情列表</div>
		<table class="table table-striped" width="800" bgoodsindex="1" align="center">
			<tbody>
				<tr>
					<td colspan=10 scope="col" width="100" align="left" >
						<select id="range" name="range" class="sltfield" style="margin-right:3px">
							<option value="">请选择
							<option value="all">全部</option>
							<option value="wid">公众号</option>
						</select>
						<input type="hidden" id="module" name="module" value="<?php echo $_GET['module'];?>" />
						<input type="hidden" id="do" name="do" value="<?php echo $_GET['do'];?>" />
						<input id="indata" class="sltfield" name="indata" value="<?php echo $_GET['indata'];?>" />
						<input type="hidden" id="massid" name="massid" value="<?php echo $massid;?>" />
						<select id="wid_name" name="wid_name" class="sltfield" style="margin-right:3px">
							<option value="">请选择</option>
							<?php foreach($winfoarray as $key => $value){?>
							<option value="<?php echo $key;?>"><?php echo $value;?></option>
							<?php }?>
						</select>
						<input type="hidden" name="beIframe" value="1">
						<input id="search1" class="btn btn-sm btn-warning" type="submit" value="查询" />
					</td>
				</tr>
				<tr>
					<td scope="col" width="15%" align="center" style="font-weight:bold">公众号名称</td>
					<td scope="col" width="10%" align="center" style="font-weight:bold">发送状态</td>
					<td scope="col" width="10%" align="center" style="font-weight:bold">群发结果</td>
					<td scope="col" width="10%" align="center" style="font-weight:bold">粉丝数</td>
					<td scope="col" width="10%" align="center" style="font-weight:bold">过滤</td>
					<td scope="col" width="10%" align="center" style="font-weight:bold">发送成功数</td>
					<td scope="col" width="10%" align="center" style="font-weight:bold">发送失败数</td>
					<td scope="col" width="15%" align="center" style="font-weight:bold">时间</td>
				</tr>
				<?php
				if(is_array($list) && !empty($list)){
					foreach($list as $statistic){
				 ?>			
				<tr>
					<td align="center"><?php echo $winfoarray[$statistic['wid']]; ?></td>
					<td align="center"><?php echo $statistic['sendstatus']; ?></td>
					<td align="center">
					<?php  
						if(in_array($this ->SEND_STATE[$statistic['status']],$this ->SEND_STATE)){
							echo $this -> SEND_STATE[$statistic['status']];
						}else{
							echo $statistic['status'];
						}
					?>
					</td>
					<td align="center"><?php echo $statistic['totalcount']; ?></td>
					<td align="center"><?php echo $statistic['filtercount']; ?></td>
					<td align="center"><?php echo $statistic['sentcount']; ?></td>
					<td align="center"><?php echo $statistic['errorcount']; ?></td>
					<td align="center"><?php echo date("Y-m-d H:i:s", $statistic['time']); ?></td>
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
					$("#wid_name").hide();					
				}else if($(this).val() == 'wid'){
					$("#indata").hide();//隐藏
					$("#indata").val("");  
					$("#wid_name").show();
				}else{
					$("#indata").show();//显示	
					$("#wid_name").hide();					
				}
			})
		}
	);
	
	$('#range').val('<?php echo $_GET['range'];?>');
	
	<?php if(!empty($search_condition)&&$search_condition=='wid'){?>
	$("#wid_name").show();
	$("#indata").hide();
	$('#wid_name').val('<?php echo $_GET['wid_name'];?>');
	
	<?php }else{ ?>
	
	$("#wid_name").hide();
	$("#indata").show();
	<?php } ?>
	
	function checknull(obj, warning){
		if (obj.value == "") {
			alert(warning);
			obj.focus();
			return true;
		}
		return false;
	}
	

	function validateform(){
		
		var selectone = document.getElementById("range"); 
		var index = selectone.selectedIndex;
		var value = selectone.options[index].value; 
		
		var selecttwo = document.getElementById("wid_name"); 
		var indexstate = selecttwo.selectedIndex;
		var valuestate = selecttwo.options[indexstate].value;
		
		if((value != "all")&&(value != "wid")){
			if (checknull(document.content.indata, "请输入查询内容!") == true) {
				return false;
			}
			return true; 
		}else if(value == "wid"){
			if (checknull(selecttwo.options[indexstate], "请选择查询条件!") == true) {
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