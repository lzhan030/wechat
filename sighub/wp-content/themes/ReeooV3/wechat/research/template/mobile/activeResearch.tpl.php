<?php defined('IN_IA') or exit('Access Denied');?><?php include template('header', TEMPLATE_INCLUDEPATH);?>
<style>
body{background:#ECECEC;}
.research-thumb{width:100%;}
</style>
<div class="research">
	<div class="mobile-div img-rounded">
		<div class="mobile-hd"><?php echo $category['id'] != 0 ? "[ {$category['name']} ] - " : ""; ?>进行中的预约活动</div>
		<div class="mobile-content">
		<?php if(empty($notStartResearch) && empty($inProcess)){?>
		<div class="alert alert-success">
			<strong>暂无预约活动！</strong>
		</div>
		<?php } ?>
		<?php foreach($notStartResearch as $item){ ?>
			<div class="panel panel-<?php echo $item['restday']<=2?'danger':'success';?>">
				<div class="panel-heading">
				<?php echo $item['title']; ?><span style="float: right;"><?php echo $item['restday']==1?'明天开始':"还有{$item['restday']}天开始";?></span>
				</div>
                <div class="panel-body">预约有效期：<?php echo $item['startdate'];?> - <?php echo $item['enddate'];?><a class="btn btn-small btn-default" style="float: right;" type="button">尚未开始</a></div>
			</div>
		<?php } ?>
		<?php foreach($inProcess as $item){?>
			<div class="panel panel-<?php echo $item['restday']<=2?'danger':'success';?>">
				<div class="panel-heading">
				<?php echo $item['title']; ?><span style="float: right;"><?php echo $item['restday']===0?'今天截止':($item['restday']=='1'?'明天截止':"还有{$item['restday']}天截止")?></span>
				</div>
                <div class="panel-body">预约有效期：<?php echo $item['startdate'];?> - <?php echo $item['enddate'];?><a class="btn btn-small btn-info" style="float: right;" type="button" href="<?php echo $this -> createMobileUrl('research', array('gweid' => $_W['gweid'], 'id' => $item['reid'] ));?>">查看该预约</a></div>
			</div>
		<?php } ?>
		</div>
	</div>
	<div style="margin-right:3%;text-align:right;">
		<a class="btn btn-small btn-inverse" type="button" style="margin-right:3%;" href="<?php echo $this -> createMobileUrl('EndedResearch', array('gweid' => $_W['gweid'], 'category' => $_GPC['category'] ));?>">历史预约</a>
		<span class="icon-arrow-right"></span>
	</div>
</div>
<?php 
$title = "查看进行中的预约";
$content = "点击查看查看进行中的预约列表和详情。";
include $this->template('footer');?>
