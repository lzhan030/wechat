<?php	 	
/*----------------------------------------------------------------------*/
/* Include Files */
/*----------------------------------------------------------------------*/
require_once TEMPLATEPATH . '/admin/core.php';
require_once(TEMPLATEPATH . '/admin/menu-options.php');
require_once(TEMPLATEPATH . '/admin/slider-options.php');
require_once(TEMPLATEPATH . '/admin/shortcodes.php');
$theme = new Admincore();
$theme->theme_name = 'My Mobile Page V3';
$theme->load();
add_theme_support( 'automatic-feed-links' );
add_filter( 'show_admin_bar', '__return_false' );
/*----------------------------------------------------------------------*/
/* Load jquery */
/*----------------------------------------------------------------------*/
if ( !is_admin() ) { // instruction to only load if it is not the admin area
function my_init() {
		// comment out the next two lines to load the local copy of jQuery
		wp_deregister_script('jquery');
		wp_register_script('jquery', get_bloginfo('template_directory').'/../ReeooV3/we7/script/jquery-1.7.2.min.js', false, '1.6.1');
		wp_enqueue_script('jquery');

		wp_register_script('flexslider', get_template_directory_uri().'/scripts/jquery.flexslider.js', false, '1.8');
		wp_enqueue_script('flexslider');
}
add_action('init', 'my_init');
}

/*----------------------------------------------------------------------*/
/* Add shortcode buttons */
/*----------------------------------------------------------------------*/
add_action('init', 'add_button');
function add_button() {  
   if ( current_user_can('edit_posts') &&  current_user_can('edit_pages') )  
   {  
	 add_filter('mce_external_plugins', 'add_plugin');  
	 add_filter('mce_buttons', 'register_button');  
   }  
}    
function register_button($buttons) {  
   array_push($buttons, "subtitle", "toogle", "tabsmenu", "section", "imagefull", "imagehalf", "imagethird", "videofull", "videohalf", "slideshow", "social", "callbutton");  
   return $buttons;  
} 
function add_plugin($plugin_array) {  
   $plugin_array['subtitle'] = get_template_directory_uri().'/scripts/customcodes.js';
   $plugin_array['toogle'] = get_template_directory_uri().'/scripts/customcodes.js';
   $plugin_array['tabsmenu'] = get_template_directory_uri().'/scripts/customcodes.js';
   $plugin_array['section'] = get_template_directory_uri().'/scripts/customcodes.js';
   $plugin_array['imagefull'] = get_template_directory_uri().'/scripts/customcodes.js';
   $plugin_array['imagehalf'] = get_template_directory_uri().'/scripts/customcodes.js';
   $plugin_array['imagethird'] = get_template_directory_uri().'/scripts/customcodes.js';
   $plugin_array['videofull'] = get_template_directory_uri().'/scripts/customcodes.js';
   $plugin_array['videohalf'] = get_template_directory_uri().'/scripts/customcodes.js';
   $plugin_array['slideshow'] = get_template_directory_uri().'/scripts/customcodes.js';
   $plugin_array['social'] = get_template_directory_uri().'/scripts/customcodes.js';
   $plugin_array['callbutton'] = get_template_directory_uri().'/scripts/customcodes.js';
   return $plugin_array;  
}  
/*----------------------------------------------------------------------*/
/* Add featured image function */
/*----------------------------------------------------------------------*/
add_theme_support('post-thumbnails');
add_image_size('menu-icon-size', 108, 108);
/*----------------------------------------------------------------------*/
/* Remove images atributes to make them 100% width */
/*----------------------------------------------------------------------*/
add_filter( 'post_thumbnail_html', 'remove_thumbnail_dimensions', 10 );
add_filter( 'image_send_to_editor', 'remove_thumbnail_dimensions', 10 );
// Removes attached image sizes as well
add_filter( 'the_content', 'remove_thumbnail_dimensions', 10 );
function remove_thumbnail_dimensions( $html ) {
    $html = preg_replace( '/(width|height)=\"\d*\"\s/', "", $html );
    return $html;
}
/*----------------------------------------------------------------------*/
/* Create custom post types */
/*----------------------------------------------------------------------*/
add_action( 'init', 'create_post_type' );
function create_post_type() {
  register_post_type( 'icons_menu_v3',
    array(
      'labels' => array(
        'name' => __( 'Menu items' ),
		'add_new_item' => __('Add New Menu Icon'),
        'singular_name' => __( 'Menu item' )
      ),
      'public' => true,
	  'supports' => array( 'title', 'page-attributes', 'thumbnail')
    )
  );
	register_post_type( 'slider',
	array(
	  'labels' => array(
		'name' => __( 'Home screen slider' ),
		'add_new_item' => __('Add New Slide'),
		'singular_name' => __( 'Slider item' )
	  ),
	  'public' => true,
	  'supports' => array( 'title', 'thumbnail')
	)
	);
	register_post_type( 'gallery',
	array(
	  'labels' => array(
		'name' => __( 'Gallery items' ),
		'add_new_item' => __('Add New Photo'),
		'singular_name' => __( 'Gallery item' )
	  ),
	  'public' => true,
	  'supports' => array( 'title', 'thumbnail')
	)
	);
}
post_type_supports( $postype, $feature );

/*----------------------------------------------------------------------*/
/* Home exerpt used for posts on home page */
/*----------------------------------------------------------------------*/
add_filter('the_excerpt', 'home_excerpts');
function home_excerpts($content = false) {
            global $post;
            $mycontent = $post->post_excerpt;
 
            $mycontent = $post->post_content;
            $mycontent = strip_shortcodes($mycontent);
            $mycontent = str_replace(']]>', ']]&gt;', $mycontent);
            $mycontent = strip_tags($mycontent);
            $excerpt_length = 70;
            $words = explode(' ', $mycontent, $excerpt_length + 1);
            if(count($words) > $excerpt_length) :
                array_pop($words);
                array_push($words, '...');
                $mycontent = implode(' ', $words);
            endif;
            $mycontent = '<p>' . $mycontent . '</p>';
// Make sure to return the content
    return $mycontent;
}
function blog_excerpts($content = false) {
            global $post;
            $mycontent = $post->post_excerpt;
 
            $mycontent = $post->post_content;
            $mycontent = strip_shortcodes($mycontent);
            $mycontent = str_replace(']]>', ']]&gt;', $mycontent);
            $mycontent = strip_tags($mycontent);
            $excerpt_length = 23;
            $words = explode(' ', $mycontent, $excerpt_length + 1);
            if(count($words) > $excerpt_length) :
                array_pop($words);
                array_push($words, '...');
                $mycontent = implode(' ', $words);
            endif;
            $mycontent = '<p>' . $mycontent . '</p>';
// Make sure to return the content
    return $mycontent;
}

/*----------------------------------------------------------------------*/
/* Register Widgets */
/*----------------------------------------------------------------------*/
register_sidebar( array(
	'name' => __( 'Archive widget area'),
	'before_widget' => '<div class="widget_section">',
	'after_widget' => '</div>',
	'before_title' => '<h2>',
	'after_title' => '</h2>',
) );
register_sidebar( array(
	'name' => __( 'Pages widget area'),
	'before_widget' => '<div class="widget_section">',
	'after_widget' => '</div>',
	'before_title' => '<h2>',
	'after_title' => '</h2>',
) );
register_sidebar( array(
	'name' => __( 'Contact widget area'),
	'before_widget' => '<div class="widget_section">',
	'after_widget' => '</div>',
	'before_title' => '<h2>',
	'after_title' => '</h2>',
) );
register_sidebar( array(
	'name' => __( 'Gallery widget area'),
	'before_widget' => '<div class="widget_section">',
	'after_widget' => '</div>',
	'before_title' => '<h2>',
	'after_title' => '</h2>',
) );

?>