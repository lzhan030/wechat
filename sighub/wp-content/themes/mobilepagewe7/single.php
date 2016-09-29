<?php 
global $gweid ,$wpdb;
$gweid =  $_GET['gweid'];
$siteId = $_GET['site'];
/**
*@function:封装gweid
*/
require_once 'wp-content/themes/ReeooV3/wesite/common/dbaccessor.php';
require_once 'wp-content/themes/ReeooV3/wechat/common/wechat_dbaccessor.php';
if((empty($siteId))&&(empty($gweid))){
$pageid =  $_GET['page_id'];
$siteId = $wpdb->get_var($wpdb -> prepare( "SELECT post_content_filtered FROM {$wpdb->prefix}posts WHERE ID =%d",$pageid ));	
}
echo $wpdb -> prepare( "SELECT post_content_filtered FROM {$wpdb->prefix}posts WHERE ID =%d",$pageid );
echo "这是".$pageid ;
echo "这是".$siteId ;
exit;
if(!empty($siteId)){
	$site=web_admin_get_site($siteId);
	foreach($site as $siteinfo){
		$userid=$siteinfo->site_user;
		$gweid=$siteinfo->GWEID;
	}

    //20150420 sara new added
    //根据当前的gweid去查找有没有处在共享虚拟号下，如果是虚拟号下的，需要将gweid换为虚拟号的gweid
    $gweid = virtualgweid_open($gweid);
}

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
}?> 
        <div class="entry"> 
        

                                <div id="container">
                    
                                    <?php if(have_posts()) : ?>
                                    <?php while(have_posts()) : the_post(); ?>
                    
                                        <div class="post" id="post-<?php the_ID(); ?>">


                                            <div class="title">
                                                <h2 style="text-align:center;"><a href="<?php the_permalink(); $siteId = $_SESSION['orangeSite'];echo '&site='.$siteId; ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
                                                <div class="content"><?php echo $post_content=theme_get_post_content(get_the_content());/*the_content();*/ ?></div>
                                            </div>    
                                            
                                            <div class="date">
                                             <?php the_author();?>发布于 <?php the_time('F j, Y') ?>
                                            </div>     
                                              	
                    <br clear="all" />
                     <?php
                     global $wpdb, $table_prefix;
                     $tableName = $table_prefix.'orangesitemeta';
                     //$siteId = ($_GET['site'] == ''||$_GET['site']==null)? '1': $_GET['site'];
                     $siteId = $_SESSION['orangeSite'];//$siteId  = $_COOKIE['orangeSite'];
                     $keyName = 'mobilethemeIsShowEditor';
                     $sql = "SELECT `site_value` FROM `".$tableName."` WHERE `site_id`='".$siteId."' and `site_key`='".$keyName."'";
                     $sitemeta = $wpdb->get_row($sql);
                     $isShowEditor = $sitemeta->site_value;
                     if(empty($isShowEditor))
                     {
                        // $isShowEditor  = 'true';
                         //$wpdb->query("INSERT INTO `".$tableName."`( `site_id`, `site_key`,`site_value`) VALUE('".$siteId."', '".$keyName."','".$isShowEditor."')");
                     }
                     ?>
                    <?php if($isShowEditor == 'true')comments_template(); ?>
                                        </div>                    
                    
                                    <?php endwhile; ?>
                   
                                    <?php endif; ?>
                                    
                                </div>
         
        
        </div> <!--entry-->
        
  
        <?php //get_sidebar(); ?><!--remove search function-->

        <?php get_footer(); ?>        