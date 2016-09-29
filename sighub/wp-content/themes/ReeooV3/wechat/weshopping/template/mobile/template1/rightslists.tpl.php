<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this->template('header');?>
<?php include $this->template('common');?>

<html>
	<head>
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/we7/style/font-awesome.css" />
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/we7/style/common.css?v=<?php echo TIMESTAMP;?>" />
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/video.css">
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/we7/script/common.js?v=<?php echo TIMESTAMP;?>"></script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/we7/script/emotions.js"></script>
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite_mobile.css">
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/wechat/welinked/style/firstpage.css">
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/shopping.mobile.css" />
		<title>维权</title>		
		<style>
			.mobile-content{border-bottom:1px solid #ccc;}
			.rightvalue{color:#333;margin-left:10px;}
		</style>
	</head>
	<?php
		if( isset($_POST['reason'])&&!Empty($_POST['reason'])){ ?>
			<script>
				alert('您的维权申请提交成功，商家会尽快处理');
			</script>
	<?php } ?>
	<div class="mobile-hd">维权列表<?php echo "("."共".$rightcounts."条".")"; ?></div>
	<div class="research" style="height:auto;overflow:hidden;margin-bottom:10%;">
	<?php if(is_array($rightsinfo)&& !empty($rightsinfo)){
	foreach( $rightsinfo as $rsinfo){ ?>
	<div class="main_auto" style="margin-right:3%;margin-left:3%;">
		<div class="mobile-content">
			<div>
				<label>维权单号:</label><font class="rightvalue"><?php echo $rsinfo['feedbackid']; ?></font>
			</div>
			<div>
				<label>订单编号:</label><font class="rightvalue"><?php echo $rsinfo['out_trade_no']; ?></font>
			</div>
			<div>
				<label>提交时间:</label><font class="rightvalue"><?php echo $rsinfo['create_time']; ?></font>
			</div>
			<div>
				<label>维权状态:</label><font class="rightvalue"><?php if($rsinfo['rights_status'] == 1) echo '维权未处理'; ?> 
				<?php if($rsinfo['rights_status'] == 2) echo '维权处理中'; ?>
				<?php if($rsinfo['rights_status'] == 3) echo '维权已解决'; ?></font>
			</div>
				<?php if($rsinfo['rights_status'] == 3) { ?>
			<div>
				<label>维权结果:</label><font class="rightvalue"><?php if($rsinfo['rights_result'] == 1) echo '退款退货'; ?> 
				<?php if($rsinfo['rights_result'] == 2) echo '退款不退货'; ?>
				<?php if($rsinfo['rights_result'] == 3) echo '暂不处理'; ?></font>
			</div>
				<?php } ?>
			<div style="float:right;">
				<div style="float:left;margin-right:10px;">
					<?php if ($rsinfo['rights_status']==3) echo "<a href='javascript:deleteright({$rsinfo['id']})' data-right-id={$rsinfo['id']}>[删除]</a>"; ?>
				</div>
				<div style="float:left;">
					<a href="<?php echo $this->createMobileUrl('rightsorderstatus',array('gweid' => $gweid,'out_trade_no' => $rsinfo['out_trade_no'],'feedbackid' => $rsinfo['feedbackid'],'goodsgid'=>$goodsgid));?>" >[维权详情]</a>  	
				</div>
			</div>
		</div>
	</div>	
	<?php  }
   }
   ?>
  </div> 

<script language='javascript'>
		$.ajaxSetup({  
			async : false  
		});
		function deleteright(rid)
		{	
			if(confirm("确定删除吗？")){
				$.ajax({
					async:false,
					url:window.location.href, 
					type: "POST",
					data:{'rightdel':'deleteright','id':rid},
					success: function(data){
						if (data.status == 'error'){
							alert(data.message);
						}else if (data.status == 'success'){
						    alert(data.message);
							location.reload();
						}
					},
					error: function(data){
						alert("出现错误,请重试");
					},
					dataType: 'json'
				});	
			}
			
			
	    }
</script>
<?php include $this->template('footerbar');?>
<?php include $this->template('footer');?>
