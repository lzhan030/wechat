<?php

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');

?>

<?php
    include '../common/dbaccessor.php';
	include '../../wechat/common/wechat_dbaccessor.php';
	include '../common/web_constant.php';
	global $current_user;
	//判断是否是分组管理员中的用户
	$groupadminflag = web_admin_issuperadmin($current_user->ID);
	$user_id =  ((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($groupadminflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
	//$user_id = (is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;


	//拿到window.open里传递过来的值	
	$menid=intval($_GET["menuId"]);	
	$siteId=intval($_GET["siteId"]);	
	
	$we7templateSelected = $wpdb -> get_var("SELECT site_value FROM {$wpdb -> prefix}orangesitemeta WHERE site_id='{$siteId}' AND site_key = 'we7templatestyle'");
	$template = $wpdb -> get_row("SELECT * FROM {$wpdb -> prefix}site_templates WHERE name='{$we7templateSelected}'");
	
	
	//获取特定的menu
	$menu=$wpdb -> get_results("SELECT * FROM {$wpdb -> prefix}site_nav WHERE `site_id`={$siteId} AND id={$menid}");	
	//2014-06-27新增修改
	//是否选中是通过gweid，userid以及wid共同决定的,是由所有的号选中的功能的并集
	$GWEID = $_SESSION['GWEID'];
	$getwids = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechat_usechat")." WHERE GWEID = ".$GWEID."  AND user_id = ".$user_id);
	
	$wechatactivity_vip=web_admin_function_info_groupnew($GWEID,"wechatvip",$user_id);
	$wechatschool=web_admin_function_info_groupnew($GWEID,"wechatschool",$user_id);
	
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js"></script>
	<link rel="stylesheet" href="../../css/wsite.css">
	<link rel="stylesheet" href="../../css/bootstrap.min.css">
	<script src="../../js/jquery.min.js"></script>
	<script src="../../js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="../../css/docs.min.css">
	<link rel="stylesheet" href="../../we7/script/colorpicker/spectrum.css">
	<script src="../../we7/script/colorpicker/spectrum.js"></script>
	<script src="../../js/bootstrap.min.js"></script>
	<title>更新菜单按钮内容</title>
</head>
<body>

	<div class="panel-body">
		<form name ="content" onSubmit="return validateform()" action="<?php echo constant("CONF_THEME_DIR"); ?>/wesite/mobilepagewe7/menu_update.php?beIframe" method="post" enctype="multipart/form-data">
			<table width="600" bordercolor=#06c border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:30px;">
			<?php 
			$upload =wp_upload_dir();
			foreach($menu as $menu_info){
				$menu_info -> css = unserialize($menu_info -> css);
				echo "<tr><td><label for='name'>菜单名称：</label>";
				echo "<input type='text' class='form-control' id='name' name='menu_title' value='{$menu_info->name}' /></td></tr>";
			?>
			<tr <?php echo ($template -> menu_bg == '0')?'style="display:none;"':''; ?>>	
				<td style="padding:10px 0;">
					<label for="name">菜单文本背景颜色:</label>
					<input type="text" class="span3" id="menubgcolor" name="icon[menubgcolor]" value="<?php echo !empty($menu_info -> css['icon']['menubgcolor'])?$menu_info -> css['icon']['menubgcolor']:'#ffffff'?>" />
					<input class="colorpicker" target="menubgcolor" value="<?php echo !empty($menu_info -> css['icon']['menubgcolor'])?$menu_info -> css['icon']['menubgcolor']:'#ffffff';?>" />
				</td>
			</tr>			
			<?php	
				if($template -> name == 'style7'){
					echo "<tr ".($template -> image_icon == '0'?'style="display:none;"':'')."><td><label for='pic'>上传菜单图片：</label>（建议上传图片大小为250*120）</td></tr>";
				}else{
					echo "<tr ".($template -> image_icon == '0'?'style="display:none;"':'')."><td><label for='pic'>上传菜单图片：</label>（建议上传图片大小为100*100）</td></tr>";
				}
				
				$icon=$menu_info -> icon;
				//if(strpos($icon,'http')!==FALSE){
				if((strpos($icon,'glyphicon') === FALSE)&&(!empty($icon))){
					if(stristr($icon,"http")!==false){
						$icon=$icon;		
					}else{
						$icon=$upload['baseurl'].$icon;
					}
					
					if($template -> name == 'style7'){
						echo "<tr ".($template -> image_icon == '0'?'style="display:none;"':'')."><td><img id='pic' src='{$icon}' height='90' width='140'/>";
					}else{
						echo "<tr ".($template -> image_icon == '0'?'style="display:none;"':'')."><td><img id='pic' src='{$icon}' height='90' width='90'/>";
					}
					
					echo "<tr><td><a id='picurl' href='#' onclick='delImage()'>删除图片</a>";
					echo '<span id="icon" style="font-size: 40px; padding: 10px 48px 6px 6px; margin: 0px 15px 20px;"></span>
						<input type="hidden" name="icon_class">';
				} else if(!empty($icon)){
					
					if($template -> name == 'style7'){
						echo "<tr ".($template -> image_icon == '0'?'style="display:none;"':'')."><td><img id='pic' src='' height='90' width='140' style='display:none;'/>";
					}else{
						echo "<tr ".($template -> image_icon == '0'?'style="display:none;"':'')."><td><img id='pic' src='' height='90' width='90' style='display:none;'/>";
					}
				?>
				
				<span id="icon" class="glyphicon <?php echo $icon;?>"style="font-size: 40px; padding: 10px 48px 6px 6px; margin: 0px 15px 20px;"></span>
				<input type="hidden" name="icon_class" value="<?php echo $icon;?>">
				<?php
				} else{
				?>
					<?php if($template -> name == 'style7'){
					?>
						<tr <?php echo ($template -> image_icon == '0')?'style="display:none;"':''; ?>><td><img id='pic' src='' height='90' width='140' style='display:none;'/>
					<?php }else{ ?>
						<tr <?php echo ($template -> image_icon == '0')?'style="display:none;"':''; ?>><td><img id='pic' src='' height='90' width='90' style='display:none;'/>
					<?php } ?>
					
					<span id="icon" style="font-size: 40px; padding: 10px 48px 6px 6px; margin: 0px 15px 20px;"></span>
					<input type="hidden" name="icon_class">
				<?php
				}					
				echo "<input type='file' class='form-control' name='file' id='file' onchange='previewImage(this)' style='margin-bottom:30px;'/>";
				?><div class="bs-glyphicons" style="height: 200px;overflow: auto;margin-top: 20px;">
						<ul class="bs-glyphicons-list">
						  
							<li>
							  <span class="glyphicon glyphicon-asterisk"></span>
							  <span class="glyphicon-class">glyphicon-asterisk</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-plus"></span>
							  <span class="glyphicon-class">glyphicon-plus</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-euro"></span>
							  <span class="glyphicon-class">glyphicon-euro</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-minus"></span>
							  <span class="glyphicon-class">glyphicon-minus</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-cloud"></span>
							  <span class="glyphicon-class">glyphicon-cloud</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-envelope"></span>
							  <span class="glyphicon-class">glyphicon-envelope</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-pencil"></span>
							  <span class="glyphicon-class">glyphicon-pencil</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-glass"></span>
							  <span class="glyphicon-class">glyphicon-glass</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-music"></span>
							  <span class="glyphicon-class">glyphicon-music</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-search"></span>
							  <span class="glyphicon-class">glyphicon-search</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-heart"></span>
							  <span class="glyphicon-class">glyphicon-heart</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-star"></span>
							  <span class="glyphicon-class">glyphicon-star</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-star-empty"></span>
							  <span class="glyphicon-class">glyphicon-star-empty</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-user"></span>
							  <span class="glyphicon-class">glyphicon-user</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-film"></span>
							  <span class="glyphicon-class">glyphicon-film</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-th-large"></span>
							  <span class="glyphicon-class">glyphicon-th-large</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-th"></span>
							  <span class="glyphicon-class">glyphicon-th</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-th-list"></span>
							  <span class="glyphicon-class">glyphicon-th-list</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-ok"></span>
							  <span class="glyphicon-class">glyphicon-ok</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-remove"></span>
							  <span class="glyphicon-class">glyphicon-remove</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-zoom-in"></span>
							  <span class="glyphicon-class">glyphicon-zoom-in</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-zoom-out"></span>
							  <span class="glyphicon-class">glyphicon-zoom-out</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-off"></span>
							  <span class="glyphicon-class">glyphicon-off</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-signal"></span>
							  <span class="glyphicon-class">glyphicon-signal</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-cog"></span>
							  <span class="glyphicon-class">glyphicon-cog</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-trash"></span>
							  <span class="glyphicon-class">glyphicon-trash</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-home"></span>
							  <span class="glyphicon-class">glyphicon-home</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-file"></span>
							  <span class="glyphicon-class">glyphicon-file</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-time"></span>
							  <span class="glyphicon-class">glyphicon-time</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-road"></span>
							  <span class="glyphicon-class">glyphicon-road</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-download-alt"></span>
							  <span class="glyphicon-class">glyphicon-download-alt</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-download"></span>
							  <span class="glyphicon-class">glyphicon-download</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-upload"></span>
							  <span class="glyphicon-class">glyphicon-upload</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-inbox"></span>
							  <span class="glyphicon-class">glyphicon-inbox</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-play-circle"></span>
							  <span class="glyphicon-class">glyphicon-play-circle</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-repeat"></span>
							  <span class="glyphicon-class">glyphicon-repeat</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-refresh"></span>
							  <span class="glyphicon-class">glyphicon-refresh</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-list-alt"></span>
							  <span class="glyphicon-class">glyphicon-list-alt</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-lock"></span>
							  <span class="glyphicon-class">glyphicon-lock</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-flag"></span>
							  <span class="glyphicon-class">glyphicon-flag</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-headphones"></span>
							  <span class="glyphicon-class">glyphicon-headphones</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-volume-off"></span>
							  <span class="glyphicon-class">glyphicon-volume-off</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-volume-down"></span>
							  <span class="glyphicon-class">glyphicon-volume-down</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-volume-up"></span>
							  <span class="glyphicon-class">glyphicon-volume-up</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-qrcode"></span>
							  <span class="glyphicon-class">glyphicon-qrcode</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-barcode"></span>
							  <span class="glyphicon-class">glyphicon-barcode</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-tag"></span>
							  <span class="glyphicon-class">glyphicon-tag</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-tags"></span>
							  <span class="glyphicon-class">glyphicon-tags</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-book"></span>
							  <span class="glyphicon-class">glyphicon-book</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-bookmark"></span>
							  <span class="glyphicon-class">glyphicon-bookmark</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-print"></span>
							  <span class="glyphicon-class">glyphicon-print</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-camera"></span>
							  <span class="glyphicon-class">glyphicon-camera</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-font"></span>
							  <span class="glyphicon-class">glyphicon-font</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-bold"></span>
							  <span class="glyphicon-class">glyphicon-bold</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-italic"></span>
							  <span class="glyphicon-class">glyphicon-italic</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-text-height"></span>
							  <span class="glyphicon-class">glyphicon-text-height</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-text-width"></span>
							  <span class="glyphicon-class">glyphicon-text-width</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-align-left"></span>
							  <span class="glyphicon-class">glyphicon-align-left</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-align-center"></span>
							  <span class="glyphicon-class">glyphicon-align-center</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-align-right"></span>
							  <span class="glyphicon-class">glyphicon-align-right</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-align-justify"></span>
							  <span class="glyphicon-class">glyphicon-align-justify</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-list"></span>
							  <span class="glyphicon-class">glyphicon-list</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-indent-left"></span>
							  <span class="glyphicon-class">glyphicon-indent-left</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-indent-right"></span>
							  <span class="glyphicon-class">glyphicon-indent-right</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-facetime-video"></span>
							  <span class="glyphicon-class">glyphicon-facetime-video</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-picture"></span>
							  <span class="glyphicon-class">glyphicon-picture</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-map-marker"></span>
							  <span class="glyphicon-class">glyphicon-map-marker</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-adjust"></span>
							  <span class="glyphicon-class">glyphicon-adjust</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-tint"></span>
							  <span class="glyphicon-class">glyphicon-tint</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-edit"></span>
							  <span class="glyphicon-class">glyphicon-edit</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-share"></span>
							  <span class="glyphicon-class">glyphicon-share</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-check"></span>
							  <span class="glyphicon-class">glyphicon-check</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-move"></span>
							  <span class="glyphicon-class">glyphicon-move</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-step-backward"></span>
							  <span class="glyphicon-class">glyphicon-step-backward</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-fast-backward"></span>
							  <span class="glyphicon-class">glyphicon-fast-backward</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-backward"></span>
							  <span class="glyphicon-class">glyphicon-backward</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-play"></span>
							  <span class="glyphicon-class">glyphicon-play</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-pause"></span>
							  <span class="glyphicon-class">glyphicon-pause</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-stop"></span>
							  <span class="glyphicon-class">glyphicon-stop</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-forward"></span>
							  <span class="glyphicon-class">glyphicon-forward</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-fast-forward"></span>
							  <span class="glyphicon-class">glyphicon-fast-forward</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-step-forward"></span>
							  <span class="glyphicon-class">glyphicon-step-forward</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-eject"></span>
							  <span class="glyphicon-class">glyphicon-eject</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-chevron-left"></span>
							  <span class="glyphicon-class">glyphicon-chevron-left</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-chevron-right"></span>
							  <span class="glyphicon-class">glyphicon-chevron-right</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-plus-sign"></span>
							  <span class="glyphicon-class">glyphicon-plus-sign</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-minus-sign"></span>
							  <span class="glyphicon-class">glyphicon-minus-sign</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-remove-sign"></span>
							  <span class="glyphicon-class">glyphicon-remove-sign</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-ok-sign"></span>
							  <span class="glyphicon-class">glyphicon-ok-sign</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-question-sign"></span>
							  <span class="glyphicon-class">glyphicon-question-sign</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-info-sign"></span>
							  <span class="glyphicon-class">glyphicon-info-sign</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-screenshot"></span>
							  <span class="glyphicon-class">glyphicon-screenshot</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-remove-circle"></span>
							  <span class="glyphicon-class">glyphicon-remove-circle</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-ok-circle"></span>
							  <span class="glyphicon-class">glyphicon-ok-circle</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-ban-circle"></span>
							  <span class="glyphicon-class">glyphicon-ban-circle</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-arrow-left"></span>
							  <span class="glyphicon-class">glyphicon-arrow-left</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-arrow-right"></span>
							  <span class="glyphicon-class">glyphicon-arrow-right</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-arrow-up"></span>
							  <span class="glyphicon-class">glyphicon-arrow-up</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-arrow-down"></span>
							  <span class="glyphicon-class">glyphicon-arrow-down</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-share-alt"></span>
							  <span class="glyphicon-class">glyphicon-share-alt</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-resize-full"></span>
							  <span class="glyphicon-class">glyphicon-resize-full</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-resize-small"></span>
							  <span class="glyphicon-class">glyphicon-resize-small</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-exclamation-sign"></span>
							  <span class="glyphicon-class">glyphicon-exclamation-sign</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-gift"></span>
							  <span class="glyphicon-class">glyphicon-gift</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-leaf"></span>
							  <span class="glyphicon-class">glyphicon-leaf</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-fire"></span>
							  <span class="glyphicon-class">glyphicon-fire</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-eye-open"></span>
							  <span class="glyphicon-class">glyphicon-eye-open</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-eye-close"></span>
							  <span class="glyphicon-class">glyphicon-eye-close</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-warning-sign"></span>
							  <span class="glyphicon-class">glyphicon-warning-sign</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-plane"></span>
							  <span class="glyphicon-class">glyphicon-plane</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-calendar"></span>
							  <span class="glyphicon-class">glyphicon-calendar</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-random"></span>
							  <span class="glyphicon-class">glyphicon-random</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-comment"></span>
							  <span class="glyphicon-class">glyphicon-comment</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-magnet"></span>
							  <span class="glyphicon-class">glyphicon-magnet</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-chevron-up"></span>
							  <span class="glyphicon-class">glyphicon-chevron-up</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-chevron-down"></span>
							  <span class="glyphicon-class">glyphicon-chevron-down</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-retweet"></span>
							  <span class="glyphicon-class">glyphicon-retweet</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-shopping-cart"></span>
							  <span class="glyphicon-class">glyphicon-shopping-cart</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-folder-close"></span>
							  <span class="glyphicon-class">glyphicon-folder-close</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-folder-open"></span>
							  <span class="glyphicon-class">glyphicon-folder-open</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-resize-vertical"></span>
							  <span class="glyphicon-class">glyphicon-resize-vertical</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-resize-horizontal"></span>
							  <span class="glyphicon-class">glyphicon-resize-horizontal</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-hdd"></span>
							  <span class="glyphicon-class">glyphicon-hdd</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-bullhorn"></span>
							  <span class="glyphicon-class">glyphicon-bullhorn</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-bell"></span>
							  <span class="glyphicon-class">glyphicon-bell</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-certificate"></span>
							  <span class="glyphicon-class">glyphicon-certificate</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-thumbs-up"></span>
							  <span class="glyphicon-class">glyphicon-thumbs-up</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-thumbs-down"></span>
							  <span class="glyphicon-class">glyphicon-thumbs-down</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-hand-right"></span>
							  <span class="glyphicon-class">glyphicon-hand-right</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-hand-left"></span>
							  <span class="glyphicon-class">glyphicon-hand-left</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-hand-up"></span>
							  <span class="glyphicon-class">glyphicon-hand-up</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-hand-down"></span>
							  <span class="glyphicon-class">glyphicon-hand-down</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-circle-arrow-right"></span>
							  <span class="glyphicon-class">glyphicon-circle-arrow-right</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-circle-arrow-left"></span>
							  <span class="glyphicon-class">glyphicon-circle-arrow-left</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-circle-arrow-up"></span>
							  <span class="glyphicon-class">glyphicon-circle-arrow-up</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-circle-arrow-down"></span>
							  <span class="glyphicon-class">glyphicon-circle-arrow-down</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-globe"></span>
							  <span class="glyphicon-class">glyphicon-globe</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-wrench"></span>
							  <span class="glyphicon-class">glyphicon-wrench</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-tasks"></span>
							  <span class="glyphicon-class">glyphicon-tasks</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-filter"></span>
							  <span class="glyphicon-class">glyphicon-filter</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-briefcase"></span>
							  <span class="glyphicon-class">glyphicon-briefcase</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-fullscreen"></span>
							  <span class="glyphicon-class">glyphicon-fullscreen</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-dashboard"></span>
							  <span class="glyphicon-class">glyphicon-dashboard</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-paperclip"></span>
							  <span class="glyphicon-class">glyphicon-paperclip</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-heart-empty"></span>
							  <span class="glyphicon-class">glyphicon-heart-empty</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-link"></span>
							  <span class="glyphicon-class">glyphicon-link</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-phone"></span>
							  <span class="glyphicon-class">glyphicon-phone</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-pushpin"></span>
							  <span class="glyphicon-class">glyphicon-pushpin</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-usd"></span>
							  <span class="glyphicon-class">glyphicon-usd</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-gbp"></span>
							  <span class="glyphicon-class">glyphicon-gbp</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-sort"></span>
							  <span class="glyphicon-class">glyphicon-sort</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-sort-by-alphabet"></span>
							  <span class="glyphicon-class">glyphicon-sort-by-alphabet</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-sort-by-alphabet-alt"></span>
							  <span class="glyphicon-class">glyphicon-sort-by-alphabet-alt</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-sort-by-order"></span>
							  <span class="glyphicon-class">glyphicon-sort-by-order</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-sort-by-order-alt"></span>
							  <span class="glyphicon-class">glyphicon-sort-by-order-alt</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-sort-by-attributes"></span>
							  <span class="glyphicon-class">glyphicon-sort-by-attributes</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-sort-by-attributes-alt"></span>
							  <span class="glyphicon-class">glyphicon-sort-by-attributes-alt</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-unchecked"></span>
							  <span class="glyphicon-class">glyphicon-unchecked</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-expand"></span>
							  <span class="glyphicon-class">glyphicon-expand</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-collapse-down"></span>
							  <span class="glyphicon-class">glyphicon-collapse-down</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-collapse-up"></span>
							  <span class="glyphicon-class">glyphicon-collapse-up</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-log-in"></span>
							  <span class="glyphicon-class">glyphicon-log-in</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-flash"></span>
							  <span class="glyphicon-class">glyphicon-flash</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-log-out"></span>
							  <span class="glyphicon-class">glyphicon-log-out</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-new-window"></span>
							  <span class="glyphicon-class">glyphicon-new-window</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-record"></span>
							  <span class="glyphicon-class">glyphicon-record</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-save"></span>
							  <span class="glyphicon-class">glyphicon-save</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-open"></span>
							  <span class="glyphicon-class">glyphicon-open</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-saved"></span>
							  <span class="glyphicon-class">glyphicon-saved</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-import"></span>
							  <span class="glyphicon-class">glyphicon-import</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-export"></span>
							  <span class="glyphicon-class">glyphicon-export</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-send"></span>
							  <span class="glyphicon-class">glyphicon-send</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-floppy-disk"></span>
							  <span class="glyphicon-class">glyphicon-floppy-disk</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-floppy-saved"></span>
							  <span class="glyphicon-class">glyphicon-floppy-saved</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-floppy-remove"></span>
							  <span class="glyphicon-class">glyphicon-floppy-remove</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-floppy-save"></span>
							  <span class="glyphicon-class">glyphicon-floppy-save</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-floppy-open"></span>
							  <span class="glyphicon-class">glyphicon-floppy-open</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-credit-card"></span>
							  <span class="glyphicon-class">glyphicon-credit-card</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-transfer"></span>
							  <span class="glyphicon-class">glyphicon-transfer</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-cutlery"></span>
							  <span class="glyphicon-class">glyphicon-cutlery</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-header"></span>
							  <span class="glyphicon-class">glyphicon-header</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-compressed"></span>
							  <span class="glyphicon-class">glyphicon-compressed</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-earphone"></span>
							  <span class="glyphicon-class">glyphicon-earphone</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-phone-alt"></span>
							  <span class="glyphicon-class">glyphicon-phone-alt</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-tower"></span>
							  <span class="glyphicon-class">glyphicon-tower</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-stats"></span>
							  <span class="glyphicon-class">glyphicon-stats</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-sd-video"></span>
							  <span class="glyphicon-class">glyphicon-sd-video</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-hd-video"></span>
							  <span class="glyphicon-class">glyphicon-hd-video</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-subtitles"></span>
							  <span class="glyphicon-class">glyphicon-subtitles</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-sound-stereo"></span>
							  <span class="glyphicon-class">glyphicon-sound-stereo</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-sound-dolby"></span>
							  <span class="glyphicon-class">glyphicon-sound-dolby</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-sound-5-1"></span>
							  <span class="glyphicon-class">glyphicon-sound-5-1</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-sound-6-1"></span>
							  <span class="glyphicon-class">glyphicon-sound-6-1</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-sound-7-1"></span>
							  <span class="glyphicon-class">glyphicon-sound-7-1</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-copyright-mark"></span>
							  <span class="glyphicon-class">glyphicon-copyright-mark</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-registration-mark"></span>
							  <span class="glyphicon-class">glyphicon-registration-mark</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-cloud-download"></span>
							  <span class="glyphicon-class">glyphicon-cloud-download</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-cloud-upload"></span>
							  <span class="glyphicon-class">glyphicon-cloud-upload</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-tree-conifer"></span>
							  <span class="glyphicon-class">glyphicon-tree-conifer</span>
							</li>
						  
							<li>
							  <span class="glyphicon glyphicon-tree-deciduous"></span>
							  <span class="glyphicon-class">glyphicon-tree-deciduous</span>
							</li>
						  
						</ul>
					  </div></td></tr>
			<tr <?php echo ($template -> image_icon == '0')?'style="display:none;"':''; ?>>	
				<td>
					<label for="name">图标颜色</label>
					<input type="text" class="span3" id="iconcolor" name="icon[color]" value="<?php echo !empty($menu_info -> css['icon']['color'])?$menu_info -> css['icon']['color']:'#ffffff'?>" />
					<input class="colorpicker" target="iconcolor" value="<?php echo !empty($menu_info -> css['icon']['color'])?$menu_info -> css['icon']['color']:'#ffffff';?>" />
					<span class="help-block">图标颜色，上传图标时此设置项无效</span>
				</td>
			</tr>
			<tr <?php echo ($template -> image_icon == '0')?'style="display:none;"':''; ?>>	
				<td>
					<label for="name">图标大小</label>
					<input class="span2" type="text" name="icon[size]" id="icon" value="<?php echo !empty($menu_info -> css['icon']['font-size'])?$menu_info -> css['icon']['font-size']:'35'?>"><span class="help-inline">PX</span>
					<span class="help-block">图标的尺寸大小，单位为像素，上传图标时此设置项无效</span>
				</td>
			</tr>
<?php
				echo "<tr><td><label for='name'>页面模板:</label>";
				
				/*如果没有http，证明为内链，加上home_url显示，插入时，判断有再截取入库*/
				$tmp = stristr($menu_info -> url,"http");
				if(($tmp===false)&&(!empty($menu_info -> url))){
					$menuurllink=home_url().$menu_info -> url;
				}else{				
					$menuurllink=$menu_info -> url;
				}
				//$needle=$_SERVER['HTTP_HOST']."/?page_id";
				$needle=home_url()."/?page_id";
				
				//$needle="/?page_id";
				$tmparray=stristr($menuurllink,$needle);
				$isvip="vip_detail";
				$isvipreg="vip_register";	
				$mem=stristr($menuurllink,$isvip);
				$memreg=stristr($menuurllink,$isvipreg);
				$isweschool = "index";
				$schoollink=stristr($menuurllink,$isweschool);
				$isvideo = "videolist";
				$videolink=stristr($menuurllink,$isvideo);
				$ishomework = "homeworklist";
				$homeworklink=stristr($menuurllink,$ishomework);
				$isnotice = "noticelist";
				$noticelink=stristr($menuurllink,$isnotice);
					
				
				//if(count($tmparray)<1){
        if($tmparray){
						//内链有效
						echo "<tr><td><input type='radio' name='menuUrl' value='0' checked='checked' onclick='disableOut()'><span> 内链 </span>";
					?>
						<!--转移到js里<input type="button" class="btn btn-xs btn-primary" onClick="javascript:window.open('../common/post_insert_dialog.php?refreshOpener=<?php echo 'v2yes'?>&beIframe&artType=<?php echo "page" ?>&siteId=<?php echo $siteId?>&menuiUrl=<?php echo $menuurllink ?>','_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')"
						   id="menu" value="页面编辑" style="width:120px;margin: 5px 0 7px 20px;"/>-->
								<input type="button" class="btn btn-xs btn-primary" onClick='ediPost()'
								id="menu" value="页面编辑" style="width:120px;margin: 5px 0 7px 20px;"/>						
								<input type="button" class="btn btn-xs btn-default" onClick="javascript:window.open('../common/page_list.php?beIframe&artType=<?php echo "page" ?>&siteId=<?php echo $siteId?>&menuiUrl=<?php echo $menuurllink ?>','_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')" 
								id="meu" name="meu" value="文章管理" style="display: none"/>
					<?php
								echo "<input type='text' id='Wmenuurl' class='form-control' name='menuiUrl' value='{$menuurllink}' readonly='readonly'/>";
					?>
					<?php
								echo "</div>";
								echo "<div>";
								echo "<input type='radio' name='menuUrl' value='1' onclick='disableIn()'> <span> 外链（请以http://或https://开头） </span>";
								echo "<input type='text' class='form-control' name='menuoUrl' id='menuoUrl' value='' /></td></tr>";
								//echo "</div>";
								echo "<tr><td>";
						?>		
								<!--<div style="margin-bottom:10%; <?php //if( !$wechatactivity_vip ) echo " display:none"; ?>">-->
								<div style="margin-bottom:0%; <?php if( !$wechatactivity_vip ) echo " display:none"; ?>">
								<input type='radio' name='menuUrl' value='4' onclick='disable()'>
								<span>会员注册</span>
								<input style="visibility:hidden" type="text" class="form-control" name="memregUrl" id="memregUrl" value="<?php bloginfo('template_directory'); ?>/wesite/common/vip_register.php" disabled="disabled"/>
								</div>
								<!--<div style="margin-bottom:10%; <?php //if( !$wechatactivity_vip ) echo " display:none"; ?>">-->
								<div style="margin-bottom:0%; <?php if( !$wechatactivity_vip ) echo " display:none"; ?>">
								<input type='radio' name='menuUrl' value='2' onclick='disable()'>
								<span>会员中心</span>
								<input style="visibility:hidden" type="text" class="form-control" name="memUrl" id="memUrl" value="<?php bloginfo('template_directory'); ?>/wesite/common/vip_detail.php" disabled="disabled"/>
								</div>
								
								<!--<div style="margin-bottom:10%; <?php //if( !$wechatschool ) echo " display:none"; ?>">-->
								<div style="margin-bottom:0%; <?php if( !$wechatschool ) echo " display:none"; ?>">
								<input type='radio' name='menuUrl' value='3' onclick='disable()'>
								<span>微学校</span>
								<input style="visibility:hidden" type="text" class="form-control" name="memwsUrl" id="memwsUrl" value="<?php echo home_url(); ?>/mobile.php?module=weSchool&do=index" disabled="disabled"/>
								</div>
								
								</td></tr>
						<?php
						} else{
					//外链或会员中心有效或微视频有效 
							if($mem){
								echo "<br/><input type='radio' name='menuUrl' value='0' onclick='disableOut()'> 内链";
					?>
								<input type="button" class="btn btn-xs btn-primary" onClick="javascript:window.open('../common/post_insert_dialog.php?beIframe&artType=<?php echo "page" ?>&siteId=<?php echo $siteId?>&refreshOpener=<?php echo 'v2yes'?>','_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')"
								id="menu" value="创建新页面" disabled="disabled" style="width:120px;margin: 5px 0 7px 20px;"/>
								<input type="button" class="btn btn-xs btn-default" onClick="javascript:window.open('../common/page_list.php?beIframe&artType=<?php echo "page" ?>&siteId=<?php echo $siteId?>&menuiUrl=<?php echo $menuurllink ?>','_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')" 
								id="meu" name="meu" value="文章管理" style="display: none" disabled="disabled"/>
								<input id="Wmenuurl" class="form-control" type="text" name='menuiUrl' value='' style="margin-top:5px" readonly="readonly"/>
					<?php							
								echo "</div>";
								echo "<div>";
								echo "<input type='radio' name='menuUrl' value='1' onclick='disableIn()'><span> 外链（请以http://或https://开头） </span>";
								echo "<input type='text' class='form-control' id='menuoUrl' name='menuoUrl' disabled='disabled' value=''/></td></tr>";
								echo "<tr><td>";
						?>		
								<!--<div style="margin-bottom:10%; <?php //if( !$wechatactivity_vip ) echo " display:none"; ?>">-->
								<div style="margin-bottom:0%; <?php if( !$wechatactivity_vip ) echo " display:none"; ?>">
						<?php
								echo "<input type='radio' name='menuUrl' value='4' onclick='disable()'>";
								echo "<span>"; 
								echo "会员注册";
								echo "</span>";
								echo "<input style='visibility:hidden' type='text' class='form-control' name='memregUrl' id='memregUrl' value='".get_bloginfo('template_directory')."/wesite/common/vip_register.php' />";
								echo "</div>";
						?>						
								<!--<div style="margin-bottom:10%; <?php //if( !$wechatactivity_vip ) echo " display:none"; ?>">-->
								<div style="margin-bottom:0%; <?php if( !$wechatactivity_vip ) echo " display:none"; ?>">
						<?php
								echo "<input type='radio' name='menuUrl' value='2' checked='checked' onclick='disable()'>";
								echo "<span>"; 
								echo "会员中心";
								echo "</span>";
								echo "<input style='visibility:hidden' type='text' class='form-control' name='memUrl' id='memUrl' value='{$menuurllink}' />";
								echo "</div>";
						?>
								<!--<div style="margin-bottom:10%; <?php //if( !$wechatschool ) echo " display:none"; ?>">-->
								<div style="margin-bottom:0%; <?php if( !$wechatschool ) echo " display:none"; ?>">
						<?php
								echo "<input type='radio' name='menuUrl' value='3' onclick='disable()'>";
								echo "<span>"; 
								echo "微学校";
								echo "</span>";
								echo "<input style='visibility:hidden' type='text' class='form-control' name='memwsUrl' id='memwsUrl' value='".home_url()."/mobile.php?module=weSchool&do=index' />";
								echo "</div>";
						?>
								<!--<div style="margin-bottom:10%; <?php //if( !$wechatschool ) echo " display:none"; ?>">-->
							
						<?php
																		
								echo "</td></tr>";
								}else if($memreg){
								echo "<br/><input type='radio' name='menuUrl' value='0' onclick='disableOut()'> 内链";
					?>
								<input type="button" class="btn btn-xs btn-primary" onClick="javascript:window.open('../common/post_insert_dialog.php?beIframe&artType=<?php echo "page" ?>&siteId=<?php echo $siteId?>&refreshOpener=<?php echo 'v2yes'?>','_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')"
								id="menu" value="创建新页面" disabled="disabled" style="width:120px;margin: 5px 0 7px 20px;"/>
								<input type="button" class="btn btn-xs btn-default" onClick="javascript:window.open('../common/page_list.php?beIframe&artType=<?php echo "page" ?>&siteId=<?php echo $siteId?>&menuiUrl=<?php echo $menuurllink ?>','_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')" 
								id="meu" name="meu" value="文章管理" style="display: none" disabled="disabled"/>
								<input id="Wmenuurl" class="form-control" type="text" name='menuiUrl' value='' style="margin-top:5px" readonly="readonly"/>
					<?php							
								echo "</div>";
								echo "<div>";
								echo "<input type='radio' name='menuUrl' value='1' onclick='disableIn()'><span> 外链（请以http://或https://开头） </span>";
								echo "<input type='text' class='form-control' id='menuoUrl' name='menuoUrl' disabled='disabled' value=''/></td></tr>";
								echo "<tr><td>";
						?>		
								<!--<div style="margin-bottom:10%; <?php //if( !$wechatactivity_vip ) echo " display:none"; ?>">-->
								<div style="margin-bottom:0%; <?php if( !$wechatactivity_vip ) echo " display:none"; ?>">
						<?php
								echo "<input type='radio' name='menuUrl' value='4' checked='checked' onclick='disable()'>";
								echo "<span>"; 
								echo "会员注册";
								echo "</span>";
								echo "<input style='visibility:hidden' type='text' class='form-control' name='memregUrl' id='memregUrl' value='{$menuurllink}' />";
								echo "</div>";
						?>						
								<!--<div style="margin-bottom:10%; <?php //if( !$wechatactivity_vip ) echo " display:none"; ?>">-->
								<div style="margin-bottom:0%; <?php if( !$wechatactivity_vip ) echo " display:none"; ?>">
						<?php
								echo "<input type='radio' name='menuUrl' value='2'  onclick='disable()'>";
								echo "<span>"; 
								echo "会员中心";
								echo "</span>";
								echo "<input style='visibility:hidden' type='text' class='form-control' name='memUrl' id='memUrl' value='".get_bloginfo('template_directory')."/wesite/common/vip_detail.php' />";
								echo "</div>";
						?>
								<!--<div style="margin-bottom:10%; <?php //if( !$wechatschool ) echo " display:none"; ?>">-->
								<div style="margin-bottom:0%; <?php if( !$wechatschool ) echo " display:none"; ?>">
						<?php
								echo "<input type='radio' name='menuUrl' value='3' onclick='disable()'>";
								echo "<span>"; 
								echo "微学校";
								echo "</span>";
								echo "<input style='visibility:hidden' type='text' class='form-control' name='memwsUrl' id='memwsUrl' value='".home_url()."/mobile.php?module=weSchool&do=index' />";
								echo "</div>";
						?>
								<!--<div style="margin-bottom:10%; <?php //if( !$wechatschool ) echo " display:none"; ?>">-->
							
						<?php
																		
								echo "</td></tr>";
								}else if($schoollink){  
								echo "<br/><input type='radio' name='menuUrl' value='0' onclick='disableOut()'> 内链";
					?>
								<input type="button" class="btn btn-xs btn-primary" onClick="javascript:window.open('../common/post_insert_dialog.php?beIframe&artType=<?php echo "page" ?>&siteId=<?php echo $siteId?>&refreshOpener=<?php echo 'v2yes'?>','_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')"
								id="menu" value="创建新页面" disabled="disabled" style="width:120px;margin: 5px 0 7px 20px;"/>
								<input type="button" class="btn btn-xs btn-default" onClick="javascript:window.open('../common/page_list.php?beIframe&artType=<?php echo "page" ?>&siteId=<?php echo $siteId?>&menuiUrl=<?php echo $menuurllink ?>','_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')" 
								id="meu" name="meu" value="文章管理" style="display: none" disabled="disabled"/>
								<input id="Wmenuurl" class="form-control" type="text" name='menuiUrl' value='' style="margin-top:5px" readonly="readonly"/>
					<?php							
								echo "</div>";
								echo "<div>";
								echo "<input type='radio' name='menuUrl' value='1' onclick='disableIn()'><span> 外链（请以http://或https://开头） </span>";
								echo "<input type='text' class='form-control' id='menuoUrl' name='menuoUrl' disabled='disabled' value=''/></td></tr>";
								echo "<tr><td>";
						?>		
								<!--<div style="margin-bottom:10%; <?php //if( !$wechatactivity_vip ) echo " display:none"; ?>">-->
								<div style="margin-bottom:0%; <?php if( !$wechatactivity_vip ) echo " display:none"; ?>">
						<?php
								echo "<input type='radio' name='menuUrl' value='4' onclick='disable()'>";
								echo "<span>"; 
								echo "会员注册";
								echo "</span>";
								echo "<input style='visibility:hidden' type='text' class='form-control' name='memregUrl' id='memregUrl' value='".get_bloginfo('template_directory')."/wesite/common/vip_register.php' />";
								echo "</div>";
						?>
								<!--<div style="margin-bottom:10%; <?php //if( !$wechatactivity_vip ) echo " display:none"; ?>">-->
								<div style="margin-bottom:0%; <?php if( !$wechatactivity_vip ) echo " display:none"; ?>">
						<?php
								echo "<input type='radio' name='menuUrl' value='2' onclick='disable()'>";
								echo "<span>"; 
								echo "会员中心";
								echo "</span>";
								echo "<input style='visibility:hidden' type='text' class='form-control' name='memUrl' id='memUrl' value='".get_bloginfo('template_directory')."/wesite/common/vip_detail.php' />";
								echo "</div>";
						?>
								<!--<div style="margin-bottom:10%; <?php //if( !$wechatschool ) echo " display:none"; ?>">-->
								<div style="margin-bottom:0%; <?php if( !$wechatschool ) echo " display:none"; ?>">
						<?php
								echo "<input type='radio' name='menuUrl' value='3' checked='checked' onclick='disable()'>";
								echo "<span>"; 
								echo "微学校";
								echo "</span>";
								echo "<input style='visibility:hidden' type='text' class='form-control' name='memwsUrl' id='memwsUrl' value='{$menuurllink}' />";
								echo "</div>";
							?>
								<!--<div style="margin-bottom:10%; <?php //if( !$wechatschool ) echo " display:none"; ?>">-->
									
						<?php		
								echo "</td></tr>";
								}
								else{
								//外链有效
								echo "<br/><input type='radio' name='menuUrl' value='0' onclick='disableOut()'> 内链";
				?>
							<input type="button" class="btn btn-xs btn-primary" onClick="javascript:window.open('../common/post_insert_dialog.php?beIframe&artType=<?php echo "page" ?>&siteId=<?php echo $siteId?>&refreshOpener=<?php echo 'v2yes'?>','_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')"
								id="menu" value="创建新页面" disabled="disabled" style="width:120px;margin: 5px 0 7px 20px;"/>
							<input type="button" class="btn btn-xs btn-default" onClick="javascript:window.open('../common/page_list.php?beIframe&artType=<?php echo "page" ?>&siteId=<?php echo $siteId?>&menuiUrl=<?php echo $menuurllink ?>','_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')" 
								id="meu" name="meu" value="文章管理" style="display: none" disabled="disabled"/>
							<input id="Wmenuurl" class="form-control" type="text" name='menuiUrl' value='' style="margin-top:5px" readonly="readonly"/>
						<?php							
							echo "</div>";
							echo "<div>";
							echo "<input type='radio' name='menuUrl' value='1' checked='checked' onclick='disableIn()'><span> 外链（请以http://或https://开头） </span>";
							echo "<input type='text' class='form-control' id='menuoUrl' name='menuoUrl' value='{$menuurllink}'/></td></tr>";
							echo "<tr><td>";
							?>
							<!--<div style="margin-bottom:10%; <?php //if( !$wechatactivity_vip ) echo " display:none"; ?>">-->
							<div style="margin-bottom:0%; <?php if( !$wechatactivity_vip ) echo " display:none"; ?>">
							<input type='radio' name='menuUrl' value='4' onclick='disable()'>
							<span>会员注册</span>
							<input style="visibility:hidden" type="text" class="form-control" name="memregUrl" id="memregUrl" value="<?php bloginfo('template_directory'); ?>/wesite/common/vip_register.php" disabled="disabled"/>
							</div>
							<!--<div style="margin-bottom:10%; <?php //if( !$wechatactivity_vip ) echo " display:none"; ?>">-->
							<div style="margin-bottom:0%; <?php if( !$wechatactivity_vip ) echo " display:none"; ?>">
							<input type='radio' name='menuUrl' value='2' onclick='disable()'>
							<span>会员中心</span>
							<input style="visibility:hidden" type="text" class="form-control" name="memUrl" id="memUrl" value="<?php bloginfo('template_directory'); ?>/wesite/common/vip_detail.php" disabled="disabled"/>
							</div>
							
							<!--<div style="margin-bottom:10%; <?php //if( !$wechatschool ) echo " display:none"; ?>">-->
							<div style="margin-bottom:0%; <?php if( !$wechatschool ) echo " display:none"; ?>">
							<input type='radio' name='menuUrl' value='3' onclick='disable()'>
							<span>微学校</span>
							<input style="visibility:hidden" type="text" class="form-control" name="memwsUrl" id="memwsUrl" value="<?php echo home_url(); ?>/mobile.php?module=weSchool&do=index" disabled="disabled"/>
							</div>
							
							</td></tr>
						<?php
								}
							} ?>
				
			<?php 
			echo "<input name='menuid' type='hidden' id='menu_id' value='{$menu_info->id}' maxlength='50' />  ";
			echo "<input name='delimgid' type='hidden' id='delimg_id' value='' maxlength='50' />  ";	
		}	
					?>
		<tr><td>		
		<div width="150" align="right">
			<input type="submit" class="btn btn-primary" value="更新" style="width:120px; margin-top:30px;"/>
			<input type="cancel" class="btn btn-default" value="取消" onclick="close2()" style="width:120px; margin-top:30px;"/>			
		</div>	
		</tr></td>
		</table>
	</form>
  </div>
</body>
		
<script language='javascript'>
	var xmlHttp;
    function createXMLHttpRequest(){
    if(window.ActiveXObject)
     xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
    else if(window.XMLHttpRequest)
     xmlHttp = new XMLHttpRequest();
    }
	
	function ediPost(){	
		var url=document.getElementById("Wmenuurl").value;
		window.open('../common/post_insert_dialog.php?refreshOpener=<?php echo 'v2yes'?>&beIframe&artType=<?php echo "page" ?>&siteId=<?php echo $siteId?>&menuiUrl='+url,'_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')
	}
	
	//图片预览
	function previewImage(file){  	
		var picsrc = document.getElementById('pic');  	  
		$('#icon').attr('class','');
		$(':input[name="icon_class"]').val('');
		if (file.files && file.files[0]) {//chrome   
			var reader = new FileReader();
			reader.readAsDataURL(file.files[0]);  
			reader.onload = function(ev){
			picsrc.src = ev.target.result;
			$("#pic").show();
			$("#picurl").hide();
			}   
		
		}else{
			//IE下，使用滤镜 出现问题
			picsrc.style.maxwidth="50px";
			picsrc.style.maxheight = "12px";
			picsrc.style.overflow="hidden";
			var picUpload = document.getElementById('file'); 
			picUpload.select();
			var imgSrc = document.selection.createRange().text;  
			picsrc.filters.item("DXImageTransform.Microsoft.AlphaImageLoader").src = imgSrc;
			picsrc.style.filter="progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale,src=\""+imgSrc+"\")";
			
		}                    
	}  
	
	function delImage(){	  
		$("#pic").hide();
		$("#picurl").hide();
		document.getElementById("delimg_id").value=-1;
	}
	//更新时，查看内容是否为空
	function checknull(obj, warning){
		if (obj.value == "") {
			alert(warning);
			obj.focus();
			return true;
		}
		return false;
	}

	function validateform()
	{
	  //if (checknull(document.content.name, "请填写菜单按钮名称!") == true) {
		//return false;
	  //}
	  return true; 
	}
	
	//disable/enable相应的内链或外链
	function disableOut() {
		document.getElementById("menuoUrl").disabled=true;
		document.getElementById("Wmenuurl").disabled=false;
		document.getElementById("meu").disabled=false;
		document.getElementById("menu").disabled=false;
		document.getElementById("memUrl").disabled=false;
		document.getElementById("memregUrl").disabled=false;
		document.getElementById("memwsUrl").disabled=false;
	}
	function disableIn() {
		document.getElementById("menuoUrl").disabled=false;
		document.getElementById("Wmenuurl").disabled=true;
		document.getElementById("meu").disabled=true;
		document.getElementById("menu").disabled=true;
		document.getElementById("memUrl").disabled=false;
		document.getElementById("memregUrl").disabled=false;
		document.getElementById("memwsUrl").disabled=false;
	}
	function disable() {
		document.getElementById("menuoUrl").disabled=true;
		document.getElementById("Wmenuurl").disabled=true;
		document.getElementById("meu").disabled=true;
		document.getElementById("menu").disabled=true;
		document.getElementById("memUrl").disabled=false;
		document.getElementById("memregUrl").disabled=false;
		document.getElementById("memwsUrl").disabled=false;
	}
	
	// function closeit() {
		// top.resizeTo(300, 200); //控制网页显示的大小		
		// setTimeout("self.close()", 5000); //毫秒
		// opener.location.reload();  //主页面刷新显示
	// }
    
		function close2(){
		window.opener=null;
		setTimeout("self.close()",0);
	}
	window.resizeTo(850,550);
	$('.bs-glyphicons-list > li').click(function(){
		console.info($(this).find('.glyphicon-class').text());
		$('#icon').attr('class','glyphicon ' + $(this).find('.glyphicon-class').text());
		$(':input[name="icon_class"]').val($(this).find('.glyphicon-class').text());
		$('#file').val('');
		$('#pic').attr('src','#');
		$("#pic").hide();
		$("#picurl").hide();
	});
 	$(function(){ 	
 	colorpicker(); 	
 	}); 	
	function colorpicker() {
		$(".colorpicker:visible").spectrum({
			className : 'colorpicker',
			showInput: true,
			showInitial: true,
			showPalette: true,
			maxPaletteSize: 10,
			preferredFormat: "hex",
			change: function(color) {
				$('#' + $(this).attr('target')).val(color.toHexString());
			},
			palette: [
				["rgb(0, 0, 0)", "rgb(67, 67, 67)", "rgb(102, 102, 102)", "rgb(153, 153, 153)","rgb(183, 183, 183)",
				"rgb(204, 204, 204)", "rgb(217, 217, 217)","rgb(239, 239, 239)", "rgb(243, 243, 243)", "rgb(255, 255, 255)"],
				["rgb(152, 0, 0)", "rgb(255, 0, 0)", "rgb(255, 153, 0)", "rgb(255, 255, 0)", "rgb(0, 255, 0)",
				"rgb(0, 255, 255)", "rgb(74, 134, 232)", "rgb(0, 0, 255)", "rgb(153, 0, 255)", "rgb(255, 0, 255)"],
				["rgb(230, 184, 175)", "rgb(244, 204, 204)", "rgb(252, 229, 205)", "rgb(255, 242, 204)", "rgb(217, 234, 211)",
				"rgb(208, 224, 227)", "rgb(201, 218, 248)", "rgb(207, 226, 243)", "rgb(217, 210, 233)", "rgb(234, 209, 220)",
				"rgb(221, 126, 107)", "rgb(234, 153, 153)", "rgb(249, 203, 156)", "rgb(255, 229, 153)", "rgb(182, 215, 168)",
				"rgb(162, 196, 201)", "rgb(164, 194, 244)", "rgb(159, 197, 232)", "rgb(180, 167, 214)", "rgb(213, 166, 189)",
				"rgb(204, 65, 37)", "rgb(224, 102, 102)", "rgb(246, 178, 107)", "rgb(255, 217, 102)", "rgb(147, 196, 125)",
				"rgb(118, 165, 175)", "rgb(109, 158, 235)", "rgb(111, 168, 220)", "rgb(142, 124, 195)", "rgb(194, 123, 160)",
				"rgb(166, 28, 0)", "rgb(204, 0, 0)", "rgb(230, 145, 56)", "rgb(241, 194, 50)", "rgb(106, 168, 79)",
				"rgb(69, 129, 142)", "rgb(60, 120, 216)", "rgb(61, 133, 198)", "rgb(103, 78, 167)", "rgb(166, 77, 121)",
				"rgb(133, 32, 12)", "rgb(153, 0, 0)", "rgb(180, 95, 6)", "rgb(191, 144, 0)", "rgb(56, 118, 29)",
				"rgb(19, 79, 92)", "rgb(17, 85, 204)", "rgb(11, 83, 148)", "rgb(53, 28, 117)", "rgb(116, 27, 71)",
				"rgb(91, 15, 0)", "rgb(102, 0, 0)", "rgb(120, 63, 4)", "rgb(127, 96, 0)", "rgb(39, 78, 19)",
				"rgb(12, 52, 61)", "rgb(28, 69, 135)", "rgb(7, 55, 99)", "rgb(32, 18, 77)", "rgb(76, 17, 48)"]
			]
		});

	}
</script>
</html>
