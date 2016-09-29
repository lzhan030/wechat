<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

get_header(); 
global $gweid;
$gweid =  $_GET['gweid'];
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
}?>
		
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <style>
	    $("body").css("background-img","url('')");  
	</style>
        <div class="header">
        <a href="<?php echo home_url( '/' ); ?>" class="left_bt">home</a>
        <span><?php the_title(); ?></span>
       <!-- <a href="#" class="right_bt" id="activator"><img src="<?php bloginfo('template_directory'); ?>/themedesigns/<?php echo get_option('themecolor'); ?>/images/search.png" alt="" title="" border="0" /></a>-->
        </div>
        
        <div class="content">
        
            <div class="corner_wrap">
                <div class="entry">
                <?php echo $post_content=theme_get_post_content(get_the_content());/*the_content();*/ ?>
                <div class="clear"></div>
                </div>     
            </div>
            <div class="clear_left"></div>
        
        </div>
    
    <?php endwhile; endif; ?>


</div>
<?php get_footer(); ?>
