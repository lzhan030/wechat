<?php defined('IN_IA') or exit('Access Denied');?>

<div class="spec_item_item" style="float:left;margin:0 5px 10px 0;">
	<input type="hidden" class="form-control spec_item_show" name="spec_item_show_<?php  echo $spec['id'];?>[]" VALUE="<?php  echo $specitem['show'];?>" />
	<input type="hidden" class="form-control spec_item_id" name="spec_item_id_<?php  echo $spec['id'];?>[]" VALUE="<?php  echo $specitem['id'];?>" />
	<div class="input-group"  style="margin:10px 0;">
		<span class="input-group-addon">
			<label class="checkbox-inline" style="margin-top:-20px;">
				<input type="checkbox" <?php  if($specitem['show']==1) { ?>checked<?php  } ?> value="1" onclick='showItem(this)'>
			</label>
		</span>
		<input type="text" class="form-control spec_item_title error" name="spec_item_title_<?php  echo $spec['id'];?>[]" VALUE="<?php  echo $specitem['title'];?>" />
		<span class="input-group-addon">
			<a href="javascript:;" onclick="removeSpecItem(this)" title='删除'><i class="fa fa-times"></i></a>
	  		<a href="javascript:;" class="fa fa-arrows" title="拖动调整显示顺序" ></a>
		</span>
	</div>
	<div>
		<?php 
		/*new add*/
		$upload =wp_upload_dir();
		if((empty($specitem['thumb']))||(stristr($specitem['thumb'],"http")!==false)){
			$sitempicurl=$specitem['thumb'];
		}else{
			$sitempicurl=$upload['baseurl'].$specitem['thumb'];
		}	
		$value=$sitempicurl;
		$name='spec_item_thumb_'.$spec['id']."[]";
		
		
		if(empty($default)) {
			$default =  get_template_directory_uri()."/images/nopic.jpg";
		}
		$val = $default;
		if(!empty($value)) {
			$val = $value;
		}
		if(empty($options['tabs'])){
			$options['tabs'] = array('browser'=>'active', 'upload'=>'');
		}
		if(empty($options['width'])) {
			$options['width'] = 800;
		}
		if(empty($options['height'])) {
			$options['height'] = 600;
		}
		if(!empty($options['global'])){
			$options['global'] = true;
		} else {
			$options['global'] = false;
		}
		if(empty($options['class_extra'])) {
			$options['class_extra'] = '';
		}
		
		$options = $this->array_elements(array('width', 'height', 'extras', 'global', 'class_extra', 'tabs'), $options);
		?>
		<div class="input-group<?php echo $options['class_extra'] ?>">
			<input id="specitem<?php echo $specitem['id']?>"    type="hidden" name="<?php echo $name ?>" value="<?php echo $value ?>"  <?php echo $options['extras']['text'] ? $options['extras']['text'] : '' ?>  class="form-control" autocomplete="off">
			<button class="btn btn-default" type="button" onclick="picktrue(this,'<?php echo $specitem['id']?>');">选择图片</button>
		</div>
		<?php 
		if(!empty($options['tabs']['browser']) || !empty($options['tabs']['upload'])){
		?>
			<div class="input-group<?php echo $options['class_extra'] ?>" style="margin-top:.5em;">
				<img id="specitemimg<?php echo $specitem['id']?>"  src="<?php echo $val ?>" onerror="this.src='<?php echo $default ?>'; this.title='图片未找到'" class="img-responsive img-thumbnail" width="150"  <?php echo $options['extras']['image'] ? $options['extras']['image'] : '' ?> />
				<em id='itempicurl<?php echo $specitem['id']?>' class="close" style="position:absolute; top: 0px; margin-left:9px;<?php if(empty($sitempicurl)){?> display:none;<?php } ?>" title="删除这张图片" onclick="delItemImage('<?php echo $specitem['id']?>')">×</em>
				<div class="help-block">图片建议尺寸：宽50像素 * 高50像素 </div>
			</div>
		<?php } ?>
		
		<!--new add end-->

	</div>
</div>

