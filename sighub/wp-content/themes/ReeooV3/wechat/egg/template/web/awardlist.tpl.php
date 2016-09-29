<?php defined('IN_IA') or exit('Access Denied');?><?php  include template('common/header', TEMPLATE_INCLUDEPATH);?>
<style>
.sub-search input,.sub-search select{margin-bottom:0;}
</style>
<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/we7/style/daterangepicker.css">
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/we7/script/daterangepicker.js"></script>
<div class="main_auto">
	<div class="main-title">
		<div class="title-1">当前位置：<a href="<?php echo $this->createWebUrl('eggList',array());?>">砸蛋</a> > <font class="fontpurple">砸蛋中奖详情</font></div>
	</div>
	<div class="main" style="height:1000px">
		<div class="stat">
			<div class="stat-div">
				<div class="navbar navbar-static-top">
					<div class="navbar-inner">
						<span class="brand">中奖名单</span>
					</div>
				</div>
				<div class="sub-item">
					<h4 class="sub-title">搜索</h4>
					<form action="" method="get">
					<input type="hidden" name="act" value="module" />
					<!--url因为GET掉了module=scratchcard,所以这里写一遍-->
					<input type="hidden" name="module" value="egg" />
					<input type="hidden" name="do" value="awardlist" />
					<input type="hidden" name="name" value="egg" />
					<input type="hidden" name="id" value="<?php  echo $id;?>" />
					<table class="table sub-search">
						<tr>
							<th>中奖情况</th>
							<td>
								<select name="isstatus">
									<option value="4">全部</option>
									<option value="0" <?php if($_GET['isstatus'] == 0) { ?> selected<?php } ?>>未领奖</option>
									<option value="1" <?php if($_GET['isstatus'] != 0 && $_GET['isstatus'] != 4) { ?> selected<?php } ?>>已领奖</option>
								</select>
							</td>
						</tr>
						<tr>
							<th>个人信息</th>
							<td>
								<select name="profile">
									<option value="" selected="selected">请选择搜索用户资料</option>
									<option <?php  if($_GET['profile'] == 'realname') { ?>selected<?php  } ?> value="realname">姓名</option>
									<option <?php  if($_GET['profile'] == 'mobilenumber') { ?>selected<?php  } ?> value="mobilenumber">手机</option>
									<!--<option <?php  if($_GET['profile'] == 'qq') { ?>selected<?php  } ?> value="qq">QQ</option>-->
								</select>
								<input type="text" name="profilevalue" value="<?php  echo $_GET['profilevalue'];?>" class="form-control" />
							</td>
						</tr>
						<tr>
							<th>中奖时间</th>
							<td>
								<input name="start" type="hidden" value="<?php  echo date('Y-m-d', $starttime)?>" />
								<input name="end" type="hidden" value="<?php  echo date('Y-m-d', $endtime)?>" />
								<button class="btn" id="date-range" class="date" type="button"><span class="date-title"><?php  echo date('Y-m-d', $starttime)?> 至 <?php  echo date('Y-m-d', $endtime)?></span> <i class="icon-caret-down"></i></button>
							</td>
						</tr>
						<tr>
							<th>奖品信息</th>
							<td>
								<select name="award">
									<option value="" selected="selected">请选择搜索奖品资料</option>
									<option <?php  if($_GET['award'] == 'title') { ?>selected<?php  } ?> value="title">名称</option>
									<!--<option <?php  if($_GET['award'] == 'description') { ?>selected<?php  } ?> value="description">描述</option>-->
								</select>
								<input type="text" name="awardvalue" value="<?php  echo $_GET['awardvalue'];?>" class="form-control" />
							</td>
						</tr>
						<tr>
							<th></th>
							<td><input type="submit" name="" value="搜索" class="btn btn-primary" /></td>
						</tr>
					</table>
					</form>
				</div>
				<div class="sub-item" id="table-list">
					<h4 class="sub-title">详细数据</h4>
					<form action="" method="post" onsubmit="">
					<div class="sub-content">
						<table class="table table-striped table-bordered" width="800" border="0" align="center">	
							<tbody>
							   <tr>
									<td scope="col" width="110" align="center" style="font-weight:bold;text-align:center;">选择</td>
									<td scope="col" width="130" align="center" style="font-weight:bold;text-align:center;">姓名</td>
									<td scope="col" width="130" align="center" style="font-weight:bold;text-align:center;">手机</td>
									<td scope="col" width="130" align="center" style="font-weight:bold;text-align:center;">奖品</td>
									<td scope="col" width="130" align="center" style="font-weight:bold;text-align:center;">描述</td>
									<td scope="col" width="130" align="center" style="font-weight:bold;text-align:center;">获取时间</td>
									<td scope="col" width="130" align="center" style="font-weight:bold;text-align:center;">中奖情况</td>
								</tr>
							    
								<?php  if(is_array($list)) { foreach($list as $row) { ?>
								<tr>
									<td class="row-first" align="center" style="text-align:center"><input type="checkbox" name="select[]" value="<?php  echo $row['id'];?>" /></td>
									<td class="row-hover" align="center" style="text-align:center"><?php  echo $row['realname'];?></td>
									<td align="center" style="text-align:center"><?php  echo $row['mobilenumber'];?></td>
									<td align="center" style="text-align:center"><?php  echo $row['award'];?></td>
									<td align="center" style="text-align:center"><?php  echo $row['description'];?></td>
									<td align="center" style="font-size:12px; color:#666;">
										<?php  echo date('Y-m-d H:i:s', $row['createtime']);?>
									</td>
									<td align="center" style="text-align:center"><?php if($row['status'] == 0) { ?><a href="<?php echo $this->createWebUrl('awardlist', array('id' => $id, 'wid' => $row['id'], 'status' => 1))?>" class="text-error">确认领奖</a><?php } else if($row['status'] == 1){ ?><a href="<?php echo $this->createWebUrl('awardlist', array('id' => $id, 'wid' => $row['id'], 'status' => 0))?>" class="text-success">取消领奖</a><?php }else{ ?> 已领奖 <?php } ?></td>
								</tr>
								<?php  } } ?>
							</tbody>
						</table>
						<table class="table">
							<tr>
								<td style="width:40px;" class="row-first"><input type="checkbox" onclick="selectall(this, 'select');" /></td>
								<td>
									<input type="submit" name="delete" value="删除" class="btn btn-primary" />
									<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
								</td>
							</tr>
						</table>
					</div>
				</form>
				<?php  echo $pager;?>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
$(function() {
	//详细数据相关操作
	var tdIndex;
	$("#table-list thead").delegate("th", "mouseover", function(){
		if($(this).find("i").hasClass("")) {
			$("#table-list thead th").each(function() {
				if($(this).find("i").hasClass("icon-sort")) $(this).find("i").attr("class", "");
			});
			$("#table-list thead th").eq($(this).index()).find("i").addClass("icon-sort");
		}
	});
	$("#table-list thead th").click(function() {
		if($(this).find("i").length>0) {
			var a = $(this).find("i");
			if(a.hasClass("icon-sort") || a.hasClass("icon-caret-up")) { //递减排序
				/*
					数据处理代码位置
				*/
				$("#table-list thead th i").attr("class", "");
				a.addClass("icon-caret-down");
			} else if(a.hasClass("icon-caret-down")) { //递增排序
				/*
					数据处理代码位置
				*/
				$("#table-list thead th i").attr("class", "");
				a.addClass("icon-caret-up");
			}
			$("#table-list thead th,#table-list tbody:eq(0) td").removeClass("row-hover");
			$(this).addClass("row-hover");
			tdIndex = $(this).index();
			$("#table-list tbody:eq(0) tr").each(function() {
				$(this).find("td").eq(tdIndex).addClass("row-hover");
			});
		}
	});
	$('#date-range').daterangepicker({
		format: 'YYYY-MM-DD',
		startDate: $(':hidden[name=start]').val(),
		endDate: $(':hidden[name=end]').val(),
		locale: {
			applyLabel: '确定',
			cancelLabel: '取消',
			fromLabel: '从',
			toLabel: '至',
			weekLabel: '周',
			customRangeLabel: '日期范围',
			daysOfWeek: moment()._lang._weekdaysMin.slice(),
			monthNames: moment()._lang._monthsShort.slice(),
			firstDay: 0
		}
	}, function(start, end){
		$('#date-range .date-title').html(start.format('YYYY-MM-DD') + ' 至 ' + end.format('YYYY-MM-DD'));
		$(':hidden[name=start]').val(start.format('YYYY-MM-DD'));
		$(':hidden[name=end]').val(end.format('YYYY-MM-DD'));
	});
});
</script>
<?php  include template('common/footer', TEMPLATE_INCLUDEPATH);?>