<?php defined('IN_IA') or exit('Access Denied');?><?php include $this -> template('header', TEMPLATE_INCLUDEPATH);
$upload =wp_upload_dir();?>
<style type="text/css">
.form .alert{width:700px;}
#editor{overflow:auto; height:260px; border: 1px solid #ccc; width: 675px;}
.main_auto{padding-left: 4px;}
div.progressbar{display:none;height:3px;background:#C4C4C4;padding:0px;}
div.progressbar .scrollbar{display:block;width:675px;height:3px;background:red;}
</style>
<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/we7/style/daterangepicker.css">
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/we7/script/daterangepicker.js"></script>
<script src="<?php bloginfo('template_directory') ?>/js/tinymce/jquery.tinymce.min.js"></script>
<script src="<?php bloginfo('template_directory') ?>/js/tinymce/tinymce.min.js?<?php echo time() ?>"></script>
<div class="main_auto">
	<div class="main-title">
		<div class="title-1">当前位置：首页&gt;<a href="<?php echo $this->createWebUrl('display',array('gweid' => $_GPC['gweid']));?>">微预约</a> > <font class="fontpurple"><?php echo $reid?"修改":"创建新"?>微预约</font></div>
	</div>

	<div class="main" style="height:2000px">
		<form id="postform" class="form" action="" method="post" enctype="multipart/form-data" onsubmit="return validate(this);">
			<h4>预约活动 <small>通过预约你可以实现服务预约, 在线咨询, 在线调查等功能</small></h4>
			<table class="tb" style="width: 812px;">
				<tr>
					<th><label for="">预约名称</label></th>
					<td>
						<input type="text" class="span4" placeholder="" name="activity" value="<?php echo $activity['title'];?>" <?php if($hasData) echo 'disabled' ?>/>
					</td>
				</tr>
				<tr>
					<th><label for="">预约分类</label></th>
					<td>
						<select name="category" class="span3" <?php if($hasData) echo 'disabled' ?>>
							<option value="0">默认分类</option>
							<?php foreach($categories as $category){ ?>
							<option value="<?php echo $category['id']; ?>" <?php if($activity['category'] == $category['id']) { ?> selected="selected"<?php } ?> ><?php echo $category['name']; ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<tr>
					<th><label for="">预约简介</label></th>
					<td>
						<textarea type="text" class="span8 richtext-clone" placeholder="" id="description" name="description" style="width:615px"><?php echo stripslashes($activity['description']);?></textarea>
						<span class="help-block">此预约活动的说明信息. 例如: 请提交你的联系方式, 和要咨询的产品信息. 我们会尽快联系你</span>
					</td>
				</tr>
		
				<tr>
					<th><label for="">提交提示信息</label></th>
					<td>
						<textarea type="text" class="span8" placeholder="" name="information" <?php if($hasData) echo 'disabled' ?>><?php echo $activity['information'];?></textarea>
						<span class="help-block">预约提交成功以后提示的信息. 例如: 你的咨询问题我们已经收到, 很快会有专人联系你. </span>
					</td>
				</tr>
				<tr>
					<th><label for="">预约开始/结束时间</label></th>
					<td>
						<button style="margin:0;" class="btn span5" id="date-range" type="button" <?php if($hasData) echo 'disabled' ?>><span class="date-title"><?php echo $activity['startdate']?$activity['startdate']:date('Y-m-d')?> 至 <?php echo $activity['startdate']?$activity['enddate']:date('Y-m-d')?></span> <i class="icon-caret-down"></i></button>
						<input name="start_date" type="hidden" value="<?php echo date('Y-m-d')?>">
						<input name="end_date" type="hidden" value="<?php echo date('Y-m-d')?>">
					</td>
				</tr>
				<tr>
					<th><label for="">预约活动封面</label></th>
					<td>
						<div class="fileupload fileupload-new" data-provides="fileupload">
							<div class="fileupload-preview thumbnail" style="width: 200px; height: 150px;"><img src="<?php if((empty($activity['thumb']))||(stristr($activity['thumb'],"http")!==false)){echo $activity['thumb'];}else{echo $upload['baseurl'].$activity['thumb'];}?>" alt="" onerror="$(this).remove();"></div>
							<div>
								<span class="btn btn-file" <?php if($hasData) echo 'disabled' ?>><span class="fileupload-new">选择图片</span><span class="fileupload-exists">更改</span><input name="thumb" type="file" <?php if($hasData) echo 'disabled' ?> /></span>
								<a href="#" class="btn fileupload-exists" data-dismiss="fileupload">移除</a>
							</div>
						</div>
						<span class="help-block">用一张图片来更好的表现你的预约主题</span>
					</td>
				</tr>
				<tr>
					<th><label for="">每人可预约次数</label></th>
					<td>
						<input type="text" class="span4" name="pretotal" value="<?php if(!empty($activity['pretotal'])) { ?><?php echo $activity['pretotal'];?><?php } else { ?>1<?php } ?>" <?php if($hasData) echo 'disabled' ?> />
					</td>
				</tr>
				<tr>
					<th><label for="">要调查的内容</label></th>
					<td>
						<div class="alert alert-block alert-new">
							<table class="table table-hover">
								<thead>
									<tr>
										<th style="min-width:200px;">调查项目</th>
										<th style="width:40px;">必填</th>
										<th style="width:160px;">类型</th>
										<th style="width:160px;">关联默认值</th>
										<th style="width:120px;"></th>
									</tr>
								</thead>
								<tbody id="re-items">
									<?php if(is_array($ds)) { foreach($ds as $r) { ?>
										<tr>
											<td><input name="title[]" type="text" class="span2" value="<?php echo $r['title'];?>"/></td>
											<td><input type="checkbox" title="必填项" <?php if($r['essential']) { ?> checked="checked"<?php } ?>/></td>
											<td>
												<select name="type[]" class="span2">
												<?php if(is_array($types)) { foreach($types as $k => $v) { ?>
												<option value="<?php echo $k;?>"<?php if($k == $r['type']) { ?> selected="selected"<?php } ?>><?php echo $v;?></option>
												<?php } } ?>
												</select>
											</td>
											<td>
												<select name="bind[]" class="span2">
													<option value="">不关联粉丝数据</option>
													<?php if(is_array($fields)) { foreach($fields as $k => $v) { ?>
													<option value="<?php echo $k;?>"<?php if($k == $r['bind']) { ?> selected="selected"<?php } ?>><?php echo $v;?></option>
													<?php } } ?>
												</select>
												<input type="hidden" name="value[]" value="<?php echo $r['value'];?>"/>
												<input type="hidden" name="desc[]" value="<?php echo $r['desc'];?>"/>
												<input type="hidden" name="essential[]" value="<?php if($r['essential']) { ?>true<?php } else { ?>false<?php } ?>"/>
											</td>
											<td><?php if(!$hasData) { ?><a href="javascript:;" onclick="deleteItem(this)" class="icon-remove-sign" title="删除此条目"></a> &nbsp; <a href="javascript:;" class="icon-edit" title="设置详细信息" onclick="setValues(this);"></a><?php } ?></td>
										</tr>
									<?php } } ?>
								</tbody>
							</table>
						</div>
						<div class="alert alert-block alert-new">
							<?php if($hasData) { ?>
							<a href="javascript:;">已经存在调查数据, 不能修改调查条目信息</a>
							<?php } else { ?>
							<a href="javascript:;" onclick="addItem();">添加调查条目 <i class="icon-plus-sign" title="添加调查条目"></i></a>
							<?php } ?>
						</div>
						<span class="help-block">预约成功启动以后(已经有粉丝用户提交给预约信息), 将不能再修改调查项目, 请仔细编辑. </span>
					</td>
				</tr>
				<tr>
					<th><label for="">补充说明</label></th>
					<td>
						<textarea type="text" class="span8" placeholder="" name="postscript"></textarea>
						<span class="help-block">对预约说明内容的补充，会显示在页面最后</span>
					</td>
				</tr>			
				<tr>
					<th></th>
					<td>
						<?php if($hasData) {?>
						<button class="btn btn-primary span3 disabled" disabled>已存在调查数据，不能修改</button>
						<?php } else{?>
						<button type="submit" class="btn btn-primary span3" name="submit" value="提交">提交</button>
						<?php }?>
						<input type="hidden" name="token" value="<?php echo $_W['token'];?>" />
					</td>
				</tr>
			</table>
		</form>
		<form id="fileform"  method="post" action="<?php bloginfo('template_directory'); ?>/js/editor/php/sae_upload_json.php?dir=image"  id="uploadImgForm"  enctype="multipart/form-data" style="display:none;"> 
			<input type="file" name="imgFile" data-role="magic-overlay" data-target="#pictureBtn" data-edit="insertImage" />
		</form>
	</div>
</div>
<div id="dialog" class="modal hide fade">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">设置选项</h3>
	</div>
	<div class="modal-body">
		<div class="well">
			<textarea id="re-desc" class="span6" rows="3"></textarea>
			<span class="help-block"><strong>设置此条目的说明信息</strong></span>
		</div>
		<div class="well re-value hide">
			<textarea id="re-value" class="span6" rows="6"></textarea>
			<span class="help-block"><strong>设置预约条目的选项(如果有需要的话.) 每行一条记录, 例如: 性别 男, 女</strong></span>
		</div>
	</div>
</div>
<?php tinymce_js("#description"); ?>
<script text="text/javascript">
	var currentItem = null;
	$(function(){
		$('#dialog').on('hide', function(){
			if(currentItem == null) return;
			var v = $('#dialog #re-value').val();
			$(currentItem).parent().prev().find(':hidden[name="value[]"]').val(encodeURIComponent(v.replace(/\n/g, ',')));
			var v = $('#dialog #re-desc').val();
			$(currentItem).parent().prev().find(':hidden[name="desc[]"]').val(encodeURIComponent(v));
		});
		<?php if($hasData) { ?>
		$('#re-items').find(':text,:checkbox,select').attr('disabled', 'disabled');
		<?php } ?>
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
		$(':hidden[name=start_date]').val(start.format('YYYY-MM-DD'));
		$(':hidden[name=end_date]').val(end.format('YYYY-MM-DD'));
	});
	function setValues(o) {
		var v = $(o).parent().prev().find(':hidden[name="value[]"]').val();
		v = decodeURIComponent(v);
		$('#dialog #re-value').val(v.replace(/,/g, '\n'));
		var v = $(o).parent().prev().find(':hidden[name="desc[]"]').val();
		v = decodeURIComponent(v);
		$('#dialog #re-desc').val(v);
		var v = $(o).parent().prev().prev().find('select[name="type[]"]').val();
		if(v == 'radio' || v == 'checkbox' || v == 'select') {
			$('.well.re-value').show();
		} else {
			$('.well.re-value').hide();
		}
		$('#dialog').modal({keyboard: false});
		currentItem = o;
	}
	function addItem() {
		var html = '' + 
				'<tr>' +
					'<td><input name="title[]" type="text" class="span2" /></td>' +
					'<td><input type="checkbox" title="必填项" /></td>' +
					'<td>' +
						'<select name="type[]" class="span2">' +
						<?php if(is_array($types)) { foreach($types as $k => $v) { ?>'<option value="<?php echo $k;?>"><?php echo $v;?></option>' + <?php } } ?>
						'</select>' +
					'</td>' +
					'<td>' +
						'<select name="bind[]" class="span2">' +
							'<option value="">不关联粉丝数据</option>' +
						<?php if(is_array($fields)) { foreach($fields as $k => $v) { ?><?php if(!empty($v)) { ?>'<option value="<?php echo $k;?>"><?php echo $v;?></option>' + <?php } ?><?php } } ?>
						'</select>' +
						'<input type="hidden" name="value[]" />' +
						'<input type="hidden" name="desc[]" />' +
						'<input type="hidden" name="essential[]" />' +
					'</td>' +
					'<td> <a href="javascript:;" onclick="deleteItem(this)" class="icon-remove-sign" title="删除此条目"></a> &nbsp; <a href="javascript:;" class="icon-edit" title="设置详细信息" onclick="setValues(this);"></a></td>' +
				'</tr>';
		$('#re-items').append(html);
	}
	function deleteItem(o) {
		$(o).parent().parent().remove();
	}
	function validate() {
		
		if($.trim($(':text[name="activity"]').val()) == '') {
			alert('必须填写预约活动标题.');
			return false;
		}
		/*
		if($.trim(KindEditor.instances[0].html()) == '') {
			alert('必须填写预约活动说明.');
			return false;
		}
		if($.trim($('textarea[name="information"]').val()) == '') {
			alert('必须填写预约活动成功提示信息.');
			return false;
		}
		*/
		if($(':text[name="title[]"]').length == 0) {
			alert('必须设定预约活动的调查条目.');
			return false;
		}
		var isError = false;
		$(':text[name="title[]"]').each(function(){
			if($.trim($(this).val()) == '') {
				isError = true;
			}
		});
		if(isError) {
			alert('必须要设定每个调查条目的标题.');
			return false;
		}
		var isError = false;
		$('#re-items tr').each(function(){
			var t = $(this).find('select[name="type[]"]').val();
			if(t == 'radio' || t == 'checkbox' || t == 'select') {
				if($.trim($(this).find(':hidden[name="value[]"]').val()) == '') {
					isError = true;
				}
			}
		});
		if(isError) {
			alert('单选, 多选或下拉选择项目必须要设定备选项.');
			return false;
		}
		$(':checkbox').each(function(){
			if($(this).attr('checked') == 'checked') {
				$(this).parent().next().next().find(':hidden[name="essential[]"]').val('true');
			} else {
				$(this).parent().next().next().find(':hidden[name="essential[]"]').val('false');
			}
		});
		window.parent.parent.scroll(0,0);
		return true;
	}
	$('.modal').on('show', function() {
		modal = $('.modal');
		modal.css('margin-top', $('#postform').height() / 2);
	});
</script>
</body>
</html>
