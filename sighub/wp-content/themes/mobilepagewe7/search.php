<?php
$siteId = $_SESSION['orangeSite'];
require_once 'wp-content/themes/ReeooV3/wesite/common/dbaccessor.php';
/*20140430$weid =  $_GET['weid'];
$fromuser = $_GET['fromuser'];
$mid = $_GET['mid'];
$auth = $_GET['auth'];20140430*/

/**
*@author: janeen
*@version: add by janeen 20140430
*/
$weid =  $_GET['weid'];
$mid = $_SESSION['mid'];
$auth = $_SESSION['auth'];

//2014-07-15新增修改
$gweid = $_SESSION['GWEID'];

if(empty($_GET['fromuser'])){   
	$fromuser = $_SESSION['fromuser'];
}else{ //从多图文进
    $fromuser = $_GET['fromuser'];
	$_SESSION['fromuser']=$fromuser;
}  //end

/**
*@description: get weid by site
*@author: janeen
*@version: add by janeen 20140429
*/
global $wpdb, $table_prefix;
$siteTableName=$table_prefix.'orangesite';
$usechatTableName=$table_prefix.'wechat_usechat';
if(empty($weid)){		
	$siteinfo = $wpdb->get_results( "SELECT * FROM ".$siteTableName." WHERE id='".$siteId."'");		
	foreach($siteinfo as $stinfo){
		$site_user=$stinfo->site_user;			
	}
	
	//$weidinfo = $wpdb->get_results( "SELECT * FROM ".$usechatTableName." WHERE user_id='".$site_user."'");
	//2014-07-15新增修改
	$weidinfo = $wpdb->get_results( "SELECT * FROM ".$usechatTableName." WHERE user_id='".$site_user."' AND GWEID = ".$gweid);
	foreach($weidinfo as $wdinfo){
		$wdinf=$wdinfo->WEID;			
	}			
	$weid=$wdinf;
}	//end
	$memberinfo=null;		
	if(!empty($fromuser)){		
		$memberinfo =  web_admin_member($weid, $fromuser);		
	}
	if((empty($memberinfo))&&(!empty($mid))){				
		$memberinfo =  web_admin_member_mid($mid,$weid);
		foreach($memberinfo as $minfo){
			$au_password=$minfo->password;
		}
		if($auth!= md5($mid.$au_password."weauth3647668")){
			$memberinfo=null;					
		}		
	}
/**
*@description: 
*@author: janeen
*@version: add by janeen 20140519
*/
  $isShowVipmember = getSiteMeta('mobilethemeIsShowVipmember', $siteId);
  //end
//防止会员登录成功后share该页面的跳转
if(($isShowVipmember == 'true')&&(empty($memberinfo))){//开启页面会员限制，并且不是会员
		header('Location: '.get_bloginfo('template_directory')."/../ReeooV3/wesite/common/vip_login.php?weid={$weid}&redirect_url=".urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])."#wechat_redirect");
		exit();	
}
get_header(); ?> 
 <style>
   .post img{padding:0;width:96%;height:150px;margin-left:2%;}
</style>            
        <div class="entry">        
		
                                <div id="container">
                    
            <?php if (have_posts()) : ?>

		<h2 class="search">搜索结果 "<?php echo $_GET['s']; ?>"</h2>


		    <?php while(have_posts()) : the_post(); ?>
            <?php if($post->post_content_filtered ==  $siteId){
			        $post_content_array = split("\n",get_the_content()); 
					$post_content_link = $post_content_array[0];
					$post_content_length =$post_content_array[1];?>
                <div class="post" id="post-<?php the_ID(); ?>">      
                    <!--<div class="title">
                        <h2><a href="<?php //the_permalink(); echo '&site='.$siteId; ?>" title="<?php //the_title(); ?>"><?php //the_title(); ?></a></h2>
                    </div>
                    <div class="date"><?php //if($isShowPic=='false'){  the_author();?>
                        发布于 <?php //the_time('F j, Y');}?> 
                    </div>-->
					<div class="title"><!--janeen-update-20140429-->
                        <h2><a href="<?php the_permalink(); echo '&site='.$siteId.'&weid='.$weid; ?>#wechat_redirect" title="<?php the_title(); ?>"><font><?php the_title();?> </font></a></h2>
                    </div>
					  <?php //if($isShowPic=='true'){?>					
                    <!--<div class="imgpost"><?php //mtheme_thumb_v2(); ?></div>-->
                    <?php //} ?>				
                    <?php mtheme_thumb_v2(); ?><!--Shanshan-update-20140527-->
					<div class="content">
					<a  style="color:#0C0A0A;text-decoration: none;font-size:15px;" href="<?php the_permalink(); echo '&site='.$siteId.'&weid='.$weid; ?>#wechat_redirect" title="<?php the_excerpt(); ?>"><?php the_excerpt(); echo '&nbsp;&nbsp;'; ?></a>
					<h2><a style="color:blue;margin-left:70%;" href="<?php the_permalink(); echo '&site='.$siteId.'&weid='.$weid; ?>#wechat_redirect">查看全文>></a></h2>
					</div>
					  <div class="date"><?php { the_author();?>
                        发布于 <?php the_time('F j, Y');}?> 
                    </div><!--Shanshan-update-20140527-->
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