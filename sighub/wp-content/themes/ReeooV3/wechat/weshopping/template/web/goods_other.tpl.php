<?php defined('IN_IA') or exit('Access Denied');?>
<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/wechat/weshopping/css/daterangepicker_goods.css">
<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/we7/style/daterangepicker.css">
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/we7/script/daterangepicker.js"></script>
<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">商品类型</label>
	<div class="col-sm-9 col-xs-12">
		<label for="isshow3" class="radio-inline"><input type="radio" name="type" value="1" id="isshow3" <?php  if(empty($item['type']) || $item['type'] == 1) { ?>checked="true"<?php  } ?> onclick="$('#product').show()" /> 实体商品</label>&nbsp;&nbsp;&nbsp;<label for="isshow4" class="radio-inline"><input type="radio" name="type" value="2" id="isshow4"  <?php  if($item['type'] == 2) { ?>checked="true"<?php  } ?>  onclick="$('#product').hide()" /> 虚拟商品</label>
	</div>
</div>
<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">是否上架</label>
	<div class="col-sm-9 col-xs-12">
		<label for="isshow2" class="radio-inline"><input type="radio" name="status" onclick="goodstatus(this.value);" value="0" id="isshow2"  <?php  if($item['status'] == 0) { ?>checked="true"<?php  } ?> /> 是</label>
		&nbsp;&nbsp;&nbsp;
		<label for="isshow1" class="radio-inline"><input type="radio" name="status" onclick="goodstatus(this.value);" value="1" id="isshow1" <?php  if($item['status'] == 1) { ?>checked="true"<?php  } ?> /> 否</label>
		&nbsp;&nbsp;&nbsp;
		<label for="isshow3" class="radio-inline"><input type="radio" name="status" onclick="goodstatus(this.value);" value="2" id="isshow3"  <?php  if($item['status'] == 2) { ?>checked="true"<?php  } ?> /> 限时上架</label>
		<span class="help-block"></span>
	</div>
</div>
<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">限时上架时间</label>
		<input name="timestart" type="hidden" value="<?php echo $timestart?date('Y-m-d H:i', $timestart):date('Y-m-d H:i')?>" />
		<input name="timeend" type="hidden" value="<?php echo $timestart?date('Y-m-d H:i', $timeend):date('Y-m-d H:i')?>" />
		<button <?php  if($item['status'] != 2) { ?> disabled="disabled" <?php  } ?> style=" background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#ffffff), to(#e6e6e6));border: 1px solid #cccccc;margin-left:15px" class="btn" id="date-range" class="date" type="button"><span class="date-title"><?php echo $timestart?date('Y-m-d H:i', $timestart):date('Y-m-d H:i')?> 至 <?php echo  $timestart?date('Y-m-d H:i', $timeend):date('Y-m-d H:i') ?></span> <i class="icon-caret-down"></i></button>
</div>
<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">排序</label>
	<div class="col-sm-9 col-xs-12">
		<input type="text" id="displayorder" name="displayorder" class="form-control" value="<?php  echo $item['displayorder'];?>" />
	</div>
</div>
<script>
$(function() {
	$('#date-range').daterangepicker({
		format: 'YYYY-MM-DD HH:mm',
		timePicker:true,
		startDate: $(':hidden[name=timestart]').val(),
		endDate: $(':hidden[name=timeend]').val(),
		timePickerIncrement: 1,
		minuteStep: 1,
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
		$('#date-range .date-title').html(start.format('YYYY-MM-DD HH:mm') + ' 至 ' + end.format('YYYY-MM-DD HH:mm'));
		$(':hidden[name=timestart]').val(start.format('YYYY-MM-DD HH:mm'));
		$(':hidden[name=timeend]').val(end.format('YYYY-MM-DD HH:mm'));
	});	
});

function goodstatus(status){
	if(status=='2'){
		 $("#date-range").attr("disabled",false);
	}else{
		$("#date-range").attr("disabled",true);
	}
}
</script>