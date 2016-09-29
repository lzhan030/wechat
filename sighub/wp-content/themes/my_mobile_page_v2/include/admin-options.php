<?php

add_action('admin_menu', 'add_theme_pages');

add_option('main_title', '');

add_option('themecolor', 'blue');

add_option('blogcat', '');
add_option('show_archive_menu', '');
add_option('show_blog_menu', '');

add_option('show_social', '');
add_option('show_top', '');
add_option('show_form', '');
add_option('contactemail', '');
add_option('contactsubject', '');


add_option('rss', '');
add_option('google', '');
add_option('facebook', '');
add_option('linkedin', '');
add_option('twitter', '');
add_option('flickr', '');
add_option('myspace', '');
add_option('delicious', '');
add_option('digg', '');
add_option('reddit', '');
add_option('stumbleupon', '');
add_option('technorati', '');

add_option('footer_text', '');

function add_theme_pages() {

	add_theme_page('My Mobile Page Options', 'My Mobile Page Settings', 5, 'colorthemes', 'color_themes_page');

}

function color_themes_page() { ?>

<div class="admin_content">
<?php echo "<h1>" . __( 'My Mobile Page Settings') . "</h1>"; ?>

	
	<form method="post" action="options.php">

		<?php wp_nonce_field('update-options'); ?>

		<h2>Theme Color</h2>

		<div class="admin_row">            
            <div class="admin_color">
            <img src="<?php bloginfo('template_directory'); ?>/images/skins-preview/blue.jpg" alt="" title="" border="0" />
            <div class="color_radio"><input type="radio" name="themecolor" id="themecolor" value="blue" <?php if(get_option('themecolor') == "blue") { echo ' checked'; } ?>/> Blue </div>
            </div>
            <div class="admin_color">
            <img src="<?php bloginfo('template_directory'); ?>/images/skins-preview/black.jpg" alt="" title="" border="0" />
            <div class="color_radio"><input type="radio" name="themecolor" id="themecolor" value="black" <?php if(get_option('themecolor') == "black") { echo ' checked'; } ?>/> Black </div>
            </div>
            <div class="admin_color">
            <img src="<?php bloginfo('template_directory'); ?>/images/skins-preview/red.jpg" alt="" title="" border="0" />
            <div class="color_radio"><input type="radio" name="themecolor" id="themecolor" value="red" <?php if(get_option('themecolor') == "red") { echo ' checked'; } ?>/> Red </div>
            </div>
            <div class="admin_color">
            <img src="<?php bloginfo('template_directory'); ?>/images/skins-preview/orange.jpg" alt="" title="" border="0" />
            <div class="color_radio"><input type="radio" name="themecolor" id="themecolor" value="orange" <?php if(get_option('themecolor') == "orange") { echo ' checked'; } ?>/> Orange </div>
            </div>
            <div class="admin_color">
            <img src="<?php bloginfo('template_directory'); ?>/images/skins-preview/green.jpg" alt="" title="" border="0" />
            <div class="color_radio"><input type="radio" name="themecolor" id="themecolor" value="green" <?php if(get_option('themecolor') == "green") { echo ' checked'; } ?>/> Green </div>
            </div>
		</div>
          
		<h2>Home Page</h2>
        
        <div class="admin_row">
        <label>Logo Image</label>
         <input type="text" name="main_title" id="main_title" size="50" value="<?php echo get_option('main_title'); ?>" />
            <?php 
			$main_title = get_option('main_title');
			if(!empty($main_title)) { ?>
            <img src="<?php echo $main_title; ?>" alt="" title="" border="0" />
            <?php }?>
        </div>
        
		<h2>Blog Page Settings</h2>
		<div class="admin_row">
		<label>Select Blog Category:</label>
	    <?php wp_dropdown_categories('show_option_none=Select category&hide_empty=0&name=blogcat&selected='.get_option('blogcat')); ?>
        </div>
        
        <div class="admin_row">
        <label>Show archive drop menu on blog page</label>
        <select name="show_archive_menu" id="show_archive_menu">
        <option name="enable" value="enable"<?php if(get_option('show_archive_menu') == "enable") { echo ' selected'; } ?>>Enable</option>
        <option name="disable" value="disable"<?php if(get_option('show_archive_menu') == "disable") { echo ' selected'; } ?>>Disable</option>
        </select>
        </div>
        
        <div class="admin_row">
        <label>Show categories drop menu on blog page</label>
        <select name="show_blog_menu" id="show_blog_menu">
        <option name="enable" value="enable"<?php if(get_option('show_blog_menu') == "enable") { echo ' selected'; } ?>>Enable</option>
        <option name="disable" value="disable"<?php if(get_option('show_blog_menu') == "disable") { echo ' selected'; } ?>>Disable</option>
        </select>
        </div>
        
        <h2>Contact Page</h2>
        
        <div class="admin_row">
        <label>Show contact form on contact page</label>
        <select name="show_form" id="show_form">
        <option name="enable" value="enable"<?php if(get_option('show_form') == "enable") { echo ' selected'; } ?>>Enable</option>
        <option name="disable" value="disable"<?php if(get_option('show_form') == "disable") { echo ' selected'; } ?>>Disable</option>
        </select>
        </div>
        
        <div class="admin_row">
		<label>Contact Email:</label>
        <input type="text" name="contactemail" id="contactemail" size="30" value="<?php echo get_option('contactemail'); ?>" />	
        </div>
        
        <div class="admin_row">
		<label>Contact Subject:</label>
        <input type="text" name="contactsubject" id="contactsubject" size="30" value="<?php echo get_option('contactsubject'); ?>" />	
        </div>

        
        <h2>Social Icons</h2>
          
        <div class="admin_row">
            <div class="social_icon_row">
            <label class="social_icon_label">RSS:</label>
            <img src="<?php bloginfo('template_directory'); ?>/images/social/rss.png" alt="" title="" border="0"/>
            <input class="social_icon_input" type="text" name="rss" id="rss" size="18" value="<?php echo get_option('rss'); ?>" />	
            </div>
            <div class="social_icon_row">
            <label class="social_icon_label">Google:</label>
            <img src="<?php bloginfo('template_directory'); ?>/images/social/google.png" alt="" title="" border="0"/>
            <input class="social_icon_input" type="text" name="google" id="google" size="18" value="<?php echo get_option('google'); ?>" />	
            </div>
            <div class="social_icon_row">
            <label class="social_icon_label">Facebook:</label>
            <img src="<?php bloginfo('template_directory'); ?>/images/social/facebook.png" alt="" title="" border="0"/>
            <input class="social_icon_input" type="text" name="facebook" id="facebook" size="18" value="<?php echo get_option('facebook'); ?>" />	
            </div>
            
            <div class="social_icon_row">
            <label class="social_icon_label">linkedin:</label>
            <img src="<?php bloginfo('template_directory'); ?>/images/social/linkedin.png" alt="" title="" border="0"/>
            <input class="social_icon_input" type="text" name="linkedin" id="linkedin" size="18" value="<?php echo get_option('linkedin'); ?>" />	
            </div>
            <div class="social_icon_row">
            <label class="social_icon_label">twitter:</label>
            <img src="<?php bloginfo('template_directory'); ?>/images/social/twitter.png" alt="" title="" border="0"/>
            <input class="social_icon_input" type="text" name="twitter" id="twitter" size="18" value="<?php echo get_option('twitter'); ?>" />	
            </div>
            <div class="social_icon_row">
            <label class="social_icon_label">flickr:</label>
            <img src="<?php bloginfo('template_directory'); ?>/images/social/flickr.png" alt="" title="" border="0"/>
            <input class="social_icon_input" type="text" name="flickr" id="flickr" size="18" value="<?php echo get_option('flickr'); ?>" />	
            </div>
            
            <div class="social_icon_row">
            <label class="social_icon_label">myspace:</label>
            <img src="<?php bloginfo('template_directory'); ?>/images/social/myspace.png" alt="" title="" border="0"/>
            <input class="social_icon_input" type="text" name="myspace" id="myspace" size="18" value="<?php echo get_option('myspace'); ?>" />	
            </div>
            <div class="social_icon_row">
            <label class="social_icon_label">delicious:</label>
            <img src="<?php bloginfo('template_directory'); ?>/images/social/delicious.png" alt="" title="" border="0"/>
            <input class="social_icon_input" type="text" name="delicious" id="delicious" size="18" value="<?php echo get_option('delicious'); ?>" />	
            </div>
            <div class="social_icon_row">
            <label class="social_icon_label">digg:</label>
            <img src="<?php bloginfo('template_directory'); ?>/images/social/digg.png" alt="" title="" border="0"/>
            <input class="social_icon_input" type="text" name="digg" id="digg" size="18" value="<?php echo get_option('digg'); ?>" />	
            </div>
            
            <div class="social_icon_row">
            <label class="social_icon_label">reddit:</label>
            <img src="<?php bloginfo('template_directory'); ?>/images/social/reddit.png" alt="" title="" border="0"/>
            <input class="social_icon_input" type="text" name="reddit" id="reddit" size="18" value="<?php echo get_option('reddit'); ?>" />	
            </div>
            <div class="social_icon_row">
            <label class="social_icon_label">stumbleupon:</label>
            <img src="<?php bloginfo('template_directory'); ?>/images/social/stumbleupon.png" alt="" title="" border="0"/>
            <input class="social_icon_input" type="text" name="stumbleupon" id="stumbleupon" size="18" value="<?php echo get_option('stumbleupon'); ?>" />	
            </div>
            <div class="social_icon_row">
            <label class="social_icon_label">technorati:</label>
            <img src="<?php bloginfo('template_directory'); ?>/images/social/technorati.png" alt="" title="" border="0"/>
            <input class="social_icon_input" type="text" name="technorati" id="technorati" size="18" value="<?php echo get_option('technorati'); ?>" />	
            </div>
            
        </div>
        
         <h2>Footer Settings</h2>
         
        <div class="admin_row">
        <label>Show social button on footer left</label>
        <select name="show_social" id="show_social">
        <option name="enable" value="enable"<?php if(get_option('show_social') == "enable") { echo ' selected'; } ?>>Enable</option>
        <option name="disable" value="disable"<?php if(get_option('show_social') == "disable") { echo ' selected'; } ?>>Disable</option>
        </select>
        </div>
        
        <div class="admin_row">
        <label>Show "go ont top" button on footer right</label>
        <select name="show_top" id="show_top">
        <option name="enable" value="enable"<?php if(get_option('show_top') == "enable") { echo ' selected'; } ?>>Enable</option>
        <option name="disable" value="disable"<?php if(get_option('show_top') == "disable") { echo ' selected'; } ?>>Disable</option>
        </select>
        </div>
        <div class="admin_row">
		<label>Footer text:</label>
        <input type="text" name="footer_text" id="footer_text" size="30" value="<?php echo get_option('footer_text'); ?>" />	
        </div> 
              
		<input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
		<input type="hidden" name="action" value="update" />
		<input type="hidden" name="page_options" value="themecolor,main_title,blogcat,show_archive_menu,show_blog_menu,show_social,show_top,show_form,contactemail,contactsubject,rss,google,facebook,linkedin,twitter,flickr,myspace,delicious,digg,reddit,stumbleupon,
technorati,footer_text" />
	</form>

</div>
<?php } 

?>