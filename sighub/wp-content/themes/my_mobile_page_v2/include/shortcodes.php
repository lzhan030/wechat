<?php

function services_box( $atts, $content = null)
{
 extract(shortcode_atts(array(
        'title'      => '',
        ), $atts));
   return '<div class="services_content"><h6>'.$title.'</h6><p>'. do_shortcode($content) . '</p></div>';
}
add_shortcode('service', 'services_box');


function portfolio_box( $atts, $content = null)
{
 extract(shortcode_atts(array(
        'title'      => '',
		'image'      => '',
        ), $atts));
   return '<div class="portfolio_content"><h3>'.$title.'</h3><p><img src="'.$image.'" alt="" title="" class="left_pic" />'. do_shortcode($content) . '</p></div>';
}
add_shortcode('portfolio', 'portfolio_box');


?>
