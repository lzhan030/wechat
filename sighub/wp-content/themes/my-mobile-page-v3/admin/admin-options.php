<?php

/*********************************************
* General Options
*********************************************
*/
$this->admin_option(array('General', 1),
	'Title', 'maintitle', 
	'text', '', 
	array('help' => 'Leave empty if you want to diplay just the home screen panels.', 'display'=>'extended')
);

$this->admin_option('General',
	'Logo Image', 'logo', 
	'imageupload', '', 
	array('help' => 'Use a logo as image instead of title. Leave blank if you are using title.', 'display'=>'extended')
);
$this->admin_option('General',
	'Home screen slider (slide to start)', 'slide_to_start', 
	'text', '1', 
	array('help' => 'First slide is 0. Add the nr of the slide you want the home screen to start.', 'display'=>'extended')
);
$this->admin_option('General',
	'Enable pagination arrows on bottom icons navigation', 'icons_arrows', 
	'select', 'enable', 
	array('help'=>'', 'display'=>'inline', 'options'=>array('enable' => 'Enable', 'disable' => 'Disable'))
);
$this->admin_option('General',
	'Custom CSS', 'custom_css', 
	'textarea', '', 
	array('help' => '')
);

$this->admin_option('General',
	'Analytics Code', 'analytics_code', 
	'textarea', '', 
	array('help' => '')
);
/*********************************************
* Theme Colors
*********************************************
*/
$this->admin_option(array('Colors', 2),
	'Main Background Color', 'bg_color', 
	'colorpicker', '4a2d70', 
	array('help' => 'Main website background color', 'display'=>'extended')
);
$this->admin_option('Colors',
	'Background Image', 'bg_image', 
	'imageupload', get_template_directory_uri() . "/images/pages_bg.jpg",
	array('help' => 'Only if you want to use one. Leave blank if you are using just the background color.', 'display'=>'extended')
);
$this->admin_option('Colors', 
	'Center Content Background Color', 'center_color', 
	'colorpicker', 'FFFFFF', 
	array('help' => 'By default the center background is white', 'display'=>'extended')
);
$this->admin_option('Colors', 
	'Center Content Text Color', 'center_text_color', 
	'colorpicker', '000000', 
	array('help' => 'By default the center text is black', 'display'=>'extended')
);
$this->admin_option('Colors', 
	'Title Color', 'title_color', 
	'colorpicker', '', 
	array('help' => 'Add color only of you are using a title', 'display'=>'extended')
);
$this->admin_option('Colors', 
	'Links general colors', 'links_colors', 
	'colorpicker', '', 
	array('help' => 'This will replace all purple default color used in default theme', 'display'=>'inline')
);
$this->admin_option('Colors', 
	'Header and Footer centered text color', 'headerfooter_color', 
	'colorpicker', '', 
	array('help' => '')
);
$this->admin_option('Colors', 
	'Header and Footer buttons text color', 'headerfooterbuttons_color', 
	'colorpicker', '', 
	array('help' => '')
);
/*********************************************
* Theme Colors
*********************************************
*/
$this->admin_option(array('Texts', 3),
	'Header Text', 'header_text', 
	'text', 'My Mobile Page V3',  
	array('help' => '')
);
$this->admin_option('Texts',
	'Footer Text', 'footer_text', 
	'text', 'My Mobile Page V3', 
	array('help' => '')
);
$this->admin_option('Texts',
	'Home Button Text', 'home_button', 
	'text', 'Home', 
	array('help' => '')
);
$this->admin_option('Texts',
	'Menu Button Text', 'menu_button', 
	'text', 'Menu', 
	array('help' => '')
);
$this->admin_option('Texts',
	'Close Menu Button Text', 'close_button', 
	'text', 'Close', 
	array('help' => '')
);
$this->admin_option('Texts',
	'Go on Top Button Text', 'top_button', 
	'text', 'Top', 
	array('help' => '')
);
/*********************************************
* Social Icons
*********************************************
*/
$this->admin_option(array('Social icons', 4),
	'RSS icon', 'icon_rss', 
	'imageupload', get_template_directory_uri() . "/images/social/rss.png", 
	array('help' => 'Remove if you do not want to display this icon', 'display'=>'inline')
);
$this->admin_option('Social icons',
	'RSS icon URL', 'url_rss', 
	'text', '', 
	array('help' => '')
);
$this->admin_option('Social icons', 
	'Twitter icon', 'icon_twitter', 
	'imageupload', get_template_directory_uri() . "/images/social/twitter.png",  
	array('help' => 'Remove if you do not want to display this icon', 'display'=>'inline')
);
$this->admin_option('Social icons',
	'Twitter icon URL', 'url_twitter', 
	'text', '', 
	array('help' => '')
);
$this->admin_option('Social icons', 
	'Facebook icon', 'icon_facebook', 
	'imageupload', get_template_directory_uri() . "/images/social/facebook.png",  
	array('help' => 'Remove if you do not want to display this icon', 'display'=>'inline')
);
$this->admin_option('Social icons',
	'Facebook icon URL', 'url_facebook', 
	'text', '', 
	array('help' => '')
);
$this->admin_option('Social icons', 
	'Digg icon', 'icon_digg', 
	'imageupload', get_template_directory_uri() . "/images/social/digg.png",  
	array('help' => 'Remove if you do not want to display this icon', 'display'=>'inline')
);
$this->admin_option('Social icons',
	'Digg icon URL', 'url_digg', 
	'text', '', 
	array('help' => '')
);
$this->admin_option('Social icons', 
	'Google plus icon', 'icon_google', 
	'imageupload', get_template_directory_uri() . "/images/social/google.png",  
	array('help' => 'Remove if you do not want to display this icon', 'display'=>'inline')
);
$this->admin_option('Social icons',
	'Google plus icon URL', 'url_google', 
	'text', '', 
	array('help' => '')
);
$this->admin_option('Social icons', 
	'Flickr icon', 'icon_flickr', 
	'imageupload', get_template_directory_uri() . "/images/social/flickr.png",  
	array('help' => 'Remove if you do not want to display this icon', 'display'=>'inline')
);
$this->admin_option('Social icons',
	'Flickr icon URL', 'url_flickr', 
	'text', '', 
	array('help' => '')
);
$this->admin_option('Social icons', 
	'Reddit icon', 'icon_reddit', 
	'imageupload', get_template_directory_uri() . "/images/social/reddit.png",  
	array('help' => 'Remove if you do not want to display this icon', 'display'=>'inline')
);
$this->admin_option('Social icons',
	'Reddit icon URL', 'url_reddit', 
	'text', '', 
	array('help' => '')
);
$this->admin_option('Social icons', 
	'Vimeo icon', 'icon_vimeo', 
	'imageupload', get_template_directory_uri() . "/images/social/vimeo.png",  
	array('help' => 'Remove if you do not want to display this icon', 'display'=>'inline')
);
$this->admin_option('Social icons',
	'Vimeo icon URL', 'url_vimeo', 
	'text', '', 
	array('help' => '')
);
/*********************************************
* Widgets
*********************************************
*/
$this->admin_option(array('Widgets', 5),
	'Enable Archive widget area', 'show_widget_area_1', 
	'select', 'disable',
	array('help'=>'', 'display'=>'inline', 'options'=>array('enable' => 'Enable', 'disable' => 'Disable'))
);
$this->admin_option('Widgets',
	'Enable Pages widget area', 'show_widget_area_2', 
	'select', 'disable', 
	array('help'=>'', 'display'=>'inline', 'options'=>array('enable' => 'Enable', 'disable' => 'Disable'))
);
$this->admin_option('Widgets',
	'Enable Contact widget area', 'show_widget_area_3', 
	'select', 'disable', 
	array('help'=>'', 'display'=>'inline', 'options'=>array('enable' => 'Enable', 'disable' => 'Disable'))
);
$this->admin_option('Widgets',
	'Enable Gallery widget area', 'show_widget_area_4', 
	'select', 'disable', 
	array('help'=>'', 'display'=>'inline', 'options'=>array('enable' => 'Enable', 'disable' => 'Disable'))
);
/*********************************************
* Contact Options
*********************************************
*/
$this->admin_option(array('Contact Page', 6),
	'Enable Contact Form', 'show_contact_form', 
	'select', 'enable',
	array('help'=>'', 'display'=>'inline', 'options'=>array('enable' => 'Enable', 'disable' => 'Disable'))
);
$this->admin_option('Contact Page',
	'Contact form title', 'contact_form_title', 
	'text', 'Contact form', 
	array('help' => '')
);
$this->admin_option('Contact Page',
	'Name lable text', 'label_name', 
	'text', 'Name', 
	array('help' => '')
);
$this->admin_option('Contact Page',
	'Email lable text', 'label_email', 
	'text', 'Email', 
	array('help' => '')
);
$this->admin_option('Contact Page',
	'Message label text', 'label_message', 
	'text', 'Message', 
	array('help' => '')
);
$this->admin_option('Contact Page',
	'Contact email', 'contactemail', 
	'text', '', 
	array('help' => '')
);
$this->admin_option('Contact Page',
	'Contact subject title', 'contactsubject', 
	'text', 'Contact subject title', 
	array('help' => '')
);
$this->admin_option('Contact Page',
	'Contact succes message', 'contactsucces', 
	'text', 'Your message has been sent. Thank you!', 
	array('help' => '')
);

/*********************************************
* Reset Options
*********************************************
*/
$this->admin_option(array('Reset Options', 7), 
'Reset Theme Options', 'reset_options', 
'content', '
<div id="admin_reset_options" style="margin-bottom:40px; display:none;"></div>
<div style="margin-bottom:40px;"><a class="admin-button-reset" onclick="if (confirm(\'All the saved settings will be lost! Do you really want to continue?\')) { admincore_form(\'admin_options&do=reset\', \'fpForm\',\'admin_reset_options\',\'true\'); } return false;">Reset Options Now</a></div>', 
array('help' => '<span style="color:red; margin:0 0 40px 0; display:block;"><strong>Note:</strong> All the previous saved settings will be lost!</span>', 'display'=>'extended-top')
);

?>