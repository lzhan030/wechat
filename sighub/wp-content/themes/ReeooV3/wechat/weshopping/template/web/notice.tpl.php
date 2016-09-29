<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common', TEMPLATE_INCLUDEPATH)) : (include template('common', TEMPLATE_INCLUDEPATH));?>
<ul class="nav nav-tabs">
	<li <?php  if($operation == 'display') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('notice', array('op' => 'display'))?>">管理</a></li>
</ul>
<?php  if($operation == 'display') { ?>
<div class="main">
	<div class="panel panel-info">
		<div class="panel-heading">筛选</div>
		<div class="panel-body">
			<form action="./index.php" method="get" class="form-horizontal" role="form">
				<input type="hidden" name="c" value="site" />
				<input type="hidden" name="a" value="entry" />
				<input type="hidden" name="m" value="ewei_shopping" />
				<input type="hidden" name="do" value="notice" />
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">关键字</label>
					<div class="col-xs-12 col-sm-7 col-lg-9">
						<input class="form-control" name="keyword" id="" type="text" value="<?php  echo $_GPC['keyword'];?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">类型</label>
					<div class="col-xs-12 col-sm-7 col-lg-9">
						<select name="type" class='form-control'>
							<option value="0" selected>维权和告警</option>
							<option value="1" <?php  if($type==1) { ?> selected<?php  } ?>>维权</option>
							<option value="2" <?php  if($type==2) { ?> selected<?php  } ?>>告警</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">状态</label>
					<div class="col-xs-12 col-sm-7 col-lg-9">
						<select name="status"  class='form-control'>
							<option value="-1" selected="selected">所有</option>
							<option value="0" <?php  if($status==0) { ?> selected="selected"<?php  } ?>>未解决</option>
							<option value="1" <?php  if($status==1) { ?> selected="selected" <?php  } ?>>用户同意</option>
							<option value="2" <?php  if($status==2) { ?> selected="selected" <?php  } ?>>用户拒绝</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">日期范围</label>
					<div class="col-xs-12 col-sm-7 col-lg-9">
						<?php  echo tpl_form_field_daterange('date',array('start'=>date('Y-m-d', $starttime),'end'=>date('Y-m-d', $endtime)))?>
					</div>
					<div class="col-sm-3 col-lg-2"><button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button></div>
				</div>
			</form>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-body table-responsive">
			<table class="table table-hover">
				<thead class="navbar-inner">
				<tr>
					<th style="width:5%;">ID</th>
					<th style="width:10%;">投诉单号</th>
					<th style="width:10%;">订单号</th>
					<th style="width:5%;">类型</th>
					<th style="width:5%;">状态</th>
					<th style="width:7%;">投诉人</th>
					<th style="width:8%;">理由</th>
					<th style="width:12%;">期待解决方案</th>
					<th style="width:10%;">备注</th>
					<th style="width:8%;">投诉日期</th>
					<th style="text-align:right;width:10%;">操作</th>
				</tr>
				</thead>
				<tbody>
				<?php  if(is_array($list)) { foreach($list as $item) { ?>
				<tr>
					<td><?php  echo $item['id'];?></td>
					<td style="word-break: break-all;"><?php  echo $item['feedbackid'];?></td>
					<td style="word-break: break-all;"><?php  echo $item['transid'];?></td>
					<td><?php  echo $this->getFeedbackType($item['type']);?></td>
					<td><?php  echo $this->getFeedbackStatus($item['status']);?></td>
					<td><?php  echo $item['address']['realname'];?></td>
					<td><?php  echo $item['reason'];?></td>
					<td><?php  echo $item['solution'];?></td>
					<td><?php  echo $item['remark'];?></td>
					<td><?php  echo date('Y-m-d H:i:s', $item['createtime'])?></td>
					<td style="text-align:right;">
						<a href="<?php  echo $this->createWebUrl('order', array('id' => $item['order']['id'], 'op' => 'detail'))?>">订单详情</a>
						&nbsp;&nbsp;
						<a href="<?php  echo $this->createWebUrl('notice', array('id' => $item['id'], 'op' => 'delete'))?>" onclick="return confirm('此操作不可恢复，确认删除？');return false;">删除</a>
					</td>
				</tr>
				<?php  } } ?>
				</tbody>
			</table>
			<?php  echo $pager;?>
		</div>
	</div>
</div>

<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>
