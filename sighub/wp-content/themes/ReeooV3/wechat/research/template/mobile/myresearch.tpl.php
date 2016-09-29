<?php defined('IN_IA') or exit('Access Denied');?><?php include template('header', TEMPLATE_INCLUDEPATH);?>
<?php if($initCalendar) { ?>
<link type="text/css" rel="stylesheet" href="./resource/style/datetimepicker.css" />
<script type="text/javascript" src="./resource/script/datetimepicker.js"></script>
<script type="text/javascript">
	$(function(){
		$('.datepicker').each(function(){
			$(this).datetimepicker({
				format: "yyyy-mm-dd",
				minView: "2",
				pickerPosition: "top-right",
				autoclose: true
			});
		});
	});

</script>
<?php } ?>
<script type="text/javascript">

	function validate(){
		<?php if(is_array($ds)) { foreach($ds as $row) { ?>
		<?php if($row['essential']) { ?>
		<?php if(in_array($row['type'], array('number', 'text', 'calendar', 'email'))) { ?>
		if($.trim($(':text[name="field_<?php echo $row['refid'];?>"]').val()) == '') {
			alert('<?php echo $row['title'];?> 必须填写.');
			return false;
		}
		<?php } ?>
		<?php if(in_array($row['type'], array('image'))) { ?>
		if($.trim($(':file[name="field_<?php echo $row['refid'];?>"]').val()) == '') {
			alert('<?php echo $row['title'];?> 必须上传.');
			return false;
		}
		<?php } ?>
		<?php if(in_array($row['type'], array('textarea'))) { ?>
		if($.trim($(':textarea[name="field_<?php echo $row['refid'];?>"]').val()) == '') {
			alert('<?php echo $row['title'];?> 必须填写.');
			return false;
		}
		<?php } ?>
		<?php if(in_array($row['type'], array('checkbox'))) { ?>
		if($(':checkbox[name="field_<?php echo $row['refid'];?>[]"]:checked').length == 0) {
			alert('<?php echo $row['title'];?> 必须选择.');
			return false;
		}
		<?php } ?>
		<?php if(in_array($row['type'], array('number'))) { ?>
		var num = parseFloat($.trim($(':text[name="field_<?php echo $row['refid'];?>"]').val()));
		if(isNaN(num)) {
			alert('<?php echo $row['title'];?> 必须输入数字.');
			return false;
		}
		<?php } ?>
		<?php if(in_array($row['type'], array('email'))) { ?>
		var mail = $.trim($(':text[name="field_<?php echo $row['refid'];?>"]').val());
		if(!(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/i).test(mail)) {
			alert('<?php echo $row['title'];?> 必须输入邮箱地址.');
			return false;
		}
		<?php } ?>
		<?php } ?>
		<?php } } ?>
		return true;
	}
</script>
<style>
body{background:#ECECEC;}
.research-thumb{width:100%;}
</style>
<div class="research">
	<?php $upload =wp_upload_dir();
	if(!empty($activity['thumb'])) {
		if(stristr($activity['thumb'],"http")!==false){
			$actithumb=$activity['thumb'];
		}else{
			$actithumb=$upload['baseurl'].$activity['thumb'];
		}
	?><img class="research-thumb" src="<?php echo $_W['attachurl'];?><?php echo $actithumb;?>"><?php } ?>
	<div class="alert alert-error mobile-div ">已成功提交以下预约:</div>
	<div class="mobile-div img-rounded">
		<div class="mobile-hd"><?php echo $activity['title'];?></div>
		<div class="mobile-content">
		<?php echo $activity['description'];?>
		</div>
	</div>
	<div class="mobile-div img-rounded">
		<div class="mobile-hd">预约有效期</div>
		<div class="mobile-content">
		<?php echo date('Y年n月j日',strtotime($activity['startdate']))?> 至 <?php echo date('Y年n月j日',strtotime($activity['enddate']))?>
		</div>
	</div>
	<?php if($rstatus=='0'){  ?>
	<div class="mobile-div img-rounded">
		<div class="mobile-hd">预约拒绝原因</div>
		<div class="mobile-content">
			<pre style="padding-left:0px;padding-top:0px;background-color:#fff;border:none;font:14px/1.5 'Microsoft Yahei','Simsun'" class="mobile-content"><?php echo $rreason; ?></pre>
		</div>
	</div>
	<?php } ?>
	<form>
	<div class="mobile-div img-rounded">
		<div class="mobile-hd">您的预约详情</div>
		<div class="mobile-content">
			<table class="form-table">
				<?php if(is_array($ds)) { foreach($ds as $row) { ?>
				<tr>
					<th><label for=""><?php echo $row['title'];?><?php if($row['essential']) { ?> <span title="必填项" class="text-error">*</span><?php } ?></label></th>
					<td>
						<?php if($row['type'] == 'number') { ?>
						<input type="text" name="field_<?php echo $row['refid'];?>" value="<?php echo $userData[$row['refid']];?>" readonly="readonly"/>
						<?php } ?>
						<?php if($row['type'] == 'text') { ?>
						<input type="text" name="field_<?php echo $row['refid'];?>" value="<?php echo $userData[$row['refid']];?>" readonly="readonly"/>
						<?php } ?>
						<?php if($row['type'] == 'textarea') { ?>
						<textarea name="field_<?php echo $row['refid'];?>" rows="3" readonly="readonly"><?php echo $userData[$row['refid']];?></textarea>
						<?php } ?>
						<?php if($row['type'] == 'radio') { ?>
						<select name="field_<?php echo $row['refid'];?>" disabled>
						<?php if(is_array($row['options'])) { foreach($row['options'] as $v) { ?>
							<option value="<?php echo $v;?>" <?php if($v == $userData[$row['refid']]) { ?> selected="selected"<?php } ?>><?php echo $v;?></option>
						<?php } } ?>
						</select>
						<?php } ?>
						<?php if($row['type'] == 'checkbox') { ?>
						<?php if(is_array($row['options'])) { 
							$userData[$row['refid']] = explode(';', $userData[$row['refid']]);
							foreach($row['options'] as $v) { ?>
						<label class="checkbox inline btn btn-small disabled <?php if( in_array($v ,$userData[$row['refid']]) ) { ?>btn-inverse active<?php } ?>">
							<input type="checkbox" name="field_<?php echo $row['refid'];?>[]" value="<?php echo $v;?>" disabled/><?php echo $v;?>
						</label>
						<?php } } ?>
						<?php } ?>
						<?php if($row['type'] == 'select') { ?>
						<select name="field_<?php echo $row['refid'];?>" readonly="readonly" disabled>
						<?php if(is_array($row['options'])) { foreach($row['options'] as $v) {  ?>
							<option value="<?php echo $v;?>" <?php if($v == $userData[$row['refid']]) { ?> selected="selected"<?php } ?> /><?php echo $v;?></option>
						<?php } } ?>
						</select>
						<?php } ?>
						<?php if(!empty($row['description'])) { ?><span class="help-block"><?php echo urldecode($row['description']);?></span><?php } ?>
					</td>
				</tr>
				<?php } } ?>
			</table>
		<div class="mobile-submit">
		<button class="btn btn-large btn-success disabled" style="width:100%;" disabled>提交成功</button>
		</div>		
		</div>
	</div>

	</form>
	<?php if(!empty($activity['postscript'])) { ?>
	<div class="mobile-div img-rounded">
		<div class="mobile-content">
		<?php echo $activity['postscript'];?>
		</div>
	</div>
	<?php } ?>
</div>
<?php 
$title = $activity['title'];
$content = stripslashes($activity['description']);
if(empty($content))
	$content = "点击即可参与到 {$activity['title']}！";
$link = $this->createMobileUrl('research',array('id'=> $reid,'gweid'=>$_GET['gweid']));
include $this->template('footer');?>
