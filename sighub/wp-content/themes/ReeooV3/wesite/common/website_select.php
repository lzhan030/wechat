<?php
@session_start(); 

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');

get_header(); ?>


<?php  
	include 'dbaccessor.php';
	include 'web_constant.php';
	$gweid =  $_SESSION['GWEID'];
	$siteId=$_GET["siteId"];	

	$site=web_admin_get_site($siteId);
	//get user by the siteid
	foreach($site as $site_info){
		$themeKey=$site_info->themes_key;
		$siteUrl=$site_info->site_url;
	}
	
	$gweid_weids=web_admin_usechat_info_group($gweid);
	

?>	

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="pingback" href="<?php bloginfo('stylesheet_url'); ?>" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/table1.css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js"></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css" />
	<title>΢ڙθtޓ</title>
</head>
<div id="primary" class="site-content">
	<div id="content" role="main">
		<form action="<?php echo constant("CONF_THEME_DIR"); ?>/wesite/common/website_create.php?beIframe" method="post" >
			<div>
				<div class="main-title">
					<div class="title-1">ձǰλ׃ú΢ڙθ > <font class="fontpurple">΢ڙθtޓ> </font>
					</div>
				</div>
				<div class="bgimg"></div>
				<div class="submenu">
						<div class="panel panel-default" style="margin-right:50px; margin-top:30px">
						<!-- Default panel contents class="gridtable"-->
						
						<table class="table table-striped" width="800"  border="1" align="center">
							<?php
							foreach($gweid_weids as $weids){	
								$WEID=$weids->WEID;
								$wid=$weids->wid;		
								$wechatsinfo=web_admin_wechats_info($wid);
								foreach($wechatsinfo as $wsinfo){
									$wnikename=$wsinfo->wechat_nikename;
									echo"<tr>";
									echo"<td>΢х뇳Ƽ/td>";
									echo"<td>$wnikename</td>";
									echo"<tr>";	
								}
								$siteurl=home_url().$siteUrl."&WEID=".$WEID;
									echo "<tr>";
									echo"<td>הӦtޓ</td>";									
									echo "<td>$siteurl </td>";
									echo "</tr>";
							}?>
														
												
							<?php   
									
							?>
						</table>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>

</html>
	
<?php get_sidebar( 'front' ); ?>
<?php get_footer(); ?>
