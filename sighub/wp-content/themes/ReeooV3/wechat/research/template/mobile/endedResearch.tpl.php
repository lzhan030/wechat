<?php defined('IN_IA') or exit('Access Denied');?><?php include template('header', TEMPLATE_INCLUDEPATH);?>
<style>
body{background:#ECECEC;}
.research-thumb{width:100%;}
</style>
<div class="research">
	<div class="mobile-div img-rounded">
		<div class="mobile-hd"><?php echo $category['id'] != 0 ? "[ {$category['name']} ] - " : ""; ?>历史预约活动</div>
		<div class="mobile-content">
		<?php if(empty($endedResearch)){?>
		<div class="alert alert-success">
			<strong>暂无预约活动！</strong>
		</div>
		<?php } ?>
		<?php foreach($endedResearch as $item){ ?>
			<div class="panel panel-success">
				<div class="panel-heading">
				<?php echo $item['title']; ?><span style="float: right;">已结束</span>
				</div>
                <div class="panel-body">预约有效期：<?php echo $item['startdate'];?> - <?php echo $item['enddate'];?><a class="btn btn-small btn-default" style="float: right;" type="button">已结束</a></div>
			</div>
		<?php } ?>
		</div>
	</div>
	<div style="margin-left:3%;">
		<span class="icon-arrow-left"></span><a class="btn btn-small btn-inverse" type="button" style="margin-left:3%;" href="<?php echo $this -> createMobileUrl('ActiveResearch', array('gweid' => $_W['gweid'], 'category' => $_GPC['category'] ));?>">预约列表</a>
	</div>

</div>
<?php 
$title = "查看历史预约";
$content = "点击查看查看历史预约列表和详情。";
include $this->template('footer');?>
