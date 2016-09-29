<?php
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
global $current_user, $wpdb;
//判断是否是分组管理员下的用户
$getgroupuserids = $wpdb->get_results( "SELECT group_id, flag FROM {$wpdb -> prefix}user_group where user_id = ".$current_user->ID);
if(!empty($getgroupuserids)){
	foreach($getgroupuserids as $getgroupinfo)
	{
	    $usergroupid = $getgroupinfo -> group_id;
	    $usergroupflag = $getgroupinfo -> flag;
	}
}else{  //分组里没有记录，则属于默认分组，groupid为0，对应的flag为0
	$usergroupid = 0;
	$usergroupflag = 0;
}

$user_id = ((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($usergroupid !=0 && $usergroupflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
//$user_id = (is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;


include '../common/web_constant.php';
$siteId = intval($_GET["siteId"]);
if(empty($we7templateSelected))
$we7templateSelected=$_GET["we7templateSelected"];
	$we7templateSelected = $wpdb -> get_var("SELECT site_value FROM {$wpdb -> prefix}orangesitemeta WHERE site_id='{$siteId}' AND site_key = 'we7templatestyle'");
$template = $wpdb -> get_row($wpdb -> prepare("SELECT * FROM {$wpdb -> prefix}site_templates WHERE name=%s",$we7templateSelected));
if(isset($_POST['submit'])){
	$wpdb -> replace($wpdb -> prefix.'site_styles',array(
			'site_id' => $siteId,
			'templateid' => $we7templateSelected,
			'variable' => 'indexbgcolor',
			'content' => $_POST['indexbgcolor']
		)) ;
	$wpdb -> replace($wpdb -> prefix.'site_styles',array(
		'site_id' => $siteId,
		'templateid' => $we7templateSelected,
		'variable' => 'fontsize',
		'content' => '14px'
	));
	$wpdb -> replace($wpdb -> prefix.'site_styles',array(
		'site_id' => $siteId,
		'templateid' => $we7templateSelected,
		'variable' => 'fontcolor',
		'content' => $_POST['fontcolor']
	));
	$wpdb -> replace($wpdb -> prefix.'site_styles',array(
		'site_id' => $siteId,
		'templateid' => $we7templateSelected,
		'variable' => 'fontnavcolor',
		'content' => $_POST['fontnavcolor']
	));
	$wpdb -> replace($wpdb -> prefix.'site_styles',array(
		'site_id' => $siteId,
		'templateid' => $we7templateSelected,
		'variable' => 'linkcolor',
		'content' => $_POST['linkcolor']
	));
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
		<title>更新风格设置</title>
		<br>
		更新成功
		<script language='javascript'>
				top.resizeTo(300, 200); //控制网页显示的大小		
				setTimeout("self.close()", 3000); //毫秒
				opener.location.reload();  //主页面刷新显示
		</script>
	</head>
</html>
	<?php
	exit;
}
$styles_indb = $wpdb -> get_results($wpdb -> prepare("SELECT variable,content FROM {$wpdb -> prefix}site_styles WHERE site_id = '{$siteId}' AND templateid = %s",$we7templateSelected));
$styles = array();
foreach($styles_indb as $style_indb){
	$styles[$style_indb -> variable] = $style_indb -> content;
}
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
	<title>更新风格设置</title>
</head>
<body>
	<div class="panel-body" style=" text-align: center;">
		<h4 style="margin: 20px 70px;text-align: left;">基本风格设置：</h4>
		<form name ="content" action="" method="post" enctype="multipart/form-data">
			<table width="600" bordercolor=#06c border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:30px;">
				<div class='form-group'>
					首页背景颜色：
					<input type="text" class="span3" id="indexbgcolor" name="indexbgcolor" value="<?php echo !empty($styles['indexbgcolor'])?$styles['indexbgcolor']:'#ffffff'?>" />
					<input class="colorpicker" target="indexbgcolor" value="<?php echo !empty($styles['indexbgcolor'])?$styles['indexbgcolor']:'#ffffff'?>" />
				</div>
				<div class='form-group'>
					普通文本颜色：
					<input type="text" class="span3" id="fontcolor" name="fontcolor" value="<?php echo !empty($styles['fontcolor'])?$styles['fontcolor']:'#2a6496'?>" />
					<input class="colorpicker" target="fontcolor" value="<?php echo !empty($styles['fontcolor'])?$styles['fontcolor']:'#2a6496'?>" />
				</div>
				<div class='form-group'>
					菜单文本颜色：
					<input type="text" class="span3" id="fontnavcolor" name="fontnavcolor" value="<?php echo !empty($styles['fontnavcolor'])?$styles['fontnavcolor']:'#2a6496'?>" />
					<input class="colorpicker" target="fontnavcolor" value="<?php echo !empty($styles['fontnavcolor'])?$styles['fontnavcolor']:'#2a6496'?>" />
				</div>
				<div class='form-group'>
					链接文字颜色：
					<input type="text" class="span3" id="linkcolor" name="linkcolor" value="<?php echo !empty($styles['linkcolor'])?$styles['linkcolor']:'#2a6496'?>" />
					<input class="colorpicker" target="linkcolor" value="<?php echo !empty($styles['linkcolor'])?$styles['linkcolor']:'#2a6496'?>" />
				</div>
				<input type="submit" class="btn btn-primary" name="submit" style="margin:10px 0px 0px 0px; width: 120px" value="完成" />
			</table>
		</form>
	  </div>
	</body>
	<script language='javascript'>
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
