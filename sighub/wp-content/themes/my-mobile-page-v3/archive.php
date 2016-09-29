<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
get_header();?>
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
        
      <?php wp_reset_query(); ?>
      
      <div class="content">
      
        
        <div class="search">
            <form method="get" id="searchform" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <input type="text" class="search_input" value="Search<?php the_search_query(); ?>" name="s" id="s" onclick="this.value=''"/>
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
 	  <?php /* If this is a category archive */ if (is_category()) { ?>
		<h2><?php single_cat_title(); ?></h2>
 	  <?php /* If this is a tag archive */ } elseif( is_tag() ) { ?>
		<h2>Posts Tagged &#8216;<?php single_tag_title(); ?>&#8217;</h2>
 	  <?php /* If this is a daily archive */ } elseif (is_day()) { ?>
		<h2>Archive for <?php the_time('F jS, Y'); ?></h2>
 	  <?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
		<h2>Archive for <?php the_time('F, Y'); ?></h2>
 	  <?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
		<h2>Archive for <?php the_time('Y'); ?></h2>
	  <?php /* If this is an author archive */ } elseif (is_author()) { ?>
		<h2>Author Archive</h2>
 	  <?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
		<h2>Blog Archives</h2>
 	  <?php } ?>


		<?php while (have_posts()) : the_post(); ?>
			<?php 
			if (has_post_thumbnail()) {
			
				$image_id = get_post_thumbnail_id();
				$image_url = wp_get_attachment_image_src($image_id,'large', true);
				?>
				
			<div class="post">
				<div class="post_thumb">
				<img src="<?php echo $image_url[0]; ?>" alt="" title="" border="0" class="rounded-half" />
				<img src="<?php echo get_template_directory_uri(); ?>/images/shadow.png" alt="" title="" border="0" class="shadow" />
				</div>
				<div class="post_content">
            
            <?php } else {?>
            
			<div class="post">
				<div class="post_content_single">
            
            <?php } ?>
            <h3><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h3>
            <p>
            <?php 
				$str = blog_excerpts(); 
				if(strlen($str)>200)
				{
					$str = substr($str,0,200)."...";
				}
				ECHO $str;
			?>
			</p>
            </div>
            <div class="clear"></div>
            <span class="post_detail date"><?php the_time('m.d') ?></span>
            <span class="post_detail category"><?php the_category(', ') ?></span>
            <span class="post_detail comments"><?php comments_popup_link('0', '1', '%'); ?></span>
        </div>
            
            
            
            
            
            
		<?php endwhile; ?>

		<div class="blog_nav">
			<div class="prev"><?php next_posts_link('prev posts') ?></div>
			<div class="next"><?php previous_posts_link('next posts') ?></div>
		</div>
	<?php else :

		if ( is_category() ) { // If this is a category archive
			printf("<h2 class='center'>Sorry, but there aren't any posts in the %s category yet.</h2>", single_cat_title('',false));
		} else if ( is_date() ) { // If this is a date archive
			echo("<h2>Sorry, but there aren't any posts with this date.</h2>");
		} else if ( is_author() ) { // If this is a category archive
			$userdata = get_userdatabylogin(get_query_var('author_name'));
			printf("<h2 class='center'>Sorry, but there aren't any posts by %s yet.</h2>", $userdata->display_name);
		} else {
			echo("<h2 class='center'>No posts found.</h2>");
		}


	endif;
?>

	 <?php if($theme->get_option('show_widget_area_1') == 'enable') {
     if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Archive widget area') ) : ?>
     <h2>You can add widgets here from Admin->Appearance->Widgets <br /> Enable or Disable this widget area from Theme Options -> Widgets</h2>
    <?php endif; } else {}?>

      <div class="clear"></div>  
      </div>
	
 

<?php get_footer(); ?>
</div>