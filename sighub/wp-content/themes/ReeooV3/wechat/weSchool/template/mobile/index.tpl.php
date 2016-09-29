<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
<?php 
$upload =wp_upload_dir();

foreach($select_url as $url){
		$bgimg_url=$url['bg_url'];
		if((empty($bgimg_url))||(stristr($bgimg_url,"http")!==false)){
			$bgimgurl=$bgimg_url;
		}else{
			$bgimgurl=$upload['baseurl'].$bgimg_url;
		}
		$videopic_url=$url['videopic_url'];
		if((empty($videopic_url))||(stristr($videopic_url,"http")!==false)){
			$videopicurl=$videopic_url;
		}else{
			$videopicurl=$upload['baseurl'].$videopic_url;
		}
		
		$homework_url=$url['homework_url'];
		if((empty($homework_url))||(stristr($homework_url,"http")!==false)){
			$homeworkurl=$homework_url;
		}else{
			$homeworkurl=$upload['baseurl'].$homework_url;
		}
		
		$notice_url=$url['notice_url'];
		if((empty($notice_url))||(stristr($notice_url,"http")!==false)){
			$noticeurl=$notice_url;
		}else{
			$noticeurl=$upload['baseurl'].$notice_url;
		}
		
} 
$picone=home_url()."/wp-content/themes/ReeooV3/images/fengcai1a1.png";
$pictwo=home_url()."/wp-content/themes/ReeooV3/images/zuoye.png";
$picthree=home_url()."/wp-content/themes/ReeooV3/images/zixun11.png";
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="zh-CN">

<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=3.0, user-scalable=yes"> 
<link rel="stylesheet" href="<?php echo home_url();?>/wp-content/themes/my_mobile_page_v2/style.css" type="text/css" />
<style>
.menu {
width: 100%;
text-align: center;
position: absolute;
bottom: 5%;
}
.menu ul li a img {
width: 90px;
height: 85px;
}
</style>
<link rel="stylesheet" href="<?php echo home_url();?>/wp-content/themes/my_mobile_page_v2/themedesigns/black/style.css" type="text/css" />
<link rel="pingback" href="<?php echo home_url();?>/xmlrpc.php" />
<meta name='robots' content='noindex,nofollow' />
<link rel="alternate" type="application/rss+xml" title="微营销管理平台 &raquo; Feed" href="<?php echo home_url();?>/?feed=rss2" />
<link rel="alternate" type="application/rss+xml" title="微营销管理平台 &raquo; 评论 Feed" href="<?php echo home_url();?>/?feed=comments-rss2" />
<script type='text/javascript' src='<?php echo home_url();?>/wp-includes/js/jquery/jquery.js?ver=1.7.2'></script>
<link rel="EditURI" type="application/rsd+xml" title="RSD" href="<?php echo home_url();?>/xmlrpc.php?rsd" />
<link rel="wlwmanifest" type="application/wlwmanifest+xml" href="<?php echo home_url();?>/wp-includes/wlwmanifest.xml" /> 
<meta name="generator" content="WordPress 3.4.1" />
<script type="text/javascript">
var $ = jQuery.noConflict();
	$(function() {
		$('#activator').click(function(){
				$('#box').animate({'top':'65px'},500);
		});
		$('#boxclose').click(function(){
				$('#box').animate({'top':'-400px'},500);
		});
		$('#activator_share').click(function(){
				$('#box_share').animate({'top':'65px'},500);
		});
		$('#boxclose_share').click(function(){
				$('#box_share').animate({'top':'-400px'},500);
		});

	});
	$(document).ready(function(){
	$(".toggle_container").hide(); 
	$(".trigger").click(function(){
		$(this).toggleClass("active").next().slideToggle("slow");
		return false;
	});
	
	});
</script>
<script type="text/javascript" src="<?php echo home_url();?>/wp-content/themes/my_mobile_page_v2/js/photoslide.js"></script>

<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css">
<?php if($bgimg_url!=null){?>
<style>
    body{
      background-image: url(<?php echo $bgimgurl ?>);
    }
</style>
<?php }else{
	$bgpicurl=home_url()."/wp-content/themes/ReeooV3/images/schoolnew112.jpg";?>
<style>
    body{
      background-image: url(<?php echo $bgpicurl ?>);
    }
</style>
<?php }?>
<title><?php bloginfo('name'); ?></title>

</head>
<div id="main_container">

        <div class="box" id="box">
        	<div class="box_content">
            
            	<div class="box_content_tab">
                Search
                </div>
                
                <div class="box_content_center">
                <div class="form_content">
                <form method="get" id="searchform" action="/index.php">
                <input type="text" class="form_input_box" value="" name="s" id="s"/>
                <a class="boxclose" id="boxclose">Close</a>
                <input type="submit" class="form_submit" id="searchsubmit" value="Submit"/>
                </form>
                </div> 
                
                <div class="clear"></div>
                </div>
            
           </div>
        </div>

        <div class="box" id="box_share">
        	<div class="box_content">
            	<div class="box_content_tab">
                Social Share
                </div>
                <div class="box_content_center">
                
                        <div class="social_share">
                        <ul>    
                        
																																																                        </ul>
                        </div>
            
                <a class="boxclose_right" id="boxclose_share">close</a>
                <div class="clear"></div>
                
                </div>
           </div>
        </div>
    
	<div class="menu">
    	<ul>  
			<?php if($videopic_url!=null){ ?>
					<li><!--<a href="<?php //echo $this->createMobileUrl('videolist',array('GWEID' => $gweid, 'fromuser' =>$fromuser));?>"><img src="<?php echo $videopicurl ?>" class="attachment-menu-icon-size wp-post-image" alt="" title="" />图片和视频</a>-->
					<a href="<?php echo $this->createMobileUrl('videolist',array('gweid' => $gweid));?>"><img src="<?php echo $videopicurl ?>" class="attachment-menu-icon-size wp-post-image" alt="" title="" />图片和视频</a></li>
			<? }else{ ?>
					<li><!--<a href="<?php //echo $this->createMobileUrl('videolist',array('GWEID' => $gweid, 'fromuser' =>$fromuser));?>"><img src="http://wpcloudforsina-wordpress.stor.sinaapp.com/uploads/again1/2014/04/个人风采1a1.png" class="attachment-menu-icon-size wp-post-image" alt="" title="" />图片和视频</a>-->
					<a href="<?php echo $this->createMobileUrl('videolist',array('gweid' => $gweid));?>"><img src="<?php echo $picone ?>" class="attachment-menu-icon-size wp-post-image" alt="" title="" />图片和视频</a></li>
            <?php }
				if($homework_url!=null){ ?>
					<li><!--<a href="<?php //echo $this->createMobileUrl('homeworklist',array('GWEID' => $gweid, 'fromuser' =>$fromuser));?>"><img src="<?php echo $homeworkurl ?>" class="attachment-menu-icon-size wp-post-image" alt="" title="" />作业</a>-->
					<a href="<?php echo $this->createMobileUrl('homeworklist',array('gweid' => $gweid));?>"><img src="<?php echo $homeworkurl ?>" class="attachment-menu-icon-size wp-post-image" alt="" title="" />作业</a></li>
			<? }else{
			?>
					<li><!--<a href="<?php //echo $this->createMobileUrl('homeworklist',array('GWEID' => $gweid, 'fromuser' =>$fromuser));?>"><img src="http://wpcloudforsina-wordpress.stor.sinaapp.com/uploads/again1/2014/04/作业.png" class="attachment-menu-icon-size wp-post-image" alt="" title="" />作业</a>-->
					<a href="<?php echo $this->createMobileUrl('homeworklist',array('gweid' => $gweid));?>"><img src="<?php echo $pictwo ?>" class="attachment-menu-icon-size wp-post-image" alt="" title="" />作业</a></li>
            <?php }
				if($notice_url!=null){ ?>
					<li><!--<a href="<?php //echo $this->createMobileUrl('noticelist',array('GWEID' => $gweid, 'fromuser' =>$fromuser));?>"><img src="<?php echo $noticeurl?>" class="attachment-menu-icon-size wp-post-image" alt="" title="" />公告</a>-->
					<a href="<?php echo $this->createMobileUrl('noticelist',array('gweid' => $gweid));?>"><img src="<?php echo $noticeurl?>" class="attachment-menu-icon-size wp-post-image" alt="" title="" />公告</a></li>
			<? }else{
			?>
					<li><!--<a href="<?php //echo $this->createMobileUrl('noticelist',array('GWEID' => $gweid, 'fromuser' =>$fromuser));?>"><img src="http://wpcloudforsina-wordpress.stor.sinaapp.com/uploads/again1/2014/04/活动资讯11.png" class="attachment-menu-icon-size wp-post-image" alt="" title="" />公告</a>-->
					<a href="<?php echo $this->createMobileUrl('noticelist',array('gweid' => $gweid));?>"><img src="<?php echo $picthree ?>" class="attachment-menu-icon-size wp-post-image" alt="" title="" />公告</a></li>
			<?php } ?>
        </ul>
    </div>
</div>    
<?php include $this -> template('footer');?>