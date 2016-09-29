<?php
$tmp_path = explode ( 'ReeooV3', __FILE__ );
$template_path=$tmp_path[0];
require_once $template_path.'ReeooV3/wechat/common/session.php';

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
global  $current_user;
if( !isset($current_user->user_login)|| empty($current_user->user_login)){
		wp_redirect(wp_login_url());
}	
include '../common/wechat_dbaccessor.php';
include 'editreply_permission_check.php';
$reply_list = $wpdb -> get_results("SELECT * FROM {$wpdb -> prefix}wechat_editablereply WHERE `GWEID` = '{$_SESSION['GWEID']}' ORDER BY `edit_reply_id` ASC");
 ?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
	<meta name="Author" content="SeekEver">
	<meta name="Keywords" content="">
	<meta name="Description" content="">
	<meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery-1.10.2.min.js" type="text/javascript"></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.Jcrop.js" type="text/javascript"></script>
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/reset.css" type="text/css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/jquery.Jcrop.css" type="text/css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/test.css" type="text/css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/table1.css" />
	<script src="<?php bloginfo('template_directory'); ?>/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">

	<style>
	td{margin-bottom:5px;}
	</style>
	</head>
	<body>
		
		<div class="main_auto">
			<div class="main-title">
				<div class="title-1">当前位置：可编程回复></div>
			</div>
			<div class="row container_box" style="width:800px; min-height: 650px; margin-right:auto; padding:26px 0 0 17px;overflow-x: hidden;">
				<div class="cell_layout" style="border: 1px solid #d9dadc;background-color: #fff;">
					<div class="sidediv" style="float: left;">
						<div style="width:190px; height:50px; margin-left:30px;margin-top:32px;">
							 <input type="button" class="btn btn-primary" style="margin-left:0px;width:100%;" name="add_reply" id="add_reply" value="添加回复">	
						</div>
						<div style="width:190px; height:410px; margin-left:30px;border: 1px solid #d9dadc;">
							<span>回复列表：</span>
							<ul id="reply_list">
								<?php if(is_array($reply_list)){
										foreach($reply_list as $reply){?>
										<li data-reply-id="<?php echo $reply -> edit_reply_id ?>"><?php echo $reply -> edit_reply_name ?></li>
								<?php }} ?>
							</ul>

						</div>
					</div>
					<form id="reply_form">
						<input id="reply_id" type="hidden" name="edit_reply_id" value="0">
						<table class="tb" style="background-color: #FFF;min-height: 650px;margin-left: 260px; margin-top:20px;">
							<tr>
								<th style="width: 100px;"><label for="">回复名称</label></th>
								<td style="width: 80%;">
									<input type="text" class="span4 form-control input-sm" placeholder="" id="edit_reply_name" name="edit_reply_name" value="" />
								</td>
							</tr>
							<tr>
								<th><label for="">回复类型</label></th>
								<td>
									<input type="checkbox" name="edit_reply_type[]" value="autorep" ></input>
									<span>首次关注回复</span>
									<input type="checkbox" name="edit_reply_type[]" value="nokeyword" ></input>
									<span>无匹配回复</span>
									<div style="margin-top:10px;">
										<input type="checkbox" name="edit_reply_type[]" value="keyword" ></input>
										<span>关键词回复</span>
										<input class="form-control input-sm" type="text" id="edit_reply_keyword" name="edit_reply_keyword"  style="width: 77%; display: inline;" ></input>
									</div>
								</td>
							</tr>
							<tr>
								<th><label for="">输入设置</label></th>
								<td>
									<div style="display:inline;" id="condition_list">
										<div style=" margin-top: 20px;">
											<select name="edit_reply_condition_key[]" class="form-control input-sm" style="width:88%; display:inline;" size="1" type="text;" id="xtype" value="5" maxlength="20">
												<option value="openid" selected="selected">$X-openid</option>
												<option value="subscribe_id">$Y-关注顺序</option> 
												<option value="timestamp">$Z-timestamp</option>
											</select>
											<img id="delete_condition" src="<?php bloginfo('template_directory'); ?>/images/delete.gif" width="6%" style="margin-left:15px;" onclick="$(this).parent().remove();">
										</div>
									</div>
									<img id="add_condition" src="<?php bloginfo('template_directory'); ?>/images/add.gif" width="6%"><span> 添加条件</span>
								</td>
							</tr>
							<tr>
								<th><label for="">代码类型</label></th>
								<td>
									<label class="radio-inline">
									  <input type="radio" name="edit_reply_activity" id="edit_reply_activity" value="0" checked="checked"> PHP代码
									</label>
									<label class="radio-inline">
									  <input type="radio" name="edit_reply_activity" id="edit_reply_activity" value="1"> 算术表达式
									</label>
								</td>
							</tr>
							<tr>
								<th><label for="">代码设置</label></th>
								<td>
									<textarea id="edit_reply_code" name="edit_reply_code" class="form-control" style="height:180px;margin-bottom: 5px;"> </textarea>
								</td>
							</tr>

							<tr>
								<th><label for="">开头文本</label></th>
								<td>
									<input type="text" class="form-control input-sm" id="edit_reply_textstart" name="edit_reply_textstart" value="" style="margin-bottom: 5px;"/>
								</td>
							</tr>
							<tr>
								<th><label for="">结束文本</label></th>
								<td>
									<input type="text" class="form-control input-sm" id="edit_reply_textend" name="edit_reply_textend" value="" style="margin-bottom: 5px;"/>
								</td>
							</tr>
						</table>
						<div style="width:92%; height:50px;margin-top:15px;margin-left:360px;margin-bottom:20px" id="button_list">
							 <input type="button" class="btn btn-default" onclick="" name="delete_reply" id="delete_reply" value="删除" style="display:none; width:100px">
							 <input type="button" class="btn btn-warning" onclick="" name="scriptvalication" id="scriptvalication" value="代码验证" style="margin-left:20px; width:100px"> 
							 <input type="submit" class="btn btn-primary submit" onclick="" name="sub" id="sub" value="提交" style="margin-left:20px; width:100px">
						</div>
					</form>
				</div>
			</div>
				
		</div>
		<script>
	    		
		$('#scriptvalication').click(function(){
			jQuery.post(
				"<?php bloginfo('template_directory'); ?>/wechat/editrep/editreply_test.php",
				{'code':$('#edit_reply_code').val()},
				function(data){
					console.info(data);
					if(data.status == 'error')
						alert(data.message);
					if(data.status == 'success')
						alert("代码验证通过");
				},
				'json'
			);
			
		});
		$('#delete_reply').click(function(){
			if($('#reply_id').val()>0 && confirm("确定要删除可编程回复\""+$('li[data-reply-id="'+$('#reply_id').val()+'"]').text()+"\"吗？")){
				jQuery.post(
					"<?php bloginfo('template_directory'); ?>/wechat/editrep/editreply_ajax.php?action=delete",
					{reply_id:$('#reply_id').val()},
					function(data){
						console.info(data);
						console.info(data.status);
						console.info(data.status == "success");
						if(data.status == "error")
							alert(data.message);
						if(data.status == "success"){
							alert("删除成功");
							$('li[data-reply-id="'+data.id+'"]').remove();
							flush_form();
							$('#condition_list').append('<div style=" margin-top: 20px;">'+
															'<select name="edit_reply_condition_key[]" class="form-control input-sm" style="width:88%; display:inline;" size="1" type="text;" id="xtype" value="5" maxlength="20">'+
																'<option value="openid" selected="selected">$X-openid</option>'+
																'<option value="subscribe_id">$Y-关注顺序</option>'+
																'<option value="timestamp">$Z-timestamp</option>'+
															'</select>'+
															'<img id="delete_condition" src="<?php bloginfo('template_directory'); ?>/images/delete.gif" width="6%" style="margin-left:5%;" onclick="$(this).parent().remove();">'+
														'</div>');
						}
					},
					'json'
				);
			}	
			
		});
		$('#reply_form').submit(function(){
			if($('#edit_reply_name').val() == ""){
				alert("请输入回复名称！");
				return false;
			}
			jQuery.post(
				"<?php bloginfo('template_directory'); ?>/wechat/editrep/editreply_test.php",
				{'code':$('#edit_reply_code').val()},
				function(data){
					console.info(data);
					if(data.status == 'error')
						alert(data.message);
					if(data.status == 'success'){
						if($('#reply_id').val() === "0")
							jQuery.post(
								"<?php bloginfo('template_directory'); ?>/wechat/editrep/editreply_ajax.php?action=add",
								$('#reply_form').serialize(),
								function(data){
									console.info(data);
									console.info(data.status);
									console.info(data.status == "success");
									if(data.status == "error")
										alert(data.message);
									if(data.status == "success"){
										alert("提交成功");
										$('#reply_list').append("<li data-reply-id=\""+ data.id +"\">"+data.name+"</li>");
										$('#add_reply').click();
									}
								},
								'json'
							);
						if($('#reply_id').val() > 0)
							jQuery.post(
								"<?php bloginfo('template_directory'); ?>/wechat/editrep/editreply_ajax.php?action=update&reply_id="+$('#reply_id').val(),
								$('#reply_form').serialize(),
								function(data){
									console.info(data);
									console.info(data.status);
									console.info(data.status == "success");
									if(data.status == "error")
										alert(data.message);
									if(data.status == "success"){
										alert("更新成功");
										$('li[data-reply-id="'+data.id+'"]').text(data.name);
									}
								},
								'json'
							);
					}
				},
				'json'
			);
			
			
			return false;
			
		});
		$('#add_condition').click(function(){
			$('#condition_list').append('<div style=" margin-top: 20px;">'+
											'<select name="edit_reply_condition_key[]" class="form-control input-sm" style="width:88%; display:inline;" size="1" type="text;" id="xtype" value="5" maxlength="20">'+
												'<option value="openid" selected="selected">$X-openid</option>'+
												'<option value="subscribe_id">$Y-关注顺序</option>  '+
												'<option value="timestamp">$Z-timestamp</option> '+
											'</select>'+
											'<img id="delete_condition" src="<?php bloginfo('template_directory'); ?>/images/delete.gif" width="6%" style="margin-left:5%;" onclick="$(this).parent().remove();">'+
										'</div>');
		});
		$('#reply_list').on('click','li',function(){
			reply_li_element = $(this);
			jQuery.post(
				"<?php bloginfo('template_directory'); ?>/wechat/editrep/editreply_ajax.php?action=get",
				{reply_id:$(this).data('reply-id')},
				function(data){
					flush_form();
					console.info(data);
					$('#reply_id').val(data.edit_reply_id);
					$('#edit_reply_name').val(data.edit_reply_name);
					reply_li_element.css("background","#CED2C9");
					if ($.inArray('openid',data.edit_reply_condition)!=-1)
						$('#condition_list').append('<div style=" margin-top: 20px;">'+
								'<select name="edit_reply_condition_key[]" class="form-control input-sm" style="width:88%; display:inline;" size="1" type="text;" id="xtype" value="5" maxlength="20">'+
									'<option value="openid" selected="selected">$X-openid</option>'+
									'<option value="subscribe_id">$Y-关注顺序</option>  '+
									'<option value="timestamp">$Z-timestamp</option>'+
								'</select>'+
								'<img id="delete_condition" src="<?php bloginfo('template_directory'); ?>/images/delete.gif" width="6%" style="margin-left:5%;" onclick="$(this).parent().remove();">'+
							'</div>');
					if ($.inArray('subscribe_id',data.edit_reply_condition)!=-1)
						$('#condition_list').append('<div style=" margin-top: 20px;">'+
								'<select name="edit_reply_condition_key[]" class="form-control input-sm" style="width:88%; display:inline;" size="1" type="text;" id="xtype" value="5" maxlength="20">'+
									'<option value="openid">$X-openid</option>'+
									'<option value="subscribe_id" selected="selected">$Y-关注顺序</option> '+
									'<option value="timestamp">$Z-timestamp</option>'+
								'</select>'+
								'<img id="delete_condition" src="<?php bloginfo('template_directory'); ?>/images/delete.gif" width="6%" style="margin-left:5%;" onclick="$(this).parent().remove();">'+
							'</div>');
					if ($.inArray('timestamp',data.edit_reply_condition)!=-1)
						$('#condition_list').append('<div style=" margin-top: 20px;">'+
								'<select name="edit_reply_condition_key[]" class="form-control input-sm" style="width:88%; display:inline;" size="1" type="text;" id="xtype" value="5" maxlength="20">'+
									'<option value="openid">$X-openid</option>'+
									'<option value="subscribe_id">$Y-关注顺序</option> '+
									'<option value="timestamp" selected="selected">$Z-timestamp</option>'+
								'</select>'+
								'<img id="delete_condition" src="<?php bloginfo('template_directory'); ?>/images/delete.gif" width="6%" style="margin-left:5%;" onclick="$(this).parent().remove();">'+
							'</div>');
					if((data.edit_reply_type & 4) == 4)
						$('input[value="autorep"]').prop("checked","checked");
					if((data.edit_reply_type & 2) == 2)
						$('input[value="nokeyword"]').prop("checked","checked");
					if((data.edit_reply_type & 1) == 1)
						$('input[value="keyword"]').prop("checked","checked");
					$('#edit_reply_keyword').val(data.edit_reply_keyword);
					if(data.edit_reply_activity == "1"){
						$('#edit_reply_activity[value="0"]').removeAttr("checked");
						$('#edit_reply_activity[value="1"]').prop("checked","checked");
						}
					else{
						$('#edit_reply_activity[value="1"]').removeAttr("checked");
						$('#edit_reply_activity[value="0"]').prop("checked","checked");
						}
					$('#edit_reply_code').val(data.edit_reply_code);
					$('#edit_reply_textstart').val(data.edit_reply_textstart);
					$('#edit_reply_textend').val(data.edit_reply_textend);
					$('#delete_reply').css('display','inline');

				},
				'json'
			);
		});
		function flush_form(){
			$('#reply_id').val('0');
			$('#edit_reply_name').val('');
			$('#condition_list').empty();
			$('input[type="checkbox"]').removeAttr("checked");
			$('#edit_reply_keyword').val('');
			$('#edit_reply_activity[value="0"]').prop("checked","checked");
			$('#edit_reply_code').val('');
			$('#edit_reply_textstart').val('');
			$('#edit_reply_textend').val('');
			$('#delete_reply').css('display','none');
			$('#reply_list li').css("background","");
			
		}
		$('#add_reply').click(function(){
			flush_form();
			$('#condition_list').append('<div style=" margin-top: 20px;">'+
								'<select name="edit_reply_condition_key[]" class="form-control input-sm" style="width:88%; display:inline;" size="1" type="text;" id="xtype" value="5" maxlength="20">'+
									'<option value="openid" selected="selected">$X-openid</option>'+
									'<option value="subscribe_id">$Y-关注顺序</option> '+
									'<option value="timestamp">$Z-timestamp</option>'+
								'</select>'+
								'<img id="delete_condition" src="<?php bloginfo('template_directory'); ?>/images/delete.gif" width="6%" style="margin-left:5%;" onclick="$(this).parent().remove();">'+
							'</div>');
		});
	</script>
	</body>
</html>