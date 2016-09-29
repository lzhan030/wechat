<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
get_header();
?>

    <div class="header">
    <a href="<?php echo home_url( '/' ); ?>" class="left_bt">home</a>
    <span>Blog</span>
    <a href="#" class="right_bt" id="activator"><img src="<?php bloginfo('template_directory'); ?>/themedesigns/<?php echo get_option('themecolor'); ?>/images/search.png" alt="" title="" border="0" /></a>
    </div>

	<div class="content">
    	
        <?php if (get_option('show_blog_menu')=="enable"){ ?>
        <div class="toogle_wrap">
            <div class="trigger"><a href="#">Browse by category</a></div>
            <div class="toggle_container">
                    <ul>
                    	<li><a href="<?php echo get_option('home'); ?>/?cat=<?php echo get_option('blogcat');?>">all categories</a></li>
                        <?php wp_list_categories('title_li=&child_of='.get_option('blogcat')); ?>
                    </ul>
            </div>
        </div>
        <div class="shadow_wrap"></div>
        <?php } else{  } ?>
        
        <?php if (get_option('show_archive_menu')=="enable"){ ?>
        <div class="toogle_wrap">
            <div class="trigger"><a href="#">Browse by archive</a></div>
            <div class="toggle_container">
                    <ul>
                        <?php wp_get_archives(); ?>
                    </ul>
            </div>
        </div>
        <div class="shadow_wrap"></div>
        <?php } else{  } ?>

		       
		  <?php /* If this is a category archive */ if (is_category()) { ?>
            <h2 class="pagetitle"><?php single_cat_title(); ?></h2>
          <?php /* If this is a daily archive */ } elseif (is_day()) { ?>
            <h2 class="pagetitle">Archive for <?php the_time('F jS, Y'); ?></h2>
          <?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
            <h2 class="pagetitle">Archive for <?php the_time('F, Y'); ?></h2>
          <?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
            <h2 class="pagetitle">Archive for <?php the_time('Y'); ?></h2>
          <?php /* If this is an author archive */ } elseif (is_author()) { ?>
            <h2 class="pagetitle">Author Archive</h2>
          <?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
            <h2 class="pagetitle">Blog Archives</h2>
          <?php } ?>
          
        <?php if (have_posts()) : ?> 
        <?php while (have_posts()) : the_post(); 
		$parent_category=get_option('blogcat');
		if (post_is_in_descendant_category($parent_category)){
		?>
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
		<?php
		} else {
		}
		?>
        <div class="clear_left"></div>
        <?php endwhile; ?>
        
        <div class="left_nav"><?php next_posts_link('prev') ?></div>
        <div class="right_nav"><?php previous_posts_link('next') ?></div>
        
        <?php endif; ?>
        <div class="clear_left"></div>

    
    </div>
</div>
<?php get_footer(); ?>