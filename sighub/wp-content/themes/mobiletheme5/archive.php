<?php get_header(); ?> 
             
        <div class="entry"> 
        

                                <div id="container">
                    
            <?php if (have_posts()) : ?>

 	  <?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
 	  <?php /* If this is a category archive */ if (is_category()) { ?>
		<h2 class="search">Archive for the &#8216;<?php single_cat_title(); ?>&#8217; Category:</h2>
 	  <?php /* If this is a tag archive */ } elseif( is_tag() ) { ?>
		<h2 class="search">Posts Tagged &#8216;<?php single_tag_title(); ?>&#8217;</h2>
 	  <?php /* If this is a daily archive */ } elseif (is_day()) { ?>
		<h2 class="search">Archive for <?php the_time('F jS, Y'); ?>:</h2>
 	  <?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
		<h2 class="search">Archive for <?php the_time('F, Y'); ?>:</h2>
 	  <?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
		<h2 class="search">Archive for <?php the_time('Y'); ?>:</h2>
	  <?php /* If this is an author archive */ } elseif (is_author()) { ?>
		<h2 class="search">Author Archive</h2>
 	  <?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
		<h2 class="search">Blog Archives</h2>
 	  <?php } ?>

		<?php while (have_posts()) : the_post(); ?>
                    
                                        <div class="post" id="post-<?php the_ID(); ?>">

                                   <div class="imgpost"><?php mtheme_thumb(); ?></div>

                                            <div class="title">
                                                <h2><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
                                            </div>   
                                                                
                                            <div class="date">
                                                发布于 <?php the_time('F j, Y') ?>
                                            </div>     
                                              	
                    <br clear="all" />
                                        </div>                    
                    
                                    <?php endwhile; ?>
                                    
        <!--<div class="navigation">
			<div class="goleft"><?php next_posts_link('&laquo; Older Entries') ?></div>
			<div class="goright"><?php previous_posts_link('Newer Entries &raquo;') ?></div>
            <div class="clear"></div>
		</div>             
                    
                                    <?php else : ?>
                    
                                        <div class="post" id="post-<?php the_ID(); ?>">
                                            <h2><?php _e('No posts are added.'); ?></h2>
                                        </div>
                    
                                    <?php endif; ?>
                                    
                                </div>
         
        
        </div>--> <!--entry-->
        
  
        <?php //get_sidebar(); ?>

        <?php get_footer(); ?>        