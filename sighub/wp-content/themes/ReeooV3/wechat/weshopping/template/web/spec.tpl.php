<?php defined('IN_IA') or exit('Access Denied');?><div class="alert alert-new spec_item" style='width:100%;' id='spec_<?php  echo $spec['id'];?>' >
	<input name="spec_id[]" type="hidden" class="form-control spec_id" value="<?php  echo $spec['id'];?>"/>
	<div class="form-group">
		<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label"> 规格名</label>
		<div class="col-xs-12 col-sm-8 col-lg-9">
			<input name="spec_title[<?php  echo $spec['id'];?>]" type="text" class="form-control  spec_title" value="<?php  echo $spec['title'];?>" placeholder="(比如: 颜色)"/>
		</div>
	</div>
	<div class="form-group">
		<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">规格项</label>
		<div class="col-xs-12 col-sm-8 col-lg-9">
			<div id='spec_item_<?php  echo $spec['id'];?>' class='spec_item_items'>
			<?php  if(is_array($spec['items'])) { foreach($spec['items'] as $specitem) { ?>
			<?php  include $this->template('spec_item')?>
			<?php  } } ?>
			</div>
		</div>
		<div class="col-xs-12 col-sm-8 col-lg-9">
			<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">&nbsp;&nbsp;&nbsp;</label>
			<div class="col-xs-12 col-sm-8 col-lg-9">
				<a href="javascript:;" id="add-specitem-<?php  echo $spec['id'];?>" specid='<?php  echo $spec['id'];?>' class='btn btn-info add-specitem' onclick="addSpecItem('<?php  echo $spec['id'];?>')"><i class="fa fa-plus"></i> 添加规格项</a>
				<a href="javascript:void(0);" class='btn btn-danger' onclick="removeSpec('<?php  echo $spec['id'];?>')"><i class="fa fa-plus"></i> 删除规格</a>
			</div>
		</div>
	</div>
</div>