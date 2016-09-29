<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
require_once 'wp-content/themes/ReeooV3/wesite/common/dbaccessor.php';
require_once 'wp-content/themes/ReeooV3/wechat/common/wechat_dbaccessor.php';
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

/**
*@author: janeen
*@version: add by janeen 20140430
*/
/**
*@function: get
*/

$gweid =  $_GET['gweid'];
$siteId = $_GET['site'];

/**
*@function:封装gweid
*/
if(!empty($siteId)){
	$site=web_admin_get_site($siteId);
	foreach($site as $siteinfo){
		$userid=$siteinfo->site_user;
		$gweid=$siteinfo->GWEID;
	}

	//20150417 sara new added
	//根据当前的gweid去查找有没有处在共享虚拟号下，如果是虚拟号下的，需要将gweid换为虚拟号的gweid
	$gweid = virtualgweid_open($gweid);
}


?>
<div id="pagecontainer">
		
	<?php global $wpdb,$table_prefix;
	$siteId ='';
    if((isset($_GET['page_id']) && !empty($_GET['page_id']))||(isset($_GET['p']) && !empty($_GET['p'])))
    {
        $table = $table_prefix.'posts';
        $ID = isset($_GET['page_id'])?$_GET['page_id']:$_GET['p'];
        $sql = "select post_content_filtered from ".$table." where ID ='".intval($ID)."'";
        $row = $wpdb->get_row($sql);
        $siteId = $row->post_content_filtered;
        //var_dump($sql);
    }
    elseif(isset($_GET['site']) && !empty($_GET['site']))
    {
        $siteId = $_GET['site'];
        //setcookie('themesDetector',$theme);
		
		//add site click 

		
		
    }?>
	    <style>
			.post_content_single img{
				max-width:100%;
			}
		</style>
    
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
					query_posts(array( 'post_type' => 'icons_menu_v3', 'post_content_filtered'=> $siteId, 'orderby' => 'menu_order', 'order' => 'ASC', 'showposts' => '999')); ?>
                    <?php $postsnr = $wp_query->found_posts; ?>
                    <?php if (have_posts()) : ?>
                    <li>
                    <?php while (have_posts()) : the_post(); ?>
					<?php if($post->post_content_filtered ==  $siteId)
				{	/*如果没有http，证明为内链，加上home_url显示，插入时，判断有再截取入库*/
					$tmp = stristr(get_post_meta($post->ID, "menu_item_url", $single = true),"http");
					$itemurl=get_post_meta($post->ID, "menu_item_url", $single = true);
					if(($tmp===false)&&(!empty($itemurl))){
						$menuiUrl=home_url().get_post_meta($post->ID, "menu_item_url", $single = true);
					}else{				
						$menuiUrl=get_post_meta($post->ID, "menu_item_url", $single = true);
					}
					
					//是该网站内的才加weid
					$needle=$_SERVER['HTTP_HOST'];
					$tmparray=stristr($menuiUrl,$needle);
					if((!empty($menuiUrl))&&($tmparray)){
						$tmp=array();
						$noinfo=false;
						$ifhaveone=stristr($menuiUrl,"?");
						$ifhavetwo=stristr($menuiUrl,"#");
						$firloc=strpos($menuiUrl,"?");
						$endloc=strpos($menuiUrl,"#");
						
						if(($ifhaveone)&&($ifhavetwo)){	//有问号有井号				
							$query=substr($menuiUrl,$firloc+1,$endloc-$firloc-1);
						}else if(($ifhaveone)&&(!$ifhavetwo)){//有问号无井号
							$query=substr($menuiUrl,$firloc+1);
						}else{//无问号有井号+无问号无井号
							$noinfo=true;						
						}
						
						if(!$noinfo){
							$kvs=explode("&",$query);
							//print_r($kvs);							
							foreach($kvs as $k=>$v){
								$tmpkv = explode("=",$v);
								$tmp= array_merge ( $tmp, array($tmpkv[0] => $tmpkv[1] ) );
							}			
							
							if(empty($tmp['gweid'])){	
								$tmp['gweid'] = $gweid;
							}
							//20140430$tmp['fromuser'] = $fromuser;
							
							$queryString = http_build_query($tmp);
							
							$las=explode("#",$menuiUrl);
							
							$paramurl=substr($menuiUrl,0,$firloc)."?".$queryString.($las[1]?"#".$las[1]:'');
							$menuiUrl=$paramurl;
						}else{
							
							if(empty($tmp['gweid'])){
								$tmp['gweid'] = $gweid;
							}
							//20140430$tmp['fromuser'] = $fromuser;
							$queryString = http_build_query($tmp);
							$las=explode("#",$menuiUrl);
							$paramurl=$menuiUrl."?".$queryString.($las[1]?"#".$las[1]:'');
							$menuiUrl=$paramurl;
						}
						
					}	//end
					
					
					
					
					
					?>
				
                    <a href="<?php echo $menuiUrl; ?>" class="icon"><?php the_post_thumbnail('menu-icon-size'); ?><span><?php the_title(); ?></span></a>
                    <?php if ($postsnr > 4 && $count == 4){ ?>
                    </li><li>
                    <?php } if ($postsnr > 8 && $count == 8){ ?>
                    </li><li>
                    <?php } ?>
                    <?php $count++;} endwhile; ?>
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


		<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
        <h1><?php the_title(); ?></h1>
        	<div class="post">
			<?php 
			if (has_post_thumbnail()) {
			
				$image_id = get_post_thumbnail_id();
				$image_url = wp_get_attachment_image_src($image_id,'large', true);
				?>
				
			
				<div class="post_thumb_single">
				<img src="<?php echo $image_url[0]; ?>" alt="" title="" border="0" class="rounded" />
				<img src="<?php echo get_template_directory_uri(); ?>/images/shadow.png" alt="" title="" border="0" class="shadow" />
				</div>
            
            <?php } else {?>            
            <?php } ?>
            <div class="post_content_single">

            <?php echo $post_content=theme_get_post_content(get_the_content());/*echo the_content();*/ ?>

            </div>
         
            <!--<span class="post_detail date"><?//php the_time('d.m') ?></span>
            <span class="post_detail category"><?//php the_category(', ') ?></span>
            <span class="post_detail comments"><?//php comments_popup_link('0', '1', '%'); ?></span>-->
        </div>
        
        <?//php comments_template(); ?>
		<!--<div id="tab3" class="tabcontent">
			<h3>Social</h3>
            <ul class="singlesocial">
<?php if ($theme->display('icon_rss')) { ?><li><a target="_blank" href="<?php $theme->option('url_rss'); ?>"><img src="<?php $theme->option('icon_rss'); ?>" alt="" title="" class="rounded-half"/></a></li><?php } ?>
<?php if ($theme->display('icon_twitter')) { ?><li><a target="_blank" href="<?php $theme->option('url_twitter'); ?>"><img src="<?php $theme->option('icon_twitter'); ?>" alt="" title="" class="rounded-half"/></a></li><?php } ?>
<?php if ($theme->display('icon_facebook')) { ?><li><a target="_blank" href="<?php $theme->option('url_facebook'); ?>"><img src="<?php $theme->option('icon_facebook'); ?>" alt="" title="" class="rounded-half"/></a></li><?php } ?>
<?php if ($theme->display('icon_digg')) { ?><li class="right"><a target="_blank" href="<?php $theme->option('url_digg'); ?>"><img src="<?php $theme->option('icon_digg'); ?>" alt="" title="" class="rounded-half"/></a></li><?php } ?>

<?php if ($theme->display('icon_google')) { ?><li><a target="_blank" href="<?php $theme->option('url_google'); ?>"><img src="<?php $theme->option('icon_google'); ?>" alt="" title="" class="rounded-half"/></a></li><?php } ?>
<?php if ($theme->display('icon_reddit')) { ?><li><a target="_blank" href="<?php $theme->option('url_reddit'); ?>"><img src="<?php $theme->option('icon_reddit'); ?>" alt="" title="" class="rounded-half"/></a></li><?php } ?>

<?php if ($theme->display('icon_flickr')) { ?><li><a target="_blank" href="<?php $theme->option('url_flickr'); ?>"><img src="<?php $theme->option('icon_flickr'); ?>" alt="" title="" class="rounded-half"/></a></li><?php } ?>
<?php if ($theme->display('icon_vimeo')) { ?><li class="right"><a target="_blank" href="<?php $theme->option('url_vimeo'); ?>"><img src="<?php $theme->option('icon_vimeo'); ?>" alt="" title="" class="rounded-half"/></a></li><?php } ?>
            
            </ul>
			<div class="clear"></div>
		</div>  --> 
            
		<?php endwhile; ?>
		<?php else : ?>
        <h2>Sorry, no posts matched your criteria.</h2>
        <?php endif; ?>

      <div class="clear"></div>  
      </div>
	


<?php get_footer(); ?>
</div>