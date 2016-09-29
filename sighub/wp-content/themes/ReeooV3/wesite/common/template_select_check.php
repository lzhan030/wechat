<?php
@session_start(); 

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
//var_dump($_GET['beIframe']);
global  $current_user, $wpdb;
if( !isset($current_user->user_login)|| empty($current_user->user_login)){
		wp_redirect(wp_login_url());
}
get_header(); ?>

<script type="text/javascript">
<?php  
	include 'dbaccessor.php';
	include 'web_constant.php';

	//当前用户是否分组管理员
	$groupadminflag = web_admin_issuperadmin($current_user->ID);
	//$currentuser= (is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
	$currentuser=((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($groupadminflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
	
	//2014-07-15新增修改，获取session（gweid）
	$gweid = $_SESSION['GWEID'];
	
	//php?site_name 这样获取参数值
	$site_name=$_REQUEST["site_name"];
	$templateSelected=$_POST['templateSelected'];
	if($site_name==null){
?>
		alert("pleas input the site name");
<?php
     
	}else{
		
		//$site_id=web_admin_create_site($site_name, $templateSelected > 0 && $templateSelected <=6 ?($templateSelected ): 7,$currentuser);
		
		//2014-07-15新增修改
		$site_id=web_admin_create_site($site_name, in_array($templateSelected,array(1,2,3,4,5,6,8,9))?($templateSelected ): 7,$currentuser, $gweid);
		
		//$next_url = get_theme_root_uri()."/silver-blue/";
		//$next_url =get_template_directory_uri()."/";
		$next_url=constant("CONF_THEME_DIR"); 
?>
		var theme_key="<?php echo $templateSelected ?>";
		var url="<?php echo $next_url ?>";
		var site_id="<?php echo $site_id ?>";
		var MobileTheme="Mobile Theme";
		
		if(theme_key == 3){
			location.href=url+'/wesite/mobilepagev3/custom_made.php?beIframe&siteId='+site_id;
		}
		else if(theme_key == 2){		
			location.href=url+'/wesite/mobilepagev2/custom_made.php?beIframe&siteId='+site_id;
		}
		else if(theme_key == 1){		
			location.href=url+'/wesite/mobiletheme/custom_made.php?beIframe&siteId='+site_id;
		}
		else if(theme_key == 4){	
			location.href=url+'/wesite/videotheme/custom_made.php?beIframe&siteId='+site_id;
		}
		else if(theme_key == 5){	
			location.href=url+'/wesite/mobiletheme5/custom_made.php?beIframe&siteId='+site_id;
		}
		else if(theme_key == 8){	
			location.href=url+'/wesite/mobilethemesyncup/custom_made.php?beIframe&siteId='+site_id;
		}
		else if(theme_key == 9){	
			location.href=url+'/wesite/mobilevideotheme/custom_made.php?beIframe&siteId='+site_id;
		}
		else {	
			location.href=url+'/wesite/mobilepagewe7/custom_made.php?beIframe&siteId='+site_id+'&we7templateSelected='+theme_key;
		}
		window.parent.scrollTo(0, 0);
		//window.parent.parent.scroll(0, 0);
<?php } ?>	
</script>

<?php get_sidebar( 'front' ); ?>
<?php get_footer(); ?>
