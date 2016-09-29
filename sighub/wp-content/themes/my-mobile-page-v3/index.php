<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
session_start();
require_once 'wp-content/themes/ReeooV3/wesite/common/dbaccessor.php';
require_once 'wp-content/themes/ReeooV3/wechat/common/wechat_dbaccessor.php';
get_header(); 

/**
*@author: janeen
*@version: add by janeen 20140430
*/
/**
*@function: get
*/
global $gweid;

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
<div id="container">

	<?php
		global $wpdb, $table_prefix;
		$siteId  = $_GET['site'];
	?>

	<?php if ($theme->display('logo')) { ?> 
        <div class="logo-image"><img src="<?php $theme->option('logo'); ?>" alt="<?php bloginfo('name'); ?>" title="<?php bloginfo('name'); ?>" /></div>
    <?php } elseif ($theme->display('maintitle')) { ?> 
        <div class="logo-title"><?php $theme->option('maintitle'); ?></div>
    <?php } ?> 

    <div id="main_panels">
        <div class="panels_slider">
        <ul class="slides">
			<?php query_posts(array( 'post_type' => 'slider', 'post_content_filtered'=> $siteId, 'orderby' => 'ID', 'showposts' => '9999'));?>
            <?php if (have_posts()) : ?>
			

                    <?php while (have_posts()) : the_post(); 
					 if($post->post_content_filtered == $siteId)
					 {?>
    
                    <?php if (has_post_thumbnail( $post->ID ) ): ?>
                    <?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' ); ?>
                    <li>
                    <!--<a href="<?php echo get_post_meta($post->ID, "slider_item_url", $single = true); ?>" class="icon">-->
                   <img src="<?php echo $image[0]; ?>" alt="<?php the_title(); ?>" title="<?php the_title(); ?>" vspace="60" width="100%" height="640px" />
                   
		
                    </li>
                    
					<?php endif; ?>
                    <?php } ?>      
                    <?php endwhile; ?>                          

            <?php endif; ?>
        </ul>
        </div>
    </div>

    <div id="bottom_nav">
        <div class="icons_nav">
           <?php if($theme->get_option('icons_arrows') == 'enable') { ?> <div class="paginated"> <?php } ?> 
            <ul class="slides">
                
					<?php
					$count = 1;
					query_posts(array( 'post_type' => 'icons_menu_v3', 'post_content_filtered'=> $siteId, 'orderby' => 'ID', 'order' => 'DESC', 'showposts' => '999')); ?>
                    <?php $postsnr = $wp_query->found_posts; ?>
                    <?php if (have_posts()) : ?>
                    <li>
                    <?php while (have_posts()) : the_post(); ?>
					
					<?php if($post->post_content_filtered ==  $siteId)
                 {?>
                    
					<?php
					/**
					*@description: add weid
					*@author: janeen
					*@version: add by janeen 20140430
					*/
					$menuiUrl=get_post_meta($post->ID, "menu_item_url", $single = true);
					/*如果没有http，证明为内链，加上home_url显示，插入时，判断有再截取入库*/
					$tmp = stristr(get_post_meta($post->ID, "menu_item_url", $single = true),"http");
					$posturl=get_post_meta($post->ID, "menu_item_url", $single = true);
					if(($tmp===false)&&(!empty($posturl))){
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
					
					<a href="<?php echo  $menuiUrl?>"><?php the_post_thumbnail('menu-icon-size'); ?><span><?php the_title(); ?></span></a>
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
    
</div>
<?php $theme->option('analytics_code'); ?>
<?php get_footer(); ?>
