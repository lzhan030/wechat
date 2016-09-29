<?php
/*
Template Name: Contact
*/
get_header();
?>
<!-- form validation scripts -->
<script src="<?php bloginfo('template_url'); ?>/js/jquery.validate.min.js" type="text/javascript"></script>
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

		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
            
        <div class="header">
        <a href="<?php echo home_url( '/' ); ?>" class="left_bt">home</a>
        <span><?php the_title(); ?></span>
        <a href="#" class="right_bt" id="activator"><img src="<?php bloginfo('template_directory'); ?>/themedesigns/<?php echo get_option('themecolor'); ?>/images/search.png" alt="" title="" border="0" /></a>
        </div>
        
        <div class="content">
        
    	<div class="corner_wrap">
        
            <div class="entry">
            <?php the_content(); ?>
			</div>
      
        </div>
        <div class="shadow_wrap"></div>
            

		<?php if (get_option('show_form')=="enable"){ ?>
            <div class="toogle_wrap">
                <div class="trigger"><a href="#">Send a message</a></div>
                <div class="toggle_container">
                	<div id="Note"></div>
                    <div class="form_content">
                    <form class="cmxform" id="CommentForm" method="post" action="">
                    <label>Name:</label>
                    <input type="text" class="form_input required" id="ContactName" name="ContactName"/>
                    <label>Email:</label>
                    <input id="ContactEmail" name="ContactEmail" type="text" class="form_input required email" />
                    <label>Message:</label>
                    <textarea id="ContactComment" name="ContactComment" class="form_textarea required"></textarea>
                    <input type="submit" class="contact_submit" name="submit" id="submit" value="Submit" />
                    <input class="" type="hidden" name="to" value="<?php echo get_option("contactemail");?>" />
                    <input class="" type="hidden" name="subject" value="<?php echo get_option("contactsubject");?>" />
                    <label id="loader" style="display:none;"><img src="<?php echo bloginfo('template_url'); ?>/images/loader.gif" alt="Loading..." id="LoadingGraphic" /></label>
                    </form>
                    </div>
                </div>
            </div>   
           <div class="shadow_wrap"></div>
          <?php } else{  } ?> 

   		   <div class="clear_left"></div>

			</div>
		   <?php endwhile; endif; ?>
            
</div>         
 
 <script type="text/javascript">  
	function ajaxContact(theForm) {
		var $ = jQuery;
        $('#loader').fadeIn();
        var formData = $(theForm).serialize(),
			note = $('#Note');
        $.ajax({
            type: "POST",
            url: "<?php echo bloginfo('template_url'); ?>/contact-send.php",
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
					result = '';
					c = '';
					if (response === 'success') { 
						result = '<?php	echo "<div>Your message has been sent. Thank you!</div>";	?>';
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

	