<?php get_header(); 
global $gweid;
$gweid =  $_GET['gweid'];

require_once 'wp-content/themes/ReeooV3/wesite/common/dbaccessor.php';
require_once 'wp-content/themes/ReeooV3/wechat/common/wechat_dbaccessor.php';
if((empty($siteId))&&(empty($gweid))){
	$pageid =  $_GET['page_id'];
	$siteId = $wpdb->get_var($wpdb -> prepare( "SELECT post_content_filtered FROM {$wpdb->prefix}posts WHERE ID =%d",$pageid ));	
}

if(!empty($siteId)){
	$site=web_admin_get_site($siteId);
	foreach($site as $siteinfo){
		$userid=$siteinfo->site_user;
		$gweid=$siteinfo->GWEID;
	}
}

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
             
        <div class="entry"> 
        

                                <div id="container">
                    
                                    <?php if(have_posts()) : ?>
                                    <?php while(have_posts()) : the_post(); ?>
                    
                                        <div class="post" id="post-<?php the_ID(); ?>">


                                            <div class="title">
                                                <h2><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
                                                <div class="content"><?php echo $post_content=theme_get_post_content(get_the_content());/*the_content();*/  ?></div>
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
        
  
        <?php //get_sidebar(); ?> <!--remove search function-->

        <?php get_footer(); ?>        