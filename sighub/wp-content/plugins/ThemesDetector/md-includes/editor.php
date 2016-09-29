<?php

function bootstrap_wysiwyg_toolbar_html($content = "", $target_id = "editor",$is_single_line = true){

}

function bootstrap_wysiwyg_upload_form_html($target_id = "editor"){
	$template_directory = get_bloginfo('template_directory');
	echo <<<EOT
	 <form id="fileform"  method="post" action="{$template_directory}/js/editor/php/sae_upload_json.php?dir=image"  id="uploadImgForm"  enctype="multipart/form-data" data-target="#{$target_id}"> 
		<input type="file" name="imgFile" data-role="magic-overlay" />
	</form>
EOT;
}

function bootstrap_wysiwyg_js_require_html(){
	$template_directory = get_bloginfo('template_directory');
	echo <<<EOT
	<script src="{$template_directory}/we7/script/jquery.hotkeys.js"></script>
	<script charset="utf-8" src="{$template_directory}/js/bootstrap-wysiwyg-3.js?<?php echo time() ?>"></script>
	<script src="{$template_directory}/js/google-code-prettify/prettify.js"></script>
	<link rel="stylesheet" href="{$template_directory}/js/google-code-prettify/prettify.css">
	<script src="{$template_directory}/js/jquery.form.js"></script>
EOT;
}

function tinymce_js($selector,$narrow = false){
		echo <<<EOT
	<script type="text/javascript">
		tinymce.init({
		    selector: "{$selector}",
		    theme: "modern",
		    language: "zh_CN",
		    height: "180",
EOT;
if(!$narrow)
echo <<<EOT
		    plugins: [
		        "advlist autolink lists link image charmap print preview hr anchor pagebreak",
		        "searchreplace visualblocks visualchars code fullscreen",
		        "insertdatetime media nonbreaking save table contextmenu directionality",
		        "emoticons paste textcolor colorpicker textpattern imagetools jbimages"
		    ],
		    toolbar1: "styleselect | fontsizeselect | fontselect | bold italic | forecolor backcolor | alignleft aligncenter alignright alignjustify",
		    toolbar2: "undo redo | bullist numlist outdent indent | print preview media | link jbimages",
EOT;
if($narrow)
echo <<<EOT
		    plugins: [
		        "advlist autolink lists link image charmap print preview hr anchor pagebreak",
		        "searchreplace visualblocks visualchars code fullscreen",
		        "insertdatetime media nonbreaking save table contextmenu directionality",
		        "emoticons paste textcolor colorpicker textpattern imagetools jbimages"
		    ],
		    toolbar1: "styleselect | fontsizeselect | fontselect | bold italic | forecolor backcolor",
		    toolbar2: "alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link",
		    toolbar3: "undo redo | print preview media | jbimages",
		    menubar: "edit insert view format table tools",
EOT;
echo <<<EOT
			image_advtab: true,
			advlist_bullet_styles: "disc,circle,square",
			advlist_number_styles: "decimal,lower-alpha,lower-greek,lower-roman,upper-alpha,upper-roman",
		    fontsize_formats: "8pt 10pt 12pt 14pt 18pt 24pt 36pt 48pt",
		    font_formats: "宋体=宋体;"+
		    	"新宋体=新宋体;"+
		    	"仿宋=仿宋;"+
		    	"楷体=楷体;"+
		    	"黑体=黑体;"+
		    	"微软雅黑=微软雅黑;"+
		        "Arial=arial,helvetica,sans-serif;"+
		        "Arial Black=arial black,avant garde;"+
		        "Book Antiqua=book antiqua,palatino;"+
		        "Courier New=courier new,courier;"+
		        "Tahoma=tahoma,arial,helvetica,sans-serif;"+
		        "Times New Roman=times new roman,times;"+
		        "Verdana=verdana,geneva;",
		    setup : function(editor) {
		    	editor.on('change', function(e) {
		            if(typeof tinymceOnChange === "function")
		            	tinymceOnChange(e);
		        });
				editor.on('init', function(e) {
		            if(typeof tinymceOnInit === "function")
		            	tinymceOnInit(e);
		        });
			}
		});
	</script>
EOT;
}