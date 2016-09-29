<?php
$siteId = $_SESSION['orangeSite'];
require_once 'wp-content/themes/ReeooV3/wesite/common/dbaccessor.php';

/**
*@function: get
*/
$gweid =  $_GET['gweid'];
$gweidtrue =  $_GET['gweid'];
$mid =$_SESSION['gmid'][intval($gweid)]['mid'];
$auth =	$_SESSION['gmid'][intval($gweid)]['auth'];


/**
*@function:封装gweid
*/
if(!empty($siteId)){
	$site=web_admin_get_site($siteId);
	foreach($site as $siteinfo){
		$userid=$siteinfo->site_user;
		$gweid=$siteinfo->GWEID;
		$gweidtrue=$siteinfo->GWEID;
	}

	//20150417 sara new added
	//根据当前的gweid去查找有没有处在共享虚拟号下，如果是虚拟号下的，需要将gweid换为虚拟号的gweid
	$gweid = virtualgweid_open($gweid);
	
	$mid =$_SESSION['gmid'][intval($gweid)]['mid'];
	$auth =	$_SESSION['gmid'][intval($gweid)]['auth'];
}

/**
*@function:判断会员是否审核
*/
$vipauditinfo=web_admin_usechat_info_group($gweid);
foreach($vipauditinfo as $vaudit){
	$vipaudit=$vaudit->wechat_vipaudit;
}
/*获取fromuser*/
$fromuser=$_SESSION['gopenid'][intval($gweid)];
$weid =  $_SESSION['weid'][intval($gweid)];
$result = web_user_display_index_groupnew_wesforsel($gweid);
foreach($result as $initfunc){
	if($selCheck[$initfunc->func_name] == 0)
		$selCheck[$initfunc->func_name] = $initfunc->status;
}
$isShowVipmember = getSiteMeta('mobilethemeIsShowVipmember', $siteId);
if((isset($_SERVER['HTTP_USER_AGENT']))&&(strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false)&&($selCheck['wechatvip']==1)&&($isShowVipmember == 'true')){
	/*认证服务号则通过oauth2.0获取fromuser*/
	require_once 'wp-content/themes/ReeooV3/wesite/common/common_oauth.php';
	
}
	/**
	*@function:通过fromuser拿到会员信息
	*/
	$memberinfo=null;
	$memberinfo_wgroup=null;
	/*if((!empty($fromuser))&&(!empty($weid))){		
		//20140624 janeen update
		//$memberinfo =  web_admin_member($weid, $fromuser);
		$memberinfo_wgroup =  web_admin_member_wgroup($weid, $fromuser);				
	}else */ if((!empty($fromuser))&&(!empty($weid))&&(!empty($gweid))){		
				$memberinfo_wgroup =  web_admin_member_wgroup($weid,$gweid,$fromuser);						
	}
	if(!empty($memberinfo_wgroup)){
		foreach($memberinfo_wgroup as $minfo_wgroup){
			$mid=$minfo_wgroup->mid;
		}
	  //$memberinfo =  web_admin_member_mid($mid,$weid);
		$memberinfo =  web_admin_member_mid_group($mid,$gweid);
		foreach($memberinfo as $minfo){
			$isaudit=$minfo->isaudit;
		}
	}else{
		$memberinfo=null;
	}
	/**
	*@function:已经登陆通过mid拿到会员信息
	*/
	if((empty($memberinfo))&&(!empty($mid))){				
		//$memberinfo =  web_admin_member_mid($mid,$weid);
		$memberinfo =  web_admin_member_mid_group($mid,$gweid);
		foreach($memberinfo as $minfo){
			$au_password=$minfo->password;
			$isaudit=$minfo->isaudit;
		}
		if($auth!= md5($mid.$au_password."weauth3647668")){
			$memberinfo=null;
			unset($_SESSION['gmid'][intval($gweid)]);
		}		
	}
	
 
 
//防止会员登录成功后share该页面的跳转
if(($selCheck['wechatvip']==1)&&(($isShowVipmember == 'true')&&(empty($memberinfo)))){//开启页面会员限制，并且不是会员
		header('Location: '.get_bloginfo('template_directory')."/../ReeooV3/wesite/common/vip_login.php?gweid={$gweid}&redirect_url=".urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])."#wechat_redirect");
		exit();	
}
if(($selCheck['wechatvip']==1)&&(($isShowVipmember == 'true')&&((!empty($memberinfo))&&($vipaudit=='1')&&(($isaudit=='2')||($isaudit=='0'))))){
		header('Location: '.get_bloginfo('template_directory')."/../ReeooV3/wesite/common/vip_perdenied.php?gweid={$gweid}&isaudit={$isaudit}&redirect_url=".urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])."#wechat_redirect");
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
                        <h2><a href="<?php the_permalink(); echo '&site='.$siteId.'&gweid='.$gweidtrue; ?>#wechat_redirect" title="<?php the_title(); ?>"><font><?php the_title();?> </font></a></h2>
                    </div>
					  <?php //if($isShowPic=='true'){?>					
                    <!--<div class="imgpost"><?php //mtheme_thumb_v2(); ?></div>-->
                    <?php //} ?>				
                    <?php mtheme_thumb_v2(); ?><!--Shanshan-update-20140527-->
					<div class="content">
					<a  style="color:#0C0A0A;text-decoration: none;font-size:15px;" href="<?php the_permalink(); echo '&site='.$siteId.'&gweid='.$gweidtrue; ?>#wechat_redirect" title="<?php the_excerpt(); ?>"><?php the_excerpt(); echo '&nbsp;&nbsp;'; ?></a>
					<h2><a style="color:blue;margin-left:70%;" href="<?php the_permalink(); echo '&site='.$siteId.'&gweid='.$gweidtrue; ?>#wechat_redirect">查看全文>></a></h2>
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