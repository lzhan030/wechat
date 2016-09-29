<?php
/*
Template Name: Contact
*/
get_header(); ?>
<div id="pagecontainer">
    
    	<div id="header" class="black_gradient">
            <a href="<?php echo home_url().'/?site='.$siteId; ?>" class="back_button black_button"><?php $theme->option('home_button'); ?></a>
            <div class="page_title"><?php $theme->option('header_text'); ?></div>
            <a href="#" id="menu_open" class="black_button"><?php $theme->option('menu_button'); ?></a>
            <a href="#" id="menu_close" class="black_button"><?php $theme->option('close_button'); ?></a>
            <div class="clear"></div>
        </div>
        
    	<div id="pages_nav">
            <div class="icons_nav">
            <?php if($theme->get_option('icons_arrows') == 'enable') { ?> <div class="paginated"> <?php } ?> 
                <ul class="slides">
					<?php
					$count = 1;
					query_posts(array( 'post_type' => 'icons_menu_v3', 'orderby' => 'menu_order', 'order' => 'ASC', 'showposts' => '999')); ?>
                    <?php $postsnr = $wp_query->found_posts; ?>
                    <?php if (have_posts()) : ?>
                    <li>
                    <?php while (have_posts()) : the_post(); 
					/*如果没有http，证明为内链，加上home_url显示，插入时，判断有再截取入库*/
					$tmp = stristr(get_post_meta($post->ID, "menu_item_url", $single = true),"http");
					$itemurl=get_post_meta($post->ID, "menu_item_url", $single = true);
					if(($tmp===false)&&(!empty($itemurl))){
						$linkurl=home_url().get_post_meta($post->ID, "menu_item_url", $single = true);
					}else{				
						$linkurl=get_post_meta($post->ID, "menu_item_url", $single = true);
					}
					
					?>
                    <a href="<?php echo $linkurl; ?>" class="icon"><?php the_post_thumbnail('menu-icon-size'); ?><span><?php the_title(); ?></span></a>
                    <?php if ($postsnr > 4 && $count == 4){ ?>
                    </li><li>
                    <?php } if ($postsnr > 8 && $count == 8){ ?>
                    </li><li>
                    <?php } ?>
                    <?php $count++; endwhile; ?>
					</li>
                    <?php if ($postsnr < 4 || $postsnr == 4){ ?>
                    <li></li>
                    <?php } ?>
					<?php endif; ?>
                </ul>
                <?php if($theme->get_option('icons_arrows') == 'enable') { ?> </div> <?php } ?>  
          </div>
      </div>

	
        <!-- form validation scripts -->
        <script src="<?php echo get_template_directory_uri(); ?>/scripts/jquery.validate.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            // initialize form validation
            jQuery(document).ready(function() {
                $("#CommentForm").validate({
                    submitHandler: function(form) {
                        // form is valid, submit it
                        ajaxContact(form);
                        return false;
                    }
                });
            });
        </script>
        
      <?php wp_reset_query(); ?>
   	  <div class="content">
    
					<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

                    
                        <h1><?php the_title(); ?></h1>
                         
                            <div class="entry">
                            <?php the_content('<p class="serif">Read the rest of this page &raquo;</p>'); ?>
                            </div>
                                            
                        <!-- Contact form -->                                        
                        <?php if($theme->get_option('show_contact_form') == 'enable') { ?>
                        <h2><?php $theme->option('contact_form_title'); ?></h2>
                        <div id="Note"></div>
                        <div class="form">
                        <form class="cmxform" id="CommentForm" method="post" action="">
                             
                                    <label for="ContactName" class="overlabel"  style="color: #ffffff"><?php $theme->option('label_name'); ?></label>
                                    <input id="ContactName" name="ContactName" class="form_input required" />
                                                      
                           
                                    <label for="ContactEmail" class="overlabel"  style="color: #ffffff"><?php $theme->option('label_email'); ?></label>
                                    <input id="ContactEmail" name="ContactEmail" class="form_input required email" />
                             
                          
                                    <label for="ContactComment" class="overlabel" style="color: #ffffff"><?php $theme->option('label_message'); ?></label>
                                    <textarea id="ContactComment" name="ContactComment" class="form_textarea required" rows="10" cols="4"></textarea>
                     
                     
                                    <input type="submit" name="submit" class="form_submit" id="submit" value="Send" />
                                    <input class="" type="hidden" name="to" value="<?php echo $theme->get_option("contactemail");?>" />
                                    <input class="" type="hidden" name="subject" value="<?php echo $theme->get_option("contactsubject");?>" />
                                    <div id="loader" style="display:none;"><img src="<?php echo get_template_directory_uri(); ?>/images/ajax-loader.gif" alt="Loading..." id="LoadingGraphic" /></div>
                      
                        </form>
                        </div>
                        <?php } else {}?> 
  
                    <?php endwhile; endif; ?>
                    
	 <?php if($theme->get_option('show_widget_area_3') == 'enable') {
     if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Archive widget area') ) : ?>
     <h2>You can add widgets here from Admin->Appearance->Widgets <br /> Enable or Disable this widget area from Theme Options -> Widgets</h2>
    <?php endif; } else {}?>              
                    
      <div class="clear"></div>  
      </div>  


    
    
<script type="text/javascript">  
	// Contact form submit function        
	function ajaxContact(theForm) {
		var $ = jQuery;
        $('#loader').fadeIn();
        var formData = $(theForm).serialize(),
			note = $('#Note');
        $.ajax({
            type: "POST",
            url: "<?php echo get_template_directory_uri(); ?>/contact-send.php",
            data: formData,
            success: function(response) {
				if ( note.height() ) {			
					note.fadeIn('fast', function() { $(this).hide(); });
				} else {
					note.hide();
				}
				$('#LoadingGraphic').fadeOut('fast', function() {
					if (response === 'success') {
						$(theForm).animate({opacity: 0},'fast');
					}
					// Message Sent? Show the 'Thank You' message and hide the form
				result = '';
					c = '';
					if (response === 'success') { 
						result = '<?php echo $theme->get_option("contactsucces"); ?>';
						c = 'success';
					} else {
						result = response;
						c = 'error';

					}
					note.removeClass('success').removeClass('error').text('');
					var i = setInterval(function() {
						if ( !note.is(':visible') ) {

							note.html(result).addClass(c).slideDown('fast');
							clearInterval(i);
						}

					}, 40);    
				}); // end loading image fadeOut
            }
        });

        return false;
    }
</script>  
<?php get_footer(); ?>
</div>