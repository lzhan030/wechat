<?php defined('IN_IA') or exit('Access Denied');?>
	<script src="<?php bloginfo('template_directory') ?>/js/tinymce/jquery.tinymce.min.js"></script>
<script src="<?php bloginfo('template_directory') ?>/js/tinymce/tinymce.min.js"></script>
<div class="form-group">
	<label class="col-xs-12 col-sm-2 col-md-2 control-label">商品详情</label>
	<div class="col-sm-10 col-xs-12">
	<textarea name="content" id="content" class="form-control richtext"><?php echo $content ?></textarea>
	</div>
</div>
<?php tinymce_js("#content"); ?>