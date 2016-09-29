<?php get_header(); ?> 
             
        <div class="entry"> 
        

                                <div id="container">
                    
                                    <?php if(have_posts()) : ?>
                                    <?php while(have_posts()) : the_post(); ?>
                    
                                        <div class="post" id="post-<?php the_ID(); ?>">

                                <?php $varimage = get_the_image(array('echo' => false)); ?>
                                <?php if ($varimage) {get_the_image(array('image_class' => 'imgpost'));} ?>

                                            <div class="title">
                                                <h2><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
                                                <div class="content"><?php the_content(); ?></div>
                                            </div>    
                                              	
                    <br clear="all" />
                                        </div>                    
                    
                                    <?php endwhile; ?>
                                    
                                       <div class="navigation">
                                            <?php posts_nav_link(' &#124; ','&#171; previous','next &#187;'); ?>
                                       </div>               
                    
                                    <?php else : ?>
                    
                                        <div class="post" id="post-<?php the_ID(); ?>">
                                            <h2><?php _e('No posts are added.'); ?></h2>
                                        </div>
                    
                                    <?php endif; ?>
                                    
                                </div>
         
        
        </div> <!--entry-->
        
  
        <?php //get_sidebar(); ?><!--remove search function-->

        <?php get_footer(); ?>        