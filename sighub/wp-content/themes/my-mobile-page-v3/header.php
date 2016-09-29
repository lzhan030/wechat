<?php	 	
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
?>
<?php	 	 global $theme; ?><!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN""http://www.wapforum.org/DTD/xhtml-mobile10.dtd" > 
<html xmlns="http://www.w3.org/1999/xhtml" <?php	 	 language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php	 	 bloginfo('html_type'); ?>; charset=<?php	 	 bloginfo('charset'); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=3.0, user-scalable=yes">  
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<link rel="apple-touch-icon" sizes="114x114" href="<?php	 	 echo get_template_directory_uri(); ?>/images/apple-touch-icon.png" />
<link rel="apple-touch-startup-image" href="<?php	 	 echo get_template_directory_uri(); ?>/images/apple-touch-startup-image.png" />
<meta name="author" content="FamousThemes" />
<meta name="description" content="My Mobile Page Version 3 Template" />
<meta name="keywords" content="mobile templates, mobile wordpress themes, mobile themes, my mobile page, premium css templates, premium wordpress themes" />
<title><?php	 	 wp_title('&laquo;', true, 'right'); ?> <?php	 	 bloginfo('name'); ?></title>
<!-- Main CSS file -->
<link rel="stylesheet" type="text/css" media="all" href="<?php	bloginfo( 'stylesheet_url' ); ?>" />
<!-- Google web font -->
<link rel="pingback" href="<?php	 	 bloginfo('pingback_url'); ?>" />
<?php	 	 wp_head(); ?>
<link rel="stylesheet" href="<?php	 	 echo get_template_directory_uri(); ?>/prettyphoto/prettyPhoto.css" type="text/css" media="screen" title="prettyPhoto main stylesheet" charset="utf-8" />


<?php	 	 
if ( !is_home() ) {
wp_register_script('effects', get_template_directory_uri().'/scripts/effects.js', false, '1');
wp_enqueue_script('effects');

wp_register_script('prettyPhoto', get_template_directory_uri().'/scripts/jquery.prettyPhoto.js', false, '1');
wp_enqueue_script('prettyPhoto');

wp_register_script('fitvids', get_template_directory_uri().'/scripts/jquery.fitvids.js', false, '1');
wp_enqueue_script('fitvids');

wp_register_script('tabify', get_template_directory_uri().'/scripts/jquery.tabify.js', false, '1');
wp_enqueue_script('tabify');
}
?>

<style>
	@font-face {
	  font-family: 'Open Sans';
	  font-style: normal;
	  font-weight: 300;
	  src: local('Open Sans Light'), local('OpenSans-Light'), url(<?php bloginfo('template_directory')?>'/DXI1ORHCpsQm3Vp6mXoaTXhCUOGz7vYGh680lGh-uXM.woff) format('woff');
	}
	
</style>
<script type="text/javascript">
var $ = jQuery.noConflict();
$(window).load(function() {
	$('.icons_nav').flexslider({
	animation: "slide",
	directionNav: <?php if($theme->get_option('icons_arrows') == 'enable') { ?> true <?php } else { ?> false <?php } ?>,
	animationLoop: false,
	controlNav: false, 
	slideshow: false,
	animationDuration: 300
	});
	$('.panels_slider').flexslider({
	animation: "slide",
	directionNav: false,
	controlNav: true, 
	animationLoop: false,
	slideToStart: <?php	$theme->option('slide_to_start'); ?>,
	animationDuration: 300, 
	slideshow: false
	});
	<?php	 	 if ( !is_home() ) {?>
	$('.images_slider').flexslider({
	animation: "slide",
	directionNav: false,
	controlNav: true,
	animationLoop: true,
	animationDuration: 300, 
	slideshow: false
	});
	<?php	 	 } ?>
});
</script>
<!-- Main effects files -->
<?php	 	 if ( !is_home() ) {?>
    <script type="text/javascript">
    var $ = jQuery.noConflict();
    $(function() {
    $('#tabsmenu').tabify();
    $(".toggle_container").hide(); 
    $(".trigger").click(function(){
        $(this).toggleClass("active").next().slideToggle("slow");
        return false;
    });
    });
    </script>
<?php	 	 } ?>
<?php	 	 if ( is_page_template('gallery.php')  ) {?>
<!-- jQuery Gallery -->
<?php	 	
wp_register_script('tmpl', get_template_directory_uri().'/scripts/jquery.tmpl.min.js', false, '1');
wp_enqueue_script('tmpl');

wp_register_script('easing', get_template_directory_uri().'/scripts/jquery.easing.1.3.js', false, '1');
wp_enqueue_script('easing');

wp_register_script('elastislide', get_template_directory_uri().'/scripts/jquery.elastislide.js', false, '1');
wp_enqueue_script('elastislide');

wp_register_script('gallery', get_template_directory_uri().'/scripts/gallery.js', false, '1');
wp_enqueue_script('gallery');
?>
<noscript>
	<style>
		.es-carousel ul{
			display:block;
		}
	</style>
</noscript>
<script id="img-wrapper-tmpl" type="text/x-jquery-tmpl">	
	<div class="rg-image-wrapper">
		{{if itemsCount > 1}}
			<div class="rg-image-nav">
				<a href="#" class="rg-image-nav-prev">Previous Image</a>
				<a href="#" class="rg-image-nav-next">Next Image</a>
			</div>
		{{/if}}
		<div class="rg-image"></div>
		<div class="rg-loading"></div>
	</div>
</script>
<?php	 	 } ?>
<!-- Hide Mobiles Browser Navigation Bar -->
<script type="text/javascript">
	window.addEventListener("load",function() {
	// Set a timeout...
	setTimeout(function(){
	// Hide the address bar!
	window.scrollTo(0, 1);
	}, 0);
	});
</script>
<!-- Hide on iphone top browser element | only on home page -->
<script type="text/javascript">
if((navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i))) {
    $(window).load(function() {
       $("body").removeClass("home");

       // Check to see if the window is running in app mode. 
       // If it is not, then it is running in full screen mode
        if ( ("standalone" in window.navigator) && !window.navigator.standalone    ){
            $("body").addClass("homeiphone_app");
        } else {
            $("body").addClass("homeiphone_full");
        }
    });
}
</script>
<?php	 	 if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>
<?php	 	 $theme->hook('head'); ?>
</head>
<body <?php	 	 if ( is_home() ) {?>class="home"<?php	 	 } else {?>id="page"<?php	 	 }?>>
