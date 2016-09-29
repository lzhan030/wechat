<?php 
require_once 'wp-content/themes/ReeooV3/wesite/common/dbaccessor.php';
/**
*@function: get
*/
$gweid =  $_GET['gweid'];
$gweidtrue =  $_GET['gweid'];
$mid =$_SESSION['gmid'][intval($gweid)]['mid'];
$auth =	$_SESSION['gmid'][intval($gweid)]['auth'];
$siteId =  $_GET['site'];
if(empty($siteId)){
	$siteId = $_SESSION['orangeSite'];
}else{
	 $_SESSION['orangeSite']= $siteId;
}

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

	//20150420 sara new added
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
$isShowVipmember_editor = getSiteMeta('mobilethemeIsShowVipmemberEditor', $siteId);
if((isset($_SERVER['HTTP_USER_AGENT']))&&(strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false)&&($selCheck['wechatvip']==1)&&(($isShowVipmember == 'true')||($isShowVipmember_editor == 'true'))){
	/*认证服务号则通过oauth2.0获取fromuser*/
	require_once 'wp-content/themes/ReeooV3/wesite/common/common_oauth.php';
	
}





/**
*@function:通过fromuser拿到会员信息
*/
$countmember=false;
$memberinfo_wgroup=null;
if((!empty($fromuser))&&(!empty($weid))&&(!empty($gweid))){
	
	$countnumber = web_admin_member_count_wgroup($weid,$gweid,$fromuser);
	$memberinfo_wgroup =  web_admin_member_wgroup($weid,$gweid,$fromuser);		
}
if(!empty($memberinfo_wgroup)){
	foreach($memberinfo_wgroup as $minfo_wgroup){
		$mid=$minfo_wgroup->mid;
	}
	$memberinfo =  web_admin_member_mid_group($mid,$gweid);
	foreach($memberinfo as $minfo){
		$isaudit=$minfo->isaudit;
	}
}else{
	$memberinfo=null;
}
if(!empty($countnumber)){		
	foreach($countnumber as $memberNumber){
		$countmember=$memberNumber->memberCount;
	}
}
if($countmember==0){
	$countmember=false;
}
/**
*@function:已经登陆通过mid拿到会员信息
*/
if((!$countmember)&&(!empty($mid))){
	$countnumber = web_admin_member_count_mid($mid);
	$memberinfo =  web_admin_member_mid_group($mid,$gweid);
	foreach($memberinfo as $minfo){
		$au_password=$minfo->password;
		$isaudit=$minfo->isaudit;
	}
	if($auth!= md5($mid.$au_password."weauth3647668")){
		$memberinfo=null;
		$countmember=false;
		unset($_SESSION['gmid'][intval($gweid)]);
	}else{
		foreach($countnumber as $memberNumber){
			$countmember=$memberNumber->memberCount;
		}
	}	
}

	
if(!empty($memberinfo))	{
	foreach($memberinfo as $member){
		$realname = $member->realname;
		$nickname = $member->nickname;
		$email = $member->email;
		$isaudit=$member->isaudit;
	}
}


//防止会员登录成功后share该页面的跳转
if(($selCheck['wechatvip']==1)&&(($isShowVipmember == 'true')&&(!$countmember))){//开启页面会员限制，并且不是会员
		header('Location: '.get_bloginfo('template_directory')."/../ReeooV3/wesite/common/vip_login.php?gweid={$gweid}&redirect_url=".urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])."#wechat_redirect");
		exit();	
}
if(($selCheck['wechatvip']==1)&&(($isShowVipmember == 'true')&&($countmember)&&($vipaudit=='1')&&(($isaudit=='2')||($isaudit=='0')))){
		header('Location: '.get_bloginfo('template_directory')."/../ReeooV3/wesite/common/vip_perdenied.php?gweid={$gweid}&isaudit={$isaudit}&redirect_url=".urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])."#wechat_redirect");
		exit();	
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
}

?> 
             
        <div class="entry"> 
        

                                <div id="container">
                    
                                    <?php if(have_posts()) : ?>
                                    <?php while(have_posts()) : the_post(); ?>
                    
                                        <div class="post" id="post-<?php the_ID(); ?>">
											<?php //$post_content=theme_get_post_content(the_content());
											?>

                                            <div class="title">
                                                <h2><a href="<?php the_permalink(); $siteId = $_SESSION['orangeSite'];echo '&site='.$siteId; ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
                                                <div class="content"><?php echo $post_content=theme_get_post_content(get_the_content());/*the_content();*/ ?></div>
                                            </div>    
                                            
                                            <div class="date">
                                               <?php the_author(); ?> 发布于 <?php the_time('F j, Y') ?>
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
        
  
        <?php //get_sidebar(); ?> <!--去掉搜索框-->

        <?php get_footer(); ?>        