<?php defined('IN_IA') or exit('Access Denied');?><?php include template('common/header', TEMPLATE_INCLUDEPATH);?>
<style>
.field label{float:left;margin:0 !important; width:140px;}
</style>
<div class="main_auto"style="height:3050px">
	<div class="main-title">
		<div class="title-1">当前位置：首页&gt;<a href="<?php echo $this->createWebUrl('display',array('gweid' => $_GPC['gweid']));?>">微预约</a> > <font class="fontpurple">查看微预约结果</font></div>
	</div>
	<div class="main" style="height:3000px">
		<div class="stat">
			<div class="stat-div">
				<div class="navbar navbar-static-top">
					<div class="sub-item">
						<h4 class="sub-title">当前预约活动详情</h4>
						<table class="table sub-search">
						<tbody>
							<tr>
								<th style="width:25%;">
									预约名称
								</th>
								<td class="field">
									<?php echo $activity['title']; ?>
								</td>
							</tr>
							<tr>
								<th>预约简介</th>
								<td>
									<?php echo stripslashes($activity['description']); ?>
								</td>
							</tr>
							<tr>
								<th>预约开始/结束时间</th>
								<td>
									<?php echo $activity['startdate']; ?> 至 <?php echo $activity['enddate']; ?>
								</td>
							</tr>
							<tr>
								<th><a href="<?php echo $this -> createMobileUrl('export', array('id'=>$_GPC['id'],'gweid'=>$_W['gweid'])); ?>" name="" class="btn btn-primary">导出</a></th>
								<td></td>
							</tr>
						</tbody>
						</table>
					</div>
				</div>
				<div class="sub-item" id="table-list">
					<h4 class="sub-title">详细数据</h4>
					<form action="" method="post" onsubmit="">
					<div class="sub-content">
						<table class="table table-striped table-bordered" width="800" border="0" align="center" style="table-layout:fixed">
							<tbody class="objbody">
								<tr>
									<td scope="col" width="110" align="center" style="font-weight:bold;text-align:center;">用户</td>
									<?php if(is_array($select)) { foreach($select as $fid) { ?>
									<td scope="col" width="110" align="center" style="font-weight:bold;text-align:center;"><?php echo $ds[$fid];?></td>
									<?php } } ?>
									<td scope="col" width="100" align="center" style="font-weight:bold;text-align:center;">提交时间</td>
									<td scope="col" width="100" align="center" style="font-weight:bold;text-align:center;">审核预约</td>
									<td scope="col" width="100" align="center" style="font-weight:bold;text-align:center;">拒绝说明</td>
									<td scope="col" width="100" align="center" style="font-weight:bold;min-width:40px;text-align:center;">操作</td>
								</tr>
								<?php
									if(is_array($list)){
										foreach($list as $row){
									 ?>
								<tr>
									<td style="text-align:center;" align="center"><a href="javascript:;" title="<?php echo $row['from_user'];?>"><?php echo empty($row['openid'])?'N/A':$row['openid'];?></a></td>
									<?php if(is_array($select)) { foreach($select as $fid) { ?>
									<td align="center" style="text-align:center"><?php echo $row['fields'][$fid];?><i></i></td>
									<?php } } ?>
									<td align="center" style="font-size:12px; color:#666;">
									<?php echo date('Y-m-d H:i:s', $row['createtime']);?>
									</td>
									<td align="center" style="text-align:center">
										<div class="make-switch switch-small" id="raw" data-on-label="<?php echo $row['status'] === NULL?'处理中':'同意'?>" data-off-label="<?php echo $row['status'] === NULL?'未审核':'拒绝'?>" <?php echo $row['status'] === NULL?'onclick="active_status($(this));"':''?> data-rerid="<?php echo $row['rerid']?>"  data-reason="<?php echo $row['reason']?>">
										<input type="checkbox" <?php if($row['status'] === NULL) echo 'disabled';  ?> <?php if($row['status'] === '1') echo 'checked';  ?>/>
									</div>
									</td>
									<td class="reason" align="center" style="font-size:14px; color:#666;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;width:129px;height:28px;word-wrap:normal;text-align:center;word-break:break-all;" title=<?php echo $row['reason'];?>>
									<?php 
									echo $row['reason'];
									?>
									</td>
									<td align="center" style="text-align:center;"><a href="<?php echo $this->createMobileUrl('detail', array('id' => $row['rerid'],'gweid' => $_W['gweid']))?>">详情</a></td>
								</tr>
								
								<?php }}?>
							</tbody>
						</table>
						<table class="table">
							<tr>
								<td class="row-first"></td>
								<td>
									<input type="hidden" name="token" value="<?php echo $_W['token'];?>" />
								</td>
							</tr>
						</table>
					</div>
					</form>
					<?php echo $pager;?>
				</div>
			</div>
		</div>
	</div>
	<!-- Modal -->
	<div id="reason" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="bottom: inherit; left: 50%;right: inherit;">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">拒绝说明</h3>
	  </div>
	  <div class="modal-body">
		<div id="status_alert" class="alert alert-info" style="margin-bottom:5px;display:none;">
		</div>
		<div id="warning_status_alert" class="alert alert-warning" style="margin-bottom:5px;display:none;">
		</div>
		<form id='reason_form'>
			<div>
				<textarea rows="13" cols="13" class="form-control" id="reasoninput" name="reasoninput" autofocus="autofocus" placeholder="请输入拒绝原因" style="width: 455px; height: 135px; margin-left: 32px;margin-bottom: -16px;"></textarea>
				<input id="reridvalue" name="reridvalue" type="hidden"/>
				<input id="objvalue" name="objvalue" type="hidden"/>
			</div>
		</form>
	  </div>
	  <div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
		<button id="category_save" class="btn btn-primary" type="button" onclick="reason_submit()" >保存</button>
	  </div>
	  <div id="category_default" style="display:none;"></div>
	</div><!-- /.modal -->
</div>
<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/we7/style/daterangepicker.css" />
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/we7/script/daterangepicker.js"></script>
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
    function active_status(obj){
        obj.removeAttr('onclick');
        obj.bootstrapSwitch('setState', true);
        obj.bootstrapSwitch('setActive', true);
        
        
    }
	
$('.make-switch').on('switch-change', function (e, data) {
    
    var obj = $(this);
    //bug  here
	status = ( $(this).bootstrapSwitch('status') == true ? 1 : 0);
    $(this).bootstrapSwitch('setOnLabel', '处理中'); 
    $(this).bootstrapSwitch('setOffLabel', '处理中');
	var rid=$(this).data('rerid');
	var reason=$(this).data('reason');
    jQuery.post(
        "<?php echo $this -> createMobileUrl("applyStatus",array('gweid' => $_W['gweid'])); ?>",
        {id : $(this).data('rerid'),status : status },
        function(data, textStatus, jqXHR){
            obj.bootstrapSwitch('setOnLabel', '同意'); 
            obj.bootstrapSwitch('setOffLabel', '拒绝');
			if(status==0){//拒绝理由
				$('#status_alert').css('display','none');
				$('#warning_status_alert').css('display','none');
				//$('#reasoninput').val(reason);
				$('#reasoninput').val('');
				$('#reridvalue').val(rid);
				$('#objvalue').val(obj.parent().parent().index());
				$('#reason').modal();
			}else{
				obj.parent().parent().find('.reason').html('');
				obj.parent().parent().find('.reason').attr('title','');
			}
        },
        "json"
    ); 
});


function reason_submit(){
	if($('#reasoninput').val() == ""){
			$('#warning_status_alert').css('display','block');
			$("#warning_status_alert").html("理由不能为空");
	}else{
		$.ajax({
			url:"<?php echo $this -> createMobileUrl("rejectReason",array('gweid' => $_W['gweid'])); ?>", 
			type: "POST",
			data:{id : $('#reridvalue').val(),reason : $('#reasoninput').val() },
			success: function(data){
				if (data.status == 'success'){
					$('#status_alert').css('display','block');
					$("#status_alert").html("保存成功");
					setTimeout(function(){
						$('#reason').modal('hide');
						var obj=$('#objvalue').val();
						$('.objbody>tr').eq(obj).find('.reason').html($('#reasoninput').val());
						$('.objbody>tr').eq(obj).find('.reason').attr('title',$('#reasoninput').val());
						//location.reload();
					},1500);
					
				}else{
					alert("出现错误,请重试");
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
<?php include template('common/footer', TEMPLATE_INCLUDEPATH);?>
