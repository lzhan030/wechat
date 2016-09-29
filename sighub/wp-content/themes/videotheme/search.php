<?php 
$siteId = $_SESSION['orangeSite'];
get_header(); ?> 
             
        <div class="entry"> 
        

                                <div id="container">
                    
            <?php if (have_posts()) : ?>

		<h2 class="search">"<?php echo $_GET['s']; ?>"相关的搜索结果：</h2>


		<?php while(have_posts()) : the_post(); ?>
            <?php if($post->post_content_filtered ==  $siteId){ 
					$post_content_array = split("\n",get_the_content()); 
					$post_content_link = $post_content_array[0];
					$post_content_length =$post_content_array[1];?>
                <div class="post" id="post-<?php the_ID(); ?>">
                    <?php if($isShowPic){ ?>
                    <div class="imgpost"><?php mtheme_thumb(); ?></div>
                    <?php } ?>
                    <div class="title">
                        <h2><a href="<?php echo $post_content_link; ?>" title="<?php the_title(); ?>"><font><?php the_title();?> </font></a></h2>
                    </div>
					
					<div class="date"><?php echo $post_content_length;?>       <?php if($isShowPic){ the_author();}?>
                        发布于 <?php the_time('F j, Y') ?>
                    </div>

                    <br clear="all" />
                </div>

            <?php } endwhile; ?>
       <!-- <div class="navigation">
			<div class="goleft"><?php next_posts_link('&laquo; Older Entries') ?></div>
			<div class="goright"><?php previous_posts_link('Newer Entries &raquo;') ?></div>
            <div class="clear"></div>
		</div>             
                    
                                    <?php else : ?>
                    
                                        <div class="post" id="post-<?php the_ID(); ?>">
                                            <h2>Your search for "<?php echo $_GET['s']; ?>" returned no results! </h2>
                                        </div>
                    
                                    <?php endif; ?>
                                    
                                </div>
         
        
        </div>--> <!--entry-->
        
  
        <?php //get_sidebar(); ?>

        <?php get_footer(); ?>        