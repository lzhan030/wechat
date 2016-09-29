<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=3.0, user-scalable=yes">  
<title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?></title>
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/style.css" type="text/css" />
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/themedesigns/<?php echo get_option('themecolor'); ?>/style.css" type="text/css" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>
<?php wp_enqueue_script('jquery'); ?>
<?php wp_head(); ?>
<script type="text/javascript">
var $ = jQuery.noConflict();
	$(function() {
		$('#activator').click(function(){
				$('#box').animate({'top':'65px'},500);
		});
		$('#boxclose').click(function(){
				$('#box').animate({'top':'-400px'},500);
		});
		$('#activator_share').click(function(){
				$('#box_share').animate({'top':'65px'},500);
		});
		$('#boxclose_share').click(function(){
				$('#box_share').animate({'top':'-400px'},500);
		});

	});
	$(document).ready(function(){
	$(".toggle_container").hide(); 
	$(".trigger").click(function(){
		$(this).toggleClass("active").next().slideToggle("slow");
		return false;
	});
	
	});
</script>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/photoslide.js"></script>
</head>
<?php
/* global $wpdb, $table_prefix;
$tableName = $table_prefix.'orangesitemeta';
$siteId = ($_GET['site'] == ''||$_GET['site']==null)? '1': $_GET['site'];
$keyName = 'firstPageBackgroup';
$sql = "SELECT `site_value` FROM `".$tableName."` WHERE `site_id`='".$siteId."' and `site_key`='".$keyName."'";
$sitemeta = $wpdb->get_row($sql);
$backgroup = $sitemeta->site_value;
if((!empty($backgroup ))&&(stristr($backgroup,"http")===false)){
	$upload =wp_upload_dir();
	$backgroup=$upload['baseurl'].$backgroup;
}
if(empty($backgroup)){
	$backgroup=home_url()."/wp-content/themes/ReeooV3/images/bac_image.jpg";
} */
?>
<!--<body background="<?php echo $backgroup;?>">-->
<body>
<div id="main_container">

        <div class="box" id="box">
        	<div class="box_content">
            
            	<div class="box_content_tab">
                Search
                </div>
                
                <div class="box_content_center">
                <div class="form_content">
                <form method="get" id="searchform" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="text" class="form_input_box" value="<?php the_search_query(); ?>" name="s" id="s"/>
                <a class="boxclose" id="boxclose">Close</a>
                <input type="submit" class="form_submit" id="searchsubmit" value="Submit"/>
                </form>
                </div> 
                
                <div class="clear"></div>
                </div>
            
           </div>
        </div>

        <div class="box" id="box_share">
        	<div class="box_content">
            	<div class="box_content_tab">
                Social Share
                </div>
                <div class="box_content_center">
                
                        <div class="social_share">
                        <ul>    
                        
				<?php if (get_option('rss') != '') { ?>
                <li><a href="http://www.adultplaythings.com/categories/Bondage-Fetish-%26-Kink/"><img src="<?php bloginfo('template_directory'); ?>/images/social/bondage-toys-and-fetish-kink.png" alt="Bondage Toys" title="Bondage" border="0" /></a></li> 
                <? } else { }?>
				<?php if (get_option('google') != '') { ?>
                <li><a href="http://www.adultplaythings.com/categories/Bondage-Fetish-%26-Kink/"><img src="<?php bloginfo('template_directory'); ?>/images/social/google.png" alt="Bondage Toys" title="Bondage" border="0" /></a></li>
                <? } else { }?>
				<?php if (get_option('twitter') != '') { ?>
                <li><a href="http://www.adultplaythings.com/categories/Bondage-Fetish-%26-Kink/"><img src="<?php bloginfo('template_directory'); ?>/images/social/twitter.png" alt="Bondage Toys" title="Bondage" border="0" /></a></li>
                <? } else { }?>
				<?php if (get_option('delicious') != '') { ?>
                <li><a href="http://www.adultplaythings.com/categories/Bondage-Fetish-%26-Kink/"><img src="<?php bloginfo('template_directory'); ?>/images/social/delicious.png" alt="Bondage Toys" title="Bondage" border="0" /></a></li>
                <? } else { }?>
				<?php if (get_option('digg') != '') { ?>
                <li><a href="http://www.adultplaythings.com/categories/Bondage-Fetish-%26-Kink/"><img src="<?php bloginfo('template_directory'); ?>/images/social/digg.png" alt="Bondage Toys" title="Bondage" border="0" /></a></li>
                <? } else { }?>
				<?php if (get_option('linkedin') != '') { ?>
                <li><a href="http://www.adultplaythings.com/categories/Bondage-Fetish-%26-Kink/"><img src="<?php bloginfo('template_directory'); ?>/images/social/linkedin.png" alt="Bondage Toys" title="Bondage" border="0" /></a></li>
                <? } else { }?>
				<?php if (get_option('facebook') != '') { ?>
                <li><a href="http://www.adultplaythings.com/categories/Bondage-Fetish-%26-Kink/"><img src="<?php bloginfo('template_directory'); ?>/images/social/bondage-toys-and-fetish.png" alt="Bondage Toys" title="Bondage" border="0" /></a></li>
                <? } else { }?>
				<?php if (get_option('reddit') != '') { ?>
                <li><a href="http://www.adultplaythings.com/categories/Bondage-Fetish-%26-Kink/"><img src="<?php bloginfo('template_directory'); ?>/images/social/reddit.png" alt="Bondage Toys" title="Bondage" border="0" /></a></li>
                <? } else { }?>
				<?php if (get_option('myspace') != '') { ?>
                <li><a href="http://www.adultplaythings.com/categories/Bondage-Fetish-%26-Kink/"><img src="<?php bloginfo('template_directory'); ?>/images/social/myspace.png" alt="Bondage Toys" title="Bondage" border="0" /></a></li>
                <? } else { }?>
				<?php if (get_option('technorati') != '') { ?>
                <li><a href="http://www.adultplaythings.com/categories/Bondage-Fetish-%26-Kink/"><img src="<?php bloginfo('template_directory'); ?>/images/social/technorati.png" alt="Bondage Toys" title="Bondage" border="0" /></a></li>
                <? } else { }?>
				<?php if (get_option('stumbleupon') != '') { ?>
                <li><a href="http://www.adultplaythings.com/categories/Bondage-Fetish-%26-Kink/"><img src="<?php bloginfo('template_directory'); ?>/images/social/stumbleupon.png" alt="Bondage Toys" title="Bondage" border="0" /></a></li>
                <? } else { }?>
				<?php if (get_option('flickr') != '') { ?>
                <li><a href="http://www.adultplaythings.com/categories/Bondage-Fetish-%26-Kink/"><img src="<?php bloginfo('template_directory'); ?>/images/social/flickr.png" alt="Bondage Toys" title="Bondage" border="0" /></a></li>
                <? } else { }?>
                        </ul>
                        </div>
            
                <a class="boxclose_right" id="boxclose_share">close</a>
                <div class="clear"></div>
                
                </div>
           </div>
        </div>