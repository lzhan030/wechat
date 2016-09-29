<?php

function subtitle_descr( $atts, $content = null)
{
 extract(shortcode_atts(array(
   
        ), $atts));
   return '<span class="subtitle_descr">'. do_shortcode($content) . '</span>';
}
add_shortcode('subtitle', 'subtitle_descr');

/*------------------------------TOOGLE SHORTCODES--------------------*/
function toogle_wrap( $atts, $content = null)
{
 extract(shortcode_atts(array(
        'title'      => '',
        ), $atts));
   return '<div class="toogle_wrap"><div class="trigger"><a href="#">'.$title.'</a></div><div class="toggle_container"><p>'. do_shortcode($content) .'</p></div></div>';
}
add_shortcode('toogle', 'toogle_wrap');

/*------------------------------TABS SHORTCODES--------------------*/

function tabs_menu( $atts, $content = null)
{
 extract(shortcode_atts(array(
        'title1'      => 'Tab one',
		'title2'      => 'Tab two',
		'title3'      => 'Tab three',
        ), $atts));
   return '<ul id="tabsmenu" class="tabsmenu"><li class="active"><a href="#tab1">'.$title1.'</a></li><li><a href="#tab2">'.$title2.'</a></li><li><a href="#tab3">'.$title3.'</a></li></ul>';
}
add_shortcode('tabsmenu', 'tabs_menu');

function tabs_content( $atts, $content = null)
{
 extract(shortcode_atts(array(
        'content1'      => 'Tab content one',
		'content2'      => 'Tab content two',
		'content3'      => 'Tab content three',
        ), $atts));
   return '<div id="tab1" class="tabcontent"><p>'.$content1.'</p></div><div id="tab2" class="tabcontent"><p>'.$content2.'</p></div><div id="tab3" class="tabcontent"><p>'.$content3.'</p></div>';
}
add_shortcode('tabscontent', 'tabs_content');

/*------------------------------IMAGES SHORTCODES--------------------*/

function image_full( $atts, $content = null)
{
 extract(shortcode_atts(array(
        'imagehover'      => '',
		'templateurl'      => get_template_directory_uri(),
        ), $atts));
   return '<a href="'.$imagehover.'" rel="prettyPhoto[gallery]"><img src="'.do_shortcode($content) .'" alt="" title="" border="0" class="rounded" /></a><img src="'.$templateurl.'/images/shadow.png" alt="" title="" border="0" class="shadow" />';
}
add_shortcode('imagefull', 'image_full');

function image_half( $atts, $content = null)
{
 extract(shortcode_atts(array(
		'templateurl'      => get_template_directory_uri(),
        ), $atts));
   return '<ul class="portfolio">'.do_shortcode($content) .'</ul>';
}
add_shortcode('imagehalf', 'image_half');


function image_third( $atts, $content = null)
{
 extract(shortcode_atts(array(
		'templateurl'      => get_template_directory_uri(),
        ), $atts));
   return '<ul class="portfolio-third">'.do_shortcode($content).'</ul>';
}
add_shortcode('imagethird', 'image_third');

function image_url( $atts, $content = null)
{
 extract(shortcode_atts(array(
        'imagehover'      => '',
		'title'      => '',
		'templateurl'      => get_template_directory_uri(),
        ), $atts));
   return '<li><span>'.$title.'</span><a href="'.$imagehover.'" rel="prettyPhoto[gallery]"><img src="'.do_shortcode($content) .'" alt="" title="" border="0" class="rounded-half" /></a><img src="'.$templateurl.'/images/shadow.png" alt="" title="" border="0" class="shadow" /></li>';
}
add_shortcode('imageurl', 'image_url');

/*------------------------------VIDEO SHORTCODES--------------------*/

function video_full( $atts, $content = null)
{
 extract(shortcode_atts(array(
		'templateurl'      => get_template_directory_uri(),
        ), $atts));
   return '<div class="videocontainer"><iframe src="'.do_shortcode($content) .'" frameborder="0" allowfullscreen></iframe></div><img src="'.$templateurl.'/images/shadow.png" alt="" title="" border="0" class="shadow" />';
}
add_shortcode('videofull', 'video_full');



function video_half( $atts, $content = null)
{
 extract(shortcode_atts(array(
		'templateurl'      => get_template_directory_uri(),
        ), $atts));
   return '<ul class="portfolio">'.do_shortcode($content) .'</ul>';
}
add_shortcode('videohalf', 'video_half');

function video_half_url( $atts, $content = null)
{
 extract(shortcode_atts(array(
		'title'      => '',
		'templateurl'      => get_template_directory_uri(),
        ), $atts));
   return '<li><span>'.$title.'</span><div class="videocontainer"><iframe src="'.do_shortcode($content) .'" frameborder="0" allowfullscreen></iframe></div><img src="'.$templateurl.'/images/shadow.png" alt="" title="" border="0" class="shadow" /></li>';
}
add_shortcode('videohalfurl', 'video_half_url');

/*------------------------------SLIDESHOW SHORTCODES--------------------*/
function slideshow( $atts, $content = null)
{
 extract(shortcode_atts(array(
		'templateurl'      => get_template_directory_uri(),
        ), $atts));
   return '<div class="images_slider_container"><div class="images_slider"><ul class="slides">'.do_shortcode($content) .'</ul></div></div>';
}
add_shortcode('slideshow', 'slideshow');

function slide_img( $atts, $content = null)
{
 extract(shortcode_atts(array(
		'templateurl'      => get_template_directory_uri(),
        ), $atts));
   return '<li><img src="'.do_shortcode($content) .'" alt="" title="" border="0" /></li>';
}
add_shortcode('slide-image', 'slide_img');

/*------------------------------SECTION CONTENT SHORTCODES--------------------*/
function section_content( $atts, $content = null)
{
 extract(shortcode_atts(array(
		'templateurl'      => get_template_directory_uri(),
		'imageurl'      => '',
		'titleurl'      => '',
		'title'      => '',
        ), $atts));
   return '<div class="post"><div class="post_thumb"><img src="'.$imageurl.'" alt="" title="" border="0" class="rounded-half" /><img src="'.$templateurl.'/images/shadow.png" alt="" title="" border="0" class="shadow" /></div><div class="post_content"><h3><a href="'.$titleurl.'">'.$title.'</a></h3><p>'.do_shortcode($content) .'</p></div></div>';
}
add_shortcode('section', 'section_content');

/*------------------------------Social button SHORTCODES--------------------*/
function social_buttons( $atts, $content = null)
{
 extract(shortcode_atts(array(
		'templateurl'      => get_template_directory_uri(),
        ), $atts));
   return '<ul class="social">'.do_shortcode($content) .'</ul>';
}
add_shortcode('social', 'social_buttons');

function social_button( $atts, $content = null)
{
 extract(shortcode_atts(array(
		'templateurl'      => get_template_directory_uri(),
		'url'      => get_template_directory_uri(),
        ), $atts));
   return '<li><a href="'.$url.'"><img src="'.do_shortcode($content) .'" alt="" title="" border="0" class="rounded-half" /></a></li>';
}
add_shortcode('socialicon', 'social_button');

/*------------------------------Call button SHORTCODES--------------------*/
function call_button( $atts, $content = null)
{
 extract(shortcode_atts(array(
		'phone'      => '',
        ), $atts));
   return '<a href="tel:'.$phone.'" class="call_button">'.do_shortcode($content) .'</a>';
}
add_shortcode('callbutton', 'call_button');

?>
