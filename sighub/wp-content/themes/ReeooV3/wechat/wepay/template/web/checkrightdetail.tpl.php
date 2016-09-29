<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css" />
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/gradeclass.css" />
		<title>更新维权信息</title>
		<style>label{font-weight:normal;}</style>
	</head>
<div class="main_auto">
	<div class="main-title" style="margin-left:30px;">
		<div class="title-1">当前位置：<a href="<?php echo $this->createWebUrl('index',array());?>">微支付</a> > <a href="<?php echo $this->createWebUrl('rightmanage',array());?>">维权列表</a> > <font class="fontpurple">维权详细信息</font></div>
	</div>
	<form id="rightinfoedit" action="" method="post" onsubmit="return checkinputinfo();">
		<?php
		if( isset($_POST['right_id'])&&!Empty($_POST['right_id'])){ ?>
			<script>
				alert('提交成功');
			</script>
		<?php } ?>
		<div style="margin-left:180px; margin-top:50px;"> 
			<div class="ri-line">
				<div class="ri-label"><label for="rightid">维权编号: </label></div>
				<div class="ri-input"><input type="text" id="right_id" class="form-control" readonly="readonly" name="right_id"  readonly="readonly" name="rights_notes" value="<?php echo $rightslist['id'];?>" ></div>
			</div>
			<div class="ri-line">
				<div class="ri-label"><label for="create_time">创建时间:</label></div>	
				<div class="ri-input"><input type="text" id="right_create_time" class="form-control" readonly="readonly" name="right_create_time" value="<?php echo $rightslist['create_time'];?>" > </div>
			</div>
			<div class="ri-line">
				<div class="ri-label"><label for="out_trade_no">交易订单号: </label></div>	
				<div class="ri-input"><input type="text" id="right_out_trade_no" class="form-control" name="right_out_trade_no"  readonly="readonly" name="rights_notes" value="<?php echo $rightslist['out_trade_no'];?>" > </div>
			</div>
			<div class="ri-line">
				<div class="ri-label"><label for="feedbackid">投诉单号: </label></div>
				<div class="ri-input"><input type="text" id="right_feedbackid" class="form-control" name="right_feedbackid" readonly="readonly" name="rights_notes" value="<?php echo $rightslist['feedbackid'];?>" ></div>
			</div>
			<div class="ri-line">
				<div class="ri-label"><label for="reason">用户投诉原因:</label></div>	
				<div class="ri-input"><input type="text" id="reason" class="form-control" name="reason" readonly="readonly" name="rights_notes" value="<?php echo $RIGHT_REASON[$rightslist['reason']];?>" > </div>
			</div>
			<div class="ri-line">
				<div class="ri-label"><label for="solution">解决方案:</label></div>	
				<div class="ri-input"><input type="text" id="right_solution" class="form-control" readonly="readonly" name="right_solution" value="<?php echo $RIGHT_SOLUTION[$rightslist['solution']];?>" > </div>
			</div>
			<div class="ri-line">
				<div class="ri-label"><label for="extinfo">备注:</label></div>	
				<div class="ri-input"><input type="text" id="right_extinfo" class="form-control" readonly="readonly" name="right_extinfo" value="<?php echo $rightslist['extinfo'];?>" > </div>
			</div>
			<div class="ri-line">
				<div class="ri-label"><label for="picurl">图片凭证:</label></div>
				<div>
				<?php
					$picurls = explode(";",$rightslist['picurl']);
					foreach($picurls as $purl){
					$upload =wp_upload_dir();
					$url=$upload['baseurl'].$purl;
				?> 
				<?php 
				if(!empty($purl)){ ?>
				<div><ul><li class="right_li"><img src='<?php echo $url;?>' width='80' height='80'></li></ul></div> 
				<?php }}?>
				</div>
			</div>
			<div class="ri-line">
			<div  style="border-top: 1px solid #ccc;height: 50px; vertical-align: bottom;" >
				<div class="ri-label"><label style="font-weight:bold;">维权处理</label></div>	
				<div></div>
			</div>	
			</div>				
			<div class="ri-line" >
				<div class="ri-label"><label for="rights_status">维权状态:</label></div>
				<div class="ri-input">
					<select name="rights_status" class="form-control" size="1" type="text" id="rights_status" maxlength="20">
						<option value="1" <?php if($rightslist['rights_status'] == 1) echo 'selected="selected"'; ?> >未处理</option>
						<option value="2" <?php if($rightslist['rights_status'] == 2) echo 'selected="selected"'; ?> >处理中</option>
						<option value="3" <?php if($rightslist['rights_status'] == 3) echo 'selected="selected"'; ?> >已解决</option>
					</select>
				</div>
			</div>
			<div class="ri-line">
				<div class="ri-label"><label for="rights_result">维权结果:</label></div>
				<div class="ri-input">
					<select name="rights_result" class="form-control" size="1" type="text" id="rights_result" maxlength="20" <?php if($rightslist['rights_status'] != 3) echo 'disabled="disabled"'; ?>>
						<option value="" >请选择</option>
						<option value="1" <?php if($rightslist['rights_result'] == 1) echo 'selected="selected"'; ?> >退款退货</option>
						<option value="2" <?php if($rightslist['rights_result'] == 2) echo 'selected="selected"'; ?> >退款不退货</option>
						<option value="3" <?php if($rightslist['rights_result'] == 3) echo 'selected="selected"'; ?> >暂不处理</option>
					</select>
				</div>
			</div>
			<div class="ri-line">
				<div class="ri-label"><label for="rights_notes">维权结果备注:</label></div>
				<div class="ri-input"><input type="text" id="rights_notes" class="form-control" name="rights_notes" value="<?php echo $rightslist['rights_notes'];?>" <?php if($rightslist['rights_status'] != 3) echo 'disabled="disabled"'; ?>></div>
			</div>
			<div class="ri-line">
				<div class="ri-label"><label for="msgtype">用户最终反馈: </label></div>		
				<div class="ri-input"><input type="text" id="right_msgtype" class="form-control" name="right_msgtype" readonly="readonly" name="rights_notes"  value="<?php echo$RIGHT_MSGTYPE[$rightslist['msgtype']];?>" > </div>
			</div>
			<div class="ri-line">
				<div class="ri-label"><label for="end_time">维权完成时间:</label></div>
				<div class="ri-input"><input type="text" id="end_time" class="form-control" readonly="readonly" name="end_time" value="<?php if($rightslist['rights_status'] == 3) echo  $rightslist['end_time'];?>" > </div>
			</div>
	</div>	
	<div style="margin-left:33%;margin-top:60px;">
	    <input type="submit"  class="btn btn-primary btn-submit" value="提交" id="sub1" />
		<a href="<?php echo $this->createWebUrl('rightmanage',array());?>"><input type="button" class="btn btn-default btn-return" value="返回" id="sub3" /></a>
	</div>
	<div style="position:absolute;right:240px;top:545px;">
		<input type="button" class="btn btn-sm btn-warning" onclick="createRefund('<?php echo $rightslist['out_trade_no']?>')" name="refund_order" id="refund_order" value="退款" <?php if($rightslist['rights_status'] != 3 || $rightslist['rights_result'] == 3) echo 'style="display:none;"';?>/>
	</div>
	</div>
	</form>
</div>
<script language="javascript" type="text/javascript">
	function checkinputinfo(){
			if($('#rights_status').val()=="3"){
				if($('#rights_result').val()==""){
					alert("请选择维权结果！");
					return false;
				}
				else
				return true;
			}
	}
	$('#rights_status').change(function(){
		if($(this).val()==3){
			$('#rights_result').removeAttr('disabled');
			$('#rights_notes').removeAttr('disabled');
			if($('#rights_result').val() == 1 || $('#rights_result').val() == 2)
				$('#refund_order').css('display','inline');
		}else{
			$('#rights_result').val("");
			$('#rights_result').attr('disabled','disabled');
			$('#rights_notes').attr('disabled','disabled');
			$('#refund_order').css('display','none');
		}
	});
	$('#rights_result').change(function(){
		if($(this).val() == 1 || $(this).val() == 2)
			$('#refund_order').css('display','inline');
		else
			$('#refund_order').css('display','none');
	});
	function createRefund(id){
		window.open('<?php echo $this->createWebUrl('createRefund',array('norefresh' => '1'));?>'+'&orderid='+id,'_blank','height=620,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no');
	}
</script>
</html>