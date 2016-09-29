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
	.alert-link{color: #843534; font-weight: 700;}
	a{text-decoration: none;}
	select{-webkit-appearance: menulist;}
	input[type="checkbox"]{opacity: 1;margin-left: 0px;}
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
	<?php if(!$this -> has_member_module(true) && empty($_W['fans']['from_user'])){?>
		<div role="alert" class="mobile-div alert alert-danger" style="   background-color: #eed3d7;">当前无法识别您的微信账号，提交后您将无法查看预约记录。如需查看预约记录，请返回微信发送关键词重试。</div>
	<?php }?>
	<?php if($this -> has_member_module(true) && empty($_W['fans']['mid'])){?>
		<div role="alert" class="mobile-div alert alert-danger" style="   background-color: #eed3d7;">您当前还未登录，以后可能无法查看预约记录，请 <a class="alert-link" href="<?php echo home_url();?>/wp-content/themes/ReeooV3/wesite/common/vip_login.php?gweid=<?php echo $_W['gweidv'];?>&redirect_url=<?php echo urlencode('http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"])?>">登录</a>（<a class="alert-link" href="<?php echo home_url();?>/wp-content/themes/ReeooV3/wesite/common/vip_register.php?gweid=<?php echo $_W['gweidv'];?>&redirect_url=<?php echo urlencode('http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"])?>">注册</a>）后再提交预约。</div>
	<?php }?>

	<div class="mobile-div img-rounded">
		<div class="mobile-hd"><?php echo $activity['title'];?></div>
		<div class="mobile-content">
		<?php echo stripslashes($activity['description']);?>
		</div>
	</div>
	<div class="mobile-div img-rounded">
		<div class="mobile-hd">预约有效期</div>
		<div class="mobile-content">
		<?php echo date('Y年n月j日',strtotime($activity['startdate']))?> 至 <?php echo date('Y年n月j日',strtotime($activity['enddate']))?>
		</div>
	</div>
	<form action="" id="research" method="post" enctype="multipart/form-data" onSubmit="return researchSubmit();">
	<div class="mobile-div img-rounded">
		<div class="mobile-hd">请认真填写表单</div>
		<div class="mobile-content">
			<table class="form-table">
				<?php if(is_array($ds)) { foreach($ds as $row) { ?>
				<tr>
					<th><label for=""><?php echo $row['title'];?><?php if($row['essential']) { ?> <span title="必填项" class="text-error">*</span><?php } ?></label></th>
					<td>
						<?php if($row['type'] == 'number') { ?>
						<input type="text" class ="form-control" name="field_<?php echo $row['refid'];?>" value="<?php echo $row['default'];?>" />
						<?php } ?>
						<?php if($row['type'] == 'text') { ?>
						<input class ="form-control" type="text" name="field_<?php echo $row['refid'];?>" value="<?php echo $row['default'];?>" />
						<?php } ?>
						<?php if($row['type'] == 'textarea') { ?>
						<textarea class ="form-control" name="field_<?php echo $row['refid'];?>" rows="3"><?php echo $row['default'];?></textarea>
						<?php } ?>
						<?php if($row['type'] == 'radio') { ?>
						<select class ="form-control" name="field_<?php echo $row['refid'];?>">
						<?php if(is_array($row['options'])) { foreach($row['options'] as $v) { ?>
							<option value="<?php echo $v;?>" <?php if($v == $row['default']) { ?> selected="selected"<?php } ?>><?php echo $v;?></option>
						<?php } } ?>
						</select>
						<?php } ?>
						<?php if($row['type'] == 'checkbox') { ?>
						<?php if(is_array($row['options'])) { foreach($row['options'] as $v) { ?>
						<label class="checkbox inline btn btn-small">
							<input style="opacity: 1;margin-left: 0px;" type="checkbox" name="field_<?php echo $row['refid'];?>[]" value="<?php echo $v;?>" <?php if($v == $row['default']) { ?> checked="checked"<?php } ?>/><?php echo $v;?>
						</label>
						<?php } } ?>
						<?php } ?>
						<?php if($row['type'] == 'select') { ?>
						<select class ="form-control" name="field_<?php echo $row['refid'];?>">
						<?php if(is_array($row['options'])) { foreach($row['options'] as $v) { ?>
							<option value="<?php echo $v;?>" <?php if($v == $row['default']) { ?> selected="selected"<?php } ?> /><?php echo $v;?></option>
						<?php } } ?>
						</select>
						<?php } ?>
						<?php if($row['type'] == 'calendar') { ?>
						<input type="text" class="datepicker" name="field_<?php echo $row['refid'];?>" value="<?php echo $row['default'];?>" readonly="readonly" />
						<?php } ?>
						<?php if($row['type'] == 'email') { ?>
						<input type="text" class ="form-control" name="field_<?php echo $row['refid'];?>" value="<?php if($row['default']) { ?><?php echo $row['default'];?><?php } else { ?>@<?php } ?>" />
						<?php } ?>
						<?php if($row['type'] == 'image') { ?>
						<div class="file">
							<input type="file" name="field_<?php echo $row['refid'];?>">
							<button class="btn" type="button"><i class="icon-upload-alt"></i> 上传图片</button>
						</div>
						<?php } ?>
						<?php if($row['type'] == 'range') { ?>
						<select name="field_<?php echo $row['refid'];?>">
							<option value="0:00-1:00" >0:00-1:00</option>
							<option value="1:00-2:00">1:00-2:00</option>
							<option value="2:00-3:00">2:00-3:00</option>
							<option value="3:00-4:00">3:00-4:00</option>
							<option value="4:00-5:00">4:00-5:00</option>
							<option value="5:00-6:00">5:00-6:00</option>
							<option value="6:00-7:00">6:00-7:00</option>
							<option value="7:00-8:00">7:00-8:00</option>
							<option value="8:00-9:00">8:00-9:00</option>
							<option value="9:00-10:00">9:00-10:00</option>
							<option value="10:00-11:00">10:00-11:00</option>
							<option value="11:00-12:00">11:00-12:00</option>
							<option value="12:00-13:00">12:00-13:00</option>
							<option value="13:00-14:00">13:00-14:00</option>
							<option value="14:00-15:00">14:00-15:00</option>
							<option value="15:00-16:00">15:00-16:00</option>
							<option value="16:00-17:00">16:00-17:00</option>
							<option value="17:00-18:00">17:00-18:00</option>
							<option value="18:00-19:00">18:00-19:00</option>
							<option value="19:00-20:00">19:00-20:00</option>
							<option value="20:00-21:00">20:00-21:00</option>
							<option value="21:00-22:00">21:00-22:00</option>
							<option value="22:00-23:00">22:00-23:00</option>
							<option value="23:00-24:00">23:00-24:00</option>
						</select>
						<?php } ?>
						<?php if(!empty($row['description'])) { ?><span class="help-block"><?php echo urldecode($row['description']);?></span><?php } ?>
					</td>
				</tr>
				<?php } } ?>
			</table>
		<div class="mobile-submit">
		<input type="hidden" name="token" value="<?php echo $_W['token'];?>" />
		<input type="submit" class="btn btn-large btn-success" value="提交" style="width:100%;">
		</div>		
		</div>
	</div>

	</form>
	<?php if(!empty($activity['postscript'])) { ?>
	<div class="mobile-div img-rounded">
		<div class="mobile-content">
		<?php echo stripslashes($activity['postscript']);?>
		</div>
	</div>
	<?php } ?>
</div>
<script type="text/javascript">
	$(function(){
		isSubmitting = false;
		Messenger.options = {
			extraClasses: 'messenger-fixed messenger-on-top',
			theme: 'future'
		}
		$(".form-table").delegate(".checkbox input[type='checkbox']", "click", function(){
			$(this).parent().toggleClass("btn-primary");
		});
	});
		function researchSubmit(){
			if(validate())
				jQuery.post(
					window.location.href,
					$('#research').serialize(),
					function(data){
						if(data.type == 'success'){
							Messenger().post({
							  message: data.message,
							  type: 'success',
							  showCloseButton: true,
							  hideAfter: 5
							});
							setTimeout(function () {
										location.href=data.redirect;
										}, 3000);
						}else{
							$('.message').css('margin','auto');
							if(typeof data.message == "undefined")
								errormsg = "网络异常，请重试";
							else{
								errormsg = data.message;
							}
							  Messenger().post({
							  message: errormsg,
							  type: 'error',
							  showCloseButton: true,
							  hideAfter: 3
							  });
							}
						$('.message').css('margin','auto');
					},
					'json'
				).fail(function(){
					 Messenger().post({
						  message: "网络异常，请重试",
						  type: 'error',
						  showCloseButton: true,
						  hideAfter: 3
						  });
				});
			return false;
		}

</script>
<?php 
$title = $activity['title'];
$content = stripslashes($activity['description']);
if(empty($content))
	$content = "点击即可参与到 {$activity['title']}！";
include $this->template('footer');?>
