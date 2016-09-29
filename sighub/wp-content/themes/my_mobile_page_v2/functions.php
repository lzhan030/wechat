<?php
/*----------------------------------------------------------------------*/
/* Include Files */
/*----------------------------------------------------------------------*/

require_once(TEMPLATEPATH . '/include/admin-options.php');
require_once(TEMPLATEPATH . '/include/menu-options.php');
require_once(TEMPLATEPATH . '/include/shortcodes.php');
automatic_feed_links();

/*----------------------------------------------------------------------*/
/* Include CSS File used for admin skin */
/*----------------------------------------------------------------------*/
function admin_css() 
{
wp_enqueue_style('com-css', get_bloginfo('template_url').'/include/admin.css');
}
add_action('admin_print_styles', 'admin_css');

/*----------------------------------------------------------------------*/
/* Add featured image function */
/*----------------------------------------------------------------------*/
add_theme_support('post-thumbnails');
add_image_size('menu-icon-size', 85, 85);
/*----------------------------------------------------------------------*/
/* Register main menu */
/*----------------------------------------------------------------------*/
add_action( 'init', 'create_post_type' );
function create_post_type() {
  register_post_type( 'icons_menu',
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

function post_is_in_descendant_category( $cats, $_post = null )
{
	foreach ( (array) $cats as $cat ) {
		// get_term_children() accepts integer ID only
		$descendants = get_term_children( (int) $cat, 'category');
		if ( $descendants && in_category( $descendants, $_post ) )
			return true;
	}
	return false;
}


?>