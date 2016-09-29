<?php
/*
Template Name: Gallery
*/
get_header();
?>
    <div class="header">
    <a href="<?php echo home_url( '/' ); ?>" class="left_bt">home</a>
    <span>Photos</span>
    <a href="#" class="right_bt" id="activator"><img src="<?php bloginfo('template_directory'); ?>/themedesigns/<?php echo get_option('themecolor'); ?>/images/search.png" alt="" title="" border="0" /></a>
    </div>

    <div class="content">
		<?php query_posts(array( 'post_type' => 'gallery', 'showposts' => '9999'));?>
        <?php if (have_posts()) : ?>
        
        <span id="loading">Loading Image</span>

        <div id="thumbsWrapper">
            <div id="content">
            	<?php while (have_posts()) : the_post(); ?>

				<?php if (has_post_thumbnail( $post->ID ) ): ?>
                <?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' ); ?>
                <!--<img src="<?php bloginfo('template_directory'); ?>/scripts/timthumb.php?src=<?php echo $image[0]; ?>&h=75&w=75&zc=1" alt="<?php bloginfo('template_directory'); ?>/scripts/timthumb.php?src=<?php echo $image[0]; ?>&h=700&w=500&zc=1" title="<?php the_title(); ?>"/>--> 
                <img src="<?php echo $image[0]; ?>" alt="<?php bloginfo('template_directory'); ?>/scripts/timthumb.php?src=<?php echo $image[0]; ?>&h=700&w=500&zc=1" title="<?php the_title(); ?>"/> 
				<?php endif; ?>
                      
                <?php endwhile; ?>                          
            <div class="placeholder"></div>
            </div>
        </div>
        <div id="panel">
            <div id="wrapper">
                <a id="prev"></a>
                <a id="next"></a>
            </div>
        </div> 
    	<?php endif; ?>
    </div>
</div>    
<div id="footer">
<?php if (get_option('show_social')=="enable"){ ?>
<a href="#" class="left_bt" id="activator_share">share</a>
<?php } else{  } ?>
<span id="description"></span>
<?php if (get_option('show_top')=="enable"){ ?>
<!--<a onclick="jQuery('html, body').animate( { scrollTop: 0 }, 'slow' );"  href="javascript:void(0);" title="Go on top" class="right_bt"><img src="<?php bloginfo('template_directory'); ?>/themedesigns/<?php echo get_option('themecolor'); ?>/images/top.png" alt="" title="" border="0" /></a>-->
<?php } else{  } ?>
</div>

	