<?php
if ( function_exists('register_sidebar') )
	register_sidebar(array(
		'before_widget' => '<li id="%1$s" class="widget %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h2>',
		'after_title' => '</h2>',
	));
	register_nav_menus(
		array(
			'header-menu' => __( 'header-menu' )
		)
	);
function wp_catch_first_image($image_size = '') {  
	global $post, $posts;  
		$first_img = '';  
		ob_start();  
		ob_end_clean();  
		$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);  
		$first_img = $matches [1] [0];
		return $first_img;  
  	}
// function new_excerpt_more( $more ) {
	// return '...';
// }
// add_filter('excerpt_more', 'new_excerpt_more');
?>