<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
get_header();
function theme_get_post_content($postContent)
{
	$upload =wp_upload_dir();
	$baseurl=$upload['baseurl']; 
	$sp='~<img [^\>]*\ ?/?>~';
	preg_match_all( $sp, $postContent, $aPics );  
	$np = count($aPics[0]); 
	$SoImgAddress="/\<img.*?src\=\"(.*?)\"[^>]*>/i";  //正则表达式语句
	
	if ( $np > 0 ) {   
		for ( $i=0; $i < $np ; $i++ ) {  			
			$ImgUrl = $aPics[0][$i];
			preg_match($SoImgAddress,$ImgUrl,$imagesurl);
			$post_picurl=$baseurl.$imagesurl[1];
			if((stristr($imagesurl[1],"http")===false) && (stristr($imagesurl[1],'file://')===false)&&(stristr($imagesurl[1],'data:')===false)){
				$postContent=str_ireplace($imagesurl[1],$post_picurl,$postContent);
			}
		}
	}
	return $postContent;
}
?>

    <div class="header">
    <a href="<?php echo get_option('home'); ?>/?cat=<?php echo get_option('blogcat');?>" class="left_bt">back</a>
    <span>Blog</span>
    <a href="#" class="right_bt" id="activator"><img src="<?php bloginfo('template_directory'); ?>/themedesigns/<?php echo get_option('themecolor'); ?>/images/search.png" alt="" title="" border="0" /></a>
    </div> 

	<div class="content">
    
	        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>            
            <div class="corner_wrap">
            
                <div class="date">
                    <span class="month">
                    <?php $m = strtolower(get_the_time('M'));?>
                    <img src="<?php bloginfo('template_directory'); ?>/themedesigns/<?php echo get_option('themecolor'); ?>/images/date/<?php echo $m;?>.gif" alt="" title="" border="0" />
                    </span>
                    <span class="day"><?php the_time('d'); ?></span>
                    <span class="year"><?php the_time('Y'); ?></span>
                </div>
                
                <h2><?php the_title(); ?></h2>
                
                <div class="post_details">
                Posted in <?php the_category(', ') ?> | <?php comments_popup_link('0 comments', '1 comment', '% comments'); ?>
                </div>
                <div class="entry">
                <?php echo $post_content=theme_get_post_content(get_the_content());/*the_content();*/ ?>              
                </div> 
                
                <div class="clear"></div>
            </div>
            <div class="shadow_wrap"></div>
            

			<?php comments_template(); ?>
            
            <?php endwhile; else: ?>
            <p>Sorry, no posts matched your criteria.</p>
            <?php endif; ?>
            
            <div class="clear_left"></div>
            
    </div>
</div>
<?php get_footer(); ?>
