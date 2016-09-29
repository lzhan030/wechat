<?php
@session_start(); 
/*
Template Name:web_manage_website_create_template
*/

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
include 'web_constant.php';
global  $current_user;
if( !isset($current_user->user_login)|| empty($current_user->user_login)){
		wp_redirect(wp_login_url());
}	
get_header(); ?>

<!--判断填写内容是否为空-->
<script language="javascript">
	function checknull(obj, warning)
	{
	  if (obj.value == "") {
		alert(warning);
		obj.focus();
		return true;
	  }
	return false;
	}

	function validateform()
	{
	  if (checknull(document.content.name, "请填写站点名称!") == true) {
		return false;
	  }
	  return true; 
	}
</script>

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css" />
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
		<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js"></script>
		<script src="<?php bloginfo('template_directory'); ?>/js/bootstrap.min.js"></script>
		<title>站点建立</title>
	</head>
	
	<body>	
		<div id="primary" class="site-content">
			<div id="content" role="main">
				<form role="form" name = "content" onSubmit="return validateform()" action="<?php echo constant("CONF_THEME_DIR");  ?>/wesite/common/template_select.php?beIframe" method="post" > 
					<div>
						<div class="main-title">
							<div class="title-1">当前位置：微官网 > <font class="fontpurple">创建新站点第一步：基本信息> </font>
							</div>
						</div>
						<div class="bgimg"></div>
						<div>
							<table width="450" height="300" border="0" style=" margin-left:150px; margin-top:30px;">
								<tr>
									<td width="150">
										<b><font color="red">* </font>请输入站点信息:</b>
									</td>
									<td></td>
								</tr>
								<tr>
									<td><label for="name">&nbsp&nbsp站点名称: </label></td>
									<td><input type="text" class="form-control" id="name" name="site_name"/></td>						
								</tr>					
								<tr>
									<td>
										<input type="submit" class="btn btn-primary" value="下一步" style="width:120px" />	
									</td>
								</tr>										
							</table>
						</div>
					</div>
				</form>
			</div>
		</div>
	</body>
</html>

<?php get_sidebar( 'front' ); ?>
<?php get_footer(); ?>
