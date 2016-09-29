<?php defined('IN_IA') or exit('Access Denied');?><?php include template('header', TEMPLATE_INCLUDEPATH);?>
<style>
body{background:#ECECEC;}
.research-thumb{width:100%;}
.alert-link{color: #843534; font-weight: 700;}
a{text-decoration: none;}
</style>
<div class="research">
	<?php if(!$this -> has_member_module(true) && empty($_W['fans']['from_user'])){?>
	<div role="alert" class="mobile-div alert alert-danger" style="   background-color: #eed3d7;">当前无法识别您的微信账号，请返回微信发送关键词重试。</div>
	
	<?php
		$list = array();
        $raw_count = 0;
        $reject_count = 0;
	}?>
	<div class="mobile-div img-rounded">
		<div class="mobile-hd">预约统计</div>
		<div class="mobile-content">
		您还有<?php echo intval($raw_count);?>个预约未被处理，有<?php echo intval($reject_count);?>个预约被拒绝。
		</div>
	</div>
	<div class="mobile-div img-rounded">
		<div class="mobile-hd">您的预约详情</div>
		<div class="mobile-content">
		<?php foreach($list as $item){ ?>
			<div class="panel panel-<?php echo $item['status']===NULL?'info':($item['status']=='0'?'warning':'success')?>">
				<div class="panel-heading">
				<?php echo $item['title']; ?><span style="float: right;"><?php echo $item['status']===NULL?'预约未处理':($item['status']=='0'?'预约被拒绝':'预约已通过')?></span>
				</div>
                <div class="panel-body">预约时间：<?php echo date('Y-m-d H:i:s',$item['time']);?></div>
				<?php if($item['status']=='0'){ ?>
				<div class="panel-body" style="padding-top:0px;">拒绝原因：
				<pre style="padding-left:0px;padding-top:0px;padding-bottom:0px;background-color:#fff;border:none;font:14px/1.5 'Microsoft Yahei','Simsun'" class="mobile-content"><?php echo $item['reason']; ?></pre></div>
				<?php } ?>
				<div class="panel-body"><a class="btn btn-small btn-info" style="float: right;" type="button" href="<?php echo $this -> createMobileUrl('myresearch', array('gweid' => $_W['gweid'], 'id' => $item['rerid'] ));?>">预约详情</a></div>
			</div>
		<?php } ?>
		</div>
	</div>

</div>

<?php 
$title = "查看预约列表";
$content = "点击查看参与的预约列表和详情。";
include $this->template('footer');?>
