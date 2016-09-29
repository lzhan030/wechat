<?php defined('IN_IA') or exit('Access Denied');?><?php include template('common/header', TEMPLATE_INCLUDEPATH);
$upload =wp_upload_dir();?>
<style type="text/css">
table li{padding:5px 0;}
small a{color:#999;}
</style>
<div class="main-title">
	<div class="title-1">当前位置：首页&gt;<a href="<?php echo $this->createWebUrl('display',array('gweid' => $_GPC['gweid']));?>">微预约</a> > <font class="fontpurple">预约活动详情</font></div>
</div>
<div style="height: 1px;background-color: #B2C1FC;margin: 20px 0px 10px 0;"></div>
<div class="main" style="height:1800px">
	<div class="form form-horizontal">
		<h4>预约活动信息</h4>
		<table class="tb">
			<tr>
				<th><label for="">预约标题</label></th>
				<td>
					<?php echo $activity['title'];?>
				</td>
			</tr>
			<tr>
				<th><label for="">预约活动说明</label></th>
				<td>
					<?php echo $activity['description'];?>
				</td>
			</tr>
			<tr>
				<th><label for="">预约活动提示</label></th>
				<td>
					<?php echo $activity['information'];?>
				</td>
			</tr>
			<tr>
				<th><label for="">图片介绍</label></th>
				<td>
					<img src="<?php echo $_W['attachurl'];?><?php if((empty($activity['thumb']))||(stristr($activity['thumb'],"http")!==false)){echo $activity['thumb'];}else{echo $upload['baseurl'].$activity['thumb'];}?>" style="height:150px;" />
				</td>
			</tr>
			<tr>
				<th><label for="">创建时间</label></th>
				<td>
					<?php echo date('Y-m-d H:i:s', $activity['createtime']);?>
				</td>
			</tr>
			<tr>
				<th><label for="">开始时间</label></th>
				<td>
					<?php echo date('Y-m-d H:i:s', $activity['starttime']);?>
				</td>
			</tr>
		</table>
		<h4>用户提交的信息</h4>
		<table class="tb">
			<?php if(!empty($row['openid'])){?>
			<tr>
				<th><label for="">用户</label></th>
				<td>
					<a><?php echo $row['openid'];?></a>
				</td>
			</tr>
			<?php }?>
			<tr>
				<th><label for="">用户提交时间</label></th>
				<td>
					<?php echo date('Y-m-d H:i:s', $row['createtime']);?>
				</td>
			</tr>
			<?php if(is_array($ds)) { foreach($ds as $fid => $ftitle) { ?>
			<tr>
				<th><label for=""><?php echo $ftitle['fid'];?></label></th>
				<td>
					<?php if($ftitle['type'] == 'image') { ?><a target="_blank" href="<?php echo $_W['attachurl'];?><?php echo $row['fields'][$fid];?>">点击查看<?php echo $ftitle['fid'];?></a><?php } else { ?><?php echo $row['fields'][$fid];?><?php } ?>
				</td>
			</tr>
			<?php } } ?>
			
		</table>
		<h4>预约拒绝说明</h4>
		<table class="tb">
			<tr>
				<td>
					<pre style="padding-left:0px;padding-top:0px;background-color:#fff;border:none;font-size:14px" class="mobile-content"><?php echo $row['reason']; ?></pre>
				</td>
			</tr>
			<tr>
				<td>
					<input type="submit" class="btn btn-primary span3" name="submit" onclick="history.go(-1)" value="返回" />
				</td>
			</tr>
		</table>
	</div>
</div>
<?php include template('common/footer', TEMPLATE_INCLUDEPATH);?>
