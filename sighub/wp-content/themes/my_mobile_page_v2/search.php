<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
get_header();
global $gweid;
?>
    <div class="header">
    <a href="<?php echo home_url( '/' ); ?>" class="left_bt">home</a>
    <span>Search</span>
    <a href="#" class="right_bt" id="activator"><img src="<?php bloginfo('template_directory'); ?>/themedesigns/<?php echo get_option('themecolor'); ?>/images/search.png" alt="" title="" border="0" /></a>
    </div>
    
    <div class="content">
        
        <?php if (have_posts()) : ?> 
        
        <h2 class="pagetitle">Search Results</h2>
        
        <?php while (have_posts()) : the_post(); ?>
        <div class="corner_wrap">
        
            <div class="date">
                <span class="month">
                <?php $m = strtolower(get_the_time('M'));?>
                <img src="<?php bloginfo('template_directory'); ?>/themedesigns/<?php echo get_option('themecolor'); ?>/images/date/<?php echo $m;?>.gif" alt="" title="" border="0" />
                </span>
                <span class="day"><?php the_time('d'); ?></span>
                <span class="year"><?php the_time('Y'); ?></span>
            </div>
            
            <h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
            
            <div class="clear"></div>
        </div>
        <div class="shadow_wrap"></div>
        <div class="clear_left"></div>
        <?php endwhile; ?>
        
        <div class="left_nav"><?php next_posts_link('prev') ?></div>
        <div class="right_nav"><?php previous_posts_link('next') ?></div>
	    <?php else : ?>

		<h2 class="pagetitle">No posts found. Try a different search!</h2>

        <?php endif; ?>
        <div class="clear_left"></div>
    </div>
</div>
<?php get_footer(); ?>
