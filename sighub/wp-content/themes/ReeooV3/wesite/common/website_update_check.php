<?php
@session_start(); 

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');

get_header(); ?>


<script type="text/javascript">

	<?php  
		include 'dbaccessor.php';
		include 'web_constant.php';
		$siteId=$_GET["siteId"];	
		//拿到siteId对应的主题theme

		$site=web_admin_get_site($siteId);
		foreach($site as $site_info){
			$themeKey=$site_info->themes_key;
		}
		$sitewe7=web_admin_get_sitewe7($siteId);
		foreach($sitewe7 as $site_in){
			$thKey=$site_in->site_value;
		}
		//$next_url = get_theme_root_uri()."/silver-blue/";
		//$next_url =get_template_directory_uri()."/";
		$next_url =constant("CONF_THEME_DIR"); 
	?>
		var th_Key="<?php echo $thKey ?>";
		var theme_key="<?php echo $themeKey ?>";
		var url="<?php echo $next_url ?>";
		var site_id="<?php echo $siteId ?>";

		if(theme_key==3) {
			location.href=url+'/wesite/mobilepagev3/custom_made.php?beIframe'+'<?php if(isset($_GET['isupdate'])){echo "&isupdate";} ?>'+'&siteId='+site_id;
		}
		else if(theme_key==2) {		
			location.href=url+'/wesite/mobilepagev2/custom_made.php?beIframe'+'<?php if(isset($_GET['isupdate'])){echo "&isupdate";} ?>'+'&siteId='+site_id;
		}
		else if(theme_key==4) {		
			location.href=url+'/wesite/videotheme/custom_made.php?beIframe'+'<?php if(isset($_GET['isupdate'])){echo "&isupdate";} ?>'+'&siteId='+site_id;
		}
		else if (theme_key==1){
			location.href=url+'/wesite/mobiletheme/custom_made.php?beIframe'+'<?php if(isset($_GET['isupdate'])){echo "&isupdate";} ?>'+'&siteId='+site_id;
		}
		else if (theme_key==5){
		   location.href=url+'/wesite/mobiletheme5/custom_made.php?beIframe'+'<?php if(isset($_GET['isupdate'])){echo "&isupdate";} ?>'+'&siteId='+site_id;
		}
		else if (theme_key==8){
		   location.href=url+'/wesite/mobilethemesyncup/custom_made.php?beIframe'+'<?php if(isset($_GET['isupdate'])){echo "&isupdate";} ?>'+'&siteId='+site_id;
		}		
		else if (theme_key==9){
		   location.href=url+'/wesite/mobilevideotheme/custom_made.php?beIframe'+'<?php if(isset($_GET['isupdate'])){echo "&isupdate";} ?>'+'&siteId='+site_id;
		}
		else {
		   location.href=url+'/wesite/mobilepagewe7/custom_made.php?beIframe'+'<?php if(isset($_GET['isupdate'])){echo "&isupdate";} ?>'+'&siteId='+site_id+'&we7templateSelected='+th_Key;
		}
</script>
	
<?php get_sidebar( 'front' ); ?>
<?php get_footer(); ?>
