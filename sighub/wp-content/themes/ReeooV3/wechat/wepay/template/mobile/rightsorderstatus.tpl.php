<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/gradeclass.css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite_mobile.css">
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/wechat/welinked/style/firstpage.css">
	<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/we7/style/font-awesome.css" />
	<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/we7/style/common.css?v=<?php echo TIMESTAMP;?>" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/video.css">
	<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.css">
	<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/shopping.mobile.css" />
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/jquery-1.11.1.min.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/bootstrap.js"></script>
	<title>维权单状态</title>
	<style>
		.rightvalue{color:#333;margin-left:10px;}
	</style>	
</head>
<div class="mobile-div img-rounded">
	<div class="mobile-hd">维权单详情</div>
	<div style="margin-right:3%;margin-left:5%;">
		<div class="mobile-content">
		<?php
		if(isset($_POST['msgtype'])){ ?>
			<script>
				alert('您的维权反馈结果提交成功');
			</script>
		<?php } ?>
			<form id="msgtypesub" action="<?php echo $this->createMobileUrl('RightsOrderStatus',array('gweid' => $gweid,'out_trade_no' => $out_trade_no,'feedbackid' => $feedbackid,'goodsgid'=>$goodsgid));?>" enctype="multipart/form-data" method="post">
				<div>
					<label for="feedbackid">维权单号：</label><font class="rightvalue"><?php echo $right['feedbackid'];?></font>
				</div>
				<div>
					<label for="outtradeno">订单编号：</label><font class="rightvalue"><?php echo $right['out_trade_no']; ?></font>
				</div>				
				<div>
					<label for="create_time">提交时间：</label><font class="rightvalue"><?php echo $right['create_time'];?></font>
				</div>
				<div>
					<label for="title">商品名称：</label><font class="rightvalue"><?php if( Empty( $goodsinfos )){ echo $ordersinfo['description'];} 
				else { foreach($goodsinfos as $ginfo) echo $ginfo['title'];}?></font>
				</div>

				<div>
					<label for="reason">投诉原因：</label><font class="rightvalue"><?php echo $RIGHT_REASON[$right['reason']];?></font>
				</div>
				<div>
					<label for="solution">希望处理方式：</label><font class="rightvalue"><?php echo $RIGHT_SOLUTION[$right['solution']];?></font>
				</div>
				<div>
					<label for="extinfo">备注：</label><font class="rightvalue"><?php echo $right['extinfo'];?></font>
				</div>
				<div>
					<label for="picurl">图片凭证：</label>
				</div>
				<div>
					<?php
						$picurls = explode(";",$right['picurl']);
						foreach($picurls as $purl){
							$upload =wp_upload_dir();
							$url=$upload['baseurl'].$purl;
						?> 
						<?php if(!empty($purl)){ ?>
						<img src='<?php echo $url;?>' width='80' height='80'>
						<?php }}?>
				</div>
		</div>
	</div>	
</div>
<div class="mobile-div img-rounded" style="margin-bottom:12%;">	
	<div class="mobile-hd">维权结果</font></div>
	<div style="margin-right:3%;margin-left:5%;">
		<div class="mobile-content">
				<div>
					<label for="rights_status">维权处理状态：</label>
					<font class="rightvalue">
						<?php if($right['rights_status'] == 1) echo '维权未处理'; ?> 
						<?php if($right['rights_status'] == 2) echo '维权处理中'; ?>
						<?php if($right['rights_status'] == 3) echo '维权已解决'; ?>
					</font>
				</div>
				<div>
					<?php if($right['rights_status'] == 3) {?>
					<label for="rights_result">处理结果：</label>
					<font class="rightvalue">
						<?php  if($right['rights_result']==1) echo '退款退货';?>
						<?php  if($right['rights_result']==2) echo '退款不退货';?>				
						<?php  if($right['rights_result']==3) echo '暂不处理';?>
					</font>
				</div>
				<div>
					<label for="rights_notes">处理结果备注：</label><font class="rightvalue"><?php echo $right['rights_notes'];?></font>
				</div>
				<div style="margin-top:15px;">	
					<label for="msgtype">用户处理结果反馈：</label>
					<select name="msgtype" class="form-control" size="1" type="text" id="msgtype" maxlength="30" style="height:34px;">
						<option value="" >请选择处理结果</option>
						<option value="confirm" <?php if($right['msgtype'] == 'confirm') echo 'selected="selected"'; ?>>同意维权处理结果</option>
						<option value="reject" <?php if($right['msgtype'] == 'reject') echo 'selected="selected"'; ?>>不同意维权处理结果</option>
					</select>
				</div>
				<div id="submsgtype" style="float:right;margin-top:20px;width:100%">
					<input type="submit" class="btn btn-large btn-success" value="提交" style="width:100%"/>
				</div>
				<div id="nomsgtype"style="float:right;margin-top:10px;width:100%">
					<input type="text" class="form-control" value="此维权单已被关闭,您可重新申请维权" readonly="readonly"/>
				</div>
				<?php } ?>	
			</form>
		</div>
	</div>
</div>
<div class="footerbar">
	<a style="width:33%;padding-top:15px" onclick="href='<?php echo $this->createMobileUrl('goodsinfo',array('gweid'=>$gweid,'goodsgid' => $goodsgid));?>'">首页</a>
	<a style="width:33%;padding-top:15px" onclick="href='<?php echo $this->createMobileUrl('myorderlist',array('gweid' => $gweid,'goodsgid' => $goodsgid)); ?>'">我的订单</a>
	<a style="width:33%;padding-top:15px" onclick="href='<?php echo $this->createMobileUrl('rightslists',array('gweid' => $gweid,'goodsgid' => $goodsgid));?>'">我的维权</a>
</div>
<script language="javascript" type="text/javascript">
	if( $('#msgtype').val() == ""){
			$("#submsgtype").show();
			$("#nomsgtype").hide();
	}
	else { $("#submsgtype").hide();
			$("#nomsgtype").show();
			$('#msgtype').attr('disabled','disabled');
	}
</script>
<?php  include $this -> template('footer');?>