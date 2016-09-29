<?php
session_start();
require_once 'wp-content/themes/ReeooV3/wesite/common/dbaccessor.php';
/**
*@function: get
*/
global $gweid ;
$gweid =  $_GET['gweid'];
$gweidtrue =  $_GET['gweid'];
$siteId = $_GET['site'];
$_SESSION['orangeSite']=$siteId;
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


if(1)
 {
     if(empty($siteId)){
		$siteId = $_SESSION['orangeSite'];//$siteId  = $_COOKIE['orangeSite'];	
	}
	 $isShowPic = getSiteMeta('mobilethemeIsShowPic', $siteId);
	 $isShowEditor = getSiteMeta('mobilethemeIsShowEditor', $siteId);
	 $isShowVipmember = getSiteMeta('mobilethemeIsShowVipmember', $siteId);
	 $isShowVipmember_editor = getSiteMeta('mobilethemeIsShowVipmemberEditor', $siteId);
	 $useContact = getSiteMeta('mobilethemeContact', $siteId);	
	
	$result = web_user_display_index_groupnew_wesforsel($gweid);
	foreach($result as $initfunc){
		if($selCheck[$initfunc->func_name] == 0)
			$selCheck[$initfunc->func_name] = $initfunc->status;
	}
	if((isset($_SERVER['HTTP_USER_AGENT']))&&(strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false)&&($selCheck['wechatvip']==1)&&(($isShowVipmember == 'true')||($isShowVipmember_editor == 'true'))){
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
	}else*/ if((!empty($fromuser))&&(!empty($weid))&&(!empty($gweid))){		
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
	
	
	if(($selCheck['wechatvip']==1)&&(($isShowVipmember == 'true')&&(empty($memberinfo)))){//开启页面会员限制，并且不是会员
		header('Location: '.get_bloginfo('template_directory')."/../ReeooV3/wesite/common/vip_login.php?gweid={$gweid}&redirect_url=".urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])."#wechat_redirect");
		exit();	
	}
	if(($selCheck['wechatvip']==1)&&(($isShowVipmember == 'true')&&((!empty($memberinfo))&&($vipaudit=='1')&&(($isaudit=='2')||($isaudit=='0'))))){//开启页面会员限制，没有通过审核
		header('Location: '.get_bloginfo('template_directory')."/../ReeooV3/wesite/common/vip_perdenied.php?gweid={$gweid}&isaudit={$isaudit}&redirect_url=".urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])."#wechat_redirect");
		exit();	
	}
	if(!empty($memberinfo)){
		foreach($memberinfo as $member){
			$realname = $member->realname;
			$nickname = $member->nickname;	
		}
	}

 ?>

<?php 


get_header();?>	

<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html>
	<head>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/../ReeooV3/we7/script/jquery-1.7.2.min.js"></script>
<!--<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/../ReeooV3/we7/script/common.mobile.js.js?v=<?php echo TIMESTAMP;?>"></script>-->
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/../ReeooV3/we7/script/jquery.form.js"></script>
<script type="text/javascript">
		$(function(){
			isSubmitting = false;
			var actionparm="mobiletheme";
			var ajax_option={			
				url:"<?php bloginfo('template_directory'); ?>/mobile_post.php?action="+actionparm+"&gweid=<?php echo $gweidtrue;?>"+"&siteId=<?php echo $siteId;?>"+"&redirect_url=<?php echo urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])?>#wechat_redirect",
				success: function(data){
					if (data.status == 'insertsuc'){
						location.reload();
					}else if (data.status == 'success'){
						alert(data.message);						
						location.href="<?php bloginfo('template_directory'); ?>"+data.url;
					}else if (data.status == 'error'){
						alert(data.message);
					}else{
						alert("出现错误");
					}
					isSubmitting = false;
				},
		       error: function(data){
					alert("出现错误了");
					isSubmitting = false;
				},
				dataType: 'json'
			}
			$('#commentform').submit(function(){
				if(isSubmitting)
				return false;
				isSubmitting = true;
				$(this).ajaxSubmit(ajax_option);
				
				return false;
			});
		});
</script>
<style>
   .post img{padding:0;width:96%;height:150px;margin-left:2%;}
</style>
</head>
</html>
	
<div class="entry">
<?php if(($useContact !=null) && (strlen($useContact)>0)) { ?>
	<div class="post">
		<U><a href="tel:<?php echo $useContact ?>">点此预定,联系我们</a></U>
		<br clear="all" />
	</div>
<?php };?>	
    <div id="container">
        <?php query_posts(array( 'post_type' => 'post','post_content_filtered'=> $siteId, 'orderby' => 'date', 'order' => 'DESC', 'showposts' => '999'));?>
        <?php if(have_posts()) : ?>
		<?php 
		//
		global $wpdb, $table_prefix;
		$tableName = $table_prefix.'orangesitemeta';
		$siteId = $_GET['site'];
		$keyName = 'mobilethemeSize';
		$sql = "SELECT `site_value` FROM `".$tableName."` WHERE `site_id`='".$siteId."' and `site_key`='".$keyName."'";
		$sitemeta = $wpdb->get_row($sql);
		$postCount = $sitemeta->site_value;
		$tablecount = $table_prefix.'posts';
		$keypost = 'post';
		$sql = "SELECT count(*) as count_p FROM  $wpdb->posts WHERE `post_content_filtered`='".$siteId."' and `post_type`='".$keypost."'";
		$post = $wpdb->get_row($sql);
		$postC = $post->count_p;
		//$postC = count($post);
		//echo $postC;
		if($postCount <= 0)
			$postCount = 5;	
		$haveView = 0;
		if(isset($_GET['haveview']))
			$haveView = $_GET['haveview'];
		$i=$haveView;
		$count = 0; ?>
		
            <?php while(have_posts()) : the_post(); 
			?>
            <?php if($post->post_content_filtered ==  $siteId){
					$i--;
					if($i>=0)
						continue;
					$count++;
					if($count>$postCount)
							break;
			?>
                <div <?php post_class(); ?> class="post" id="post-<?php the_ID(); ?>">
                    <div class="title">
                        <!--20140430<h2><a href="<?php the_permalink(); echo '&site='.$siteId.'&gweid='.$gweidtrue.'&fromuser='.$fromuser.'&mid='.$mid.'&auth='.$auth; ?>" title="<?php the_title(); ?>"><?php the_title(); if( is_sticky() ) echo '&nbsp;&nbsp;<span style="color:red;">置顶</span>'; ?></a></h2>-->
						<h2 style="text-align:center;"><a href="<?php the_permalink(); echo '&site='.$siteId.'&gweid='.$gweidtrue; ?>#wechat_redirect" title="<?php the_title(); ?>"><?php the_title(); if( is_sticky() ) echo '&nbsp;&nbsp;<span style="color:red;">置顶</span>'; ?></a></h2>
                    </div>
					<?php mtheme_thumb_v2(); ?>
					<div class="content">
					<a style="color:#0C0A0A;text-decoration: none;font-size:15px;" href="<?php the_permalink(); echo '&site='.$siteId.'&gweid='.$gweidtrue; ?>#wechat_redirect" title="<?php the_excerpt();?>"><?php the_excerpt();?></a>
					<h2><a style="color:blue;margin-left:70%;" href="<?php the_permalink(); echo '&site='.$siteId.'&gweid='.$gweidtrue; ?>#wechat_redirect">查看全文>></a></h2>
					</div>
					<!--<h2><a href="<?php the_permalink(); echo '&site='.$siteId.'&gweid='.$gweidtrue; ?>#wechat_redirect"><?php echo '<span style="color:blue;margin-left:75%;">查看全文>></span>'; ?></a></h2>-->
					<!--<div class="title">
						<h2><a href="<?php the_permalink(); echo '&site='.$siteId.'&gweid='.$gweidtrue; ?>#wechat_redirect" title="<?php the_excerpt(); ?>"><?php the_excerpt(); ?></a></h2>
                    </div>-->
                    <div class="date"><?php { the_author();?>
                        发布于 <?php the_time('F j, Y');}?> 
                    </div>

                    <br clear="all" />
                </div>

            <?php } endwhile; ?>

            <div class="navigation">
			<?php 
				$url = curPageURL();
				if(stristr($url,"haveview=")){
					$url=substr( $url,0,strripos($url,'&haveview='));
				}
			?>
			<?php if($haveView>0) { 
					if($haveView>=$postC-$postCount){?>
					<div class="goleft"><a href="<?php echo $url.'&haveview='.($haveView-$postCount)?>">上一页</a></div>
					<?php } else{ ?>
					<div class="goleft"><a href="<?php echo $url.'&haveview='.($haveView-$postCount)?>">上一页</a></div>
					<div class="goright"><a href="<?php echo $url.'&haveview='.($haveView+$postCount) ?>">下一页</a></div>
					<?PHP } } ?>
			<?php if($haveView<= 0 && $postC-$postCount > 0) {?>
					<div class="goright"><a href="<?php echo $url.'&haveview='.($haveView+$postCount) ?>">下一页</a></div>
				<?php } ?>
                <div class="clear"></div>
			<?php  ?>
            </div>

        <?php else : ?>

            <div class="post" id="post-<?php the_ID(); ?>">
                <h2><?php _e('No posts are added.'); ?></h2>
            </div>

        <?php endif; ?>

    </div>


</div> <!--entry-->
<br/><br/>
        
  
        <?php //get_sidebar(); ?><!--remove search function-->
		
    

        <?php get_footer();    }
            else
                echo '请先激活必要的插件';
        ?>