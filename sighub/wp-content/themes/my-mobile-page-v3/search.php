<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
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
					}					?>
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
        
      <?php wp_reset_query(); ?>
      
      <div class="content">
      
        
        <div class="search">
            <form method="get" id="searchform" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <input type="text" class="search_input" value="<?php the_search_query(); ?>" name="s" id="s" onclick="this.value=''"/>
            <input type="image" src="<?php echo get_template_directory_uri(); ?>/images/search.png" class="search_submit" id="searchsubmit"/>
            </form>
            <div class="clear"></div>
        </div>
        
        <div class="toogle_wrap">
            <div class="trigger"><a href="#">Browse Categories</a></div>

            <div class="toggle_container">
                <ul class="lists">
                <?php wp_list_categories('title_li='); ?>
                </ul>
            </div>
        </div>

		<?php if (have_posts()) : ?>

 	  <?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
 	  <h2 class="pagetitle">Search Results</h2>

		<?php while (have_posts()) : the_post(); ?>
            
        <div class="post">
            <div class="post_content_single">
            <h3><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h3>
            <p>
            <?php echo blog_excerpts(); ?>
			</p>
            </div>
            <div class="clear"></div>
        </div>
   
            
		<?php endwhile; ?>

		<div class="blog_nav">
			<div class="prev"><?php next_posts_link('prev posts') ?></div>
			<div class="next"><?php previous_posts_link('next posts') ?></div>
		</div>
	<?php else : ?>

		<h2 class="center">No posts found. Try a different search?</h2>
<?php

	endif;
?>
      <div class="clear"></div>  
      </div>
	


<?php get_footer(); ?>
</div>