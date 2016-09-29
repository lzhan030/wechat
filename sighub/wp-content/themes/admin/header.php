<?php 

require_once './wp-content/themes/ReeooV3/wesite/common/dbaccessor.php';

if(!isset($_GET['beIframe'])){ ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
	<meta charset="UTF-8" />
	<title><?php if (is_home () ) { bloginfo('name'); } elseif ( is_category() ) { single_cat_title();
	echo " - "; bloginfo('name'); } elseif (is_single() || is_page() ) { single_post_title(); echo " - "; bloginfo('name'); }
	elseif (is_search() ) { bloginfo('name'); echo "search results:"; echo
	wp_specialchars($s); } else { wp_title('',true); } ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" />
	<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="http://feeds.feedburner.com/reeoo" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css">
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
	<!--[if IE]>
    	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]--> 	
	<?php 
		if ( is_singular() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );
		wp_head();
	?>
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js"></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/bootstrap.min.js"></script>
	<style type="text/css">
		.menu .list-group-item{border: 1px solid #FFF;padding:5px 10px;}
		.list-title{padding: 0px 10px;border: 1px solid #FFF;;}
		.panel{border-radius: 0px;-webkit-box-shadow: 0 0px 0px rgba(0,0,0,0.05);box-shadow: 0 0px 0px rgba(0,0,0,0.05);}
		.btn-primary{border-radius: 0px;}
		.logo{margin-top:10px;}
	</style>	
	
</head>

<body <?php body_class(); ?> style="background-color: #E7E8EB;">
<div class="row">
	<div class="head_box">
		<div class="inner">
			<h3 class="logo"><img src="<?php bloginfo('template_directory'); ?>/images/orange_admin.png" style="width:320px;" /><!--?php bloginfo( 'name' ); ?--></h3>
			<div class="account"><a href="<?php echo wp_logout_url();?>" style="color:#222;"><span class="glyphicon glyphicon-off"></span>&nbsp&nbsp退出</a></div>
			<!--<div class="account"><a href="<?php echo site_url( '/login/?action=login' );?>" style="color:#222;"><span class="glyphicon glyphicon-off"></span>&nbsp&nbsp退出</a></div>-->
		</div>
	</div>
</div>
<div class="row container_box" style="width:1200px; margin-left:auto; margin-right:auto; padding:36px 0 88px">
<div class="cell_layout">
<div class="sidediv">

<aside id="side">
	<header id="header">
		<nav id="nav"><?php ;//wp_nav_menu( array('menu' => 'header-menu' )); ?>
			<a class="menu list-group-item" href="<?php echo home_url();?>" rel="跳转到普通用户界面">普通用户界面</a>
			<a class="menu list-group-item" style="padding-left:35px;border-top: 1px solid #e7e7eb;" rel="管理员平台首页"><span class="glyphicon glyphicon-home"></span>&nbsp&nbsp管理员平台首页</a>
			<a class="menu list-group-item" id="usermanage" href="<?php echo home_url();?>?admin&page=usermanage" rel="用户管理">用户管理</a>
			<a class="menu list-group-item" id="groupmanage" href="<?php echo home_url();?>?admin&page=groupmanage" rel="用户分组管理">分组管理</a>
			<!--<a class="menu" href="<?php echo home_url();?>?admin&page=accountmanage" rel="用户账号管理">用户账号管理</a>-->
			<a class="menu list-group-item" id="pubwechatmanage" href="<?php echo home_url();?>?admin&page=pubwechatmanage" rel="公众号管理">公用公众号管理</a>
			<a class="menu list-group-item" id="spacemanage" href="<?php echo home_url();?>?admin&page=spacemanage" rel="空间扩容管理">空间扩容管理 <span id="space_reminder" style="color:red;font-size:12px"></span>
			</a>
			<a class="menu list-group-item" id="accountappmgt" href="<?php echo home_url();?>?admin&page=accountappmgt" rel="公众号数目管理">公众号数目管理 <span id="accountapp_reminder" style="color:red;font-size:12px"></span>
			</a>			
			<a class="menu list-group-item" id="funcmanage" href="<?php echo home_url();?>?admin&page=funcmanage" rel="网站主题管理">功能列表管理</a>
			<a class="menu list-group-item" id="adminmenu" href="<?php echo home_url();?>?admin&page=adminmenu" rel="菜单模板管理">菜单模板管理</a>
			<!--a class="menu list-group-item" id="sitemanage" href="<?php echo home_url();?>?admin&page=usermanage" rel="网站主题管理">网站主题管理</a-->
			<a class="menu list-group-item" id="stylemanage" href="<?php echo home_url();?>?admin&page=we7stylemanage" rel="模板风格管理">模板风格管理</a>
			<!--a class="menu list-group-item" id="statisticsmanage" href="<?php echo home_url();?>?admin&page=statisticsmanage" rel="统计">统计</a-->
			<a class="menu list-group-item" href="<?php echo wp_logout_url();?>" rel="退出">退出</a>
			<!--<a class="menu list-group-item" href="<?php echo site_url( '/login/?action=login' );?>" rel="退出">退出</a>-->
		</nav>
	</header>
	<footer id="footer">
		<p>©2013 <a href="<?php echo home_url(); ?>"><?php bloginfo( 'name' ); ?></a>.</p>
		<p>Powered by <a href="#" target="_blank">XXX</a>.</p>
	</footer>
</aside>
</div>
<div class="col_main">
<article id="content">
   <?php }
        else{
?>
    <!DOCTYPE html>
        <html <?php language_attributes(); ?>>
        <head>
            <meta charset="<?php bloginfo( 'charset' ); ?>" />
            <meta name="viewport" content="width=device-width" />
            <title><?php wp_title( '|', true, 'right' ); ?></title>
            <link rel="profile" href="http://gmpg.org/xfn/11" />
            <link rel="pingback" href="<?php bloginfo('template_directory'); ?>/css/style.css" />
        </head>
        <body >
         <div id="page" class="hfeed site">
             <div id="main" class="wrapper">
                 <div class="site-wrap">
   <?php }?>
   <script>
        //某链接被选中后，字体颜色变成蓝色
	    var id;
        $('a').bind('click', function(){ 
			$('a').removeClass('linkcolor'); 
			$(this).addClass('linkcolor'); 
			
			$(".linkcolor").css("color","#428bca");
			id = this.id;
			setCookie("aname",id);  //由于页面会重新刷新，所以需要在cookie中获取对应刷新前选中的id或者class属性
		});  
		$(function(){ 
		    $("#usermanage").css("color","#428bca");
			getCookie("aname");
			//alert("获取到cookie的值了吗"+getCookie("name"));			
			$("#"+getCookie("aname")).css("color","#428bca");
			if(getCookie("aname") == "usermanage")
			{
				$("#usermanage").css("color","#428bca");
			}
			else
			    $("#usermanage").css("color","#555");
		});  

		//设置cookie
        function setCookie(name,value) 
		{ 
		 var Days = 30; 
		 var exp = new Date(); 
		 exp.setTime(exp.getTime() + Days*24*60*60*1000); 
		 document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString(); 
		} 
		//取cookie 
		function getCookie(name) 
		{ 
		 var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");
		 
		 if(arr=document.cookie.match(reg))
		 
		  return unescape(arr[2]); 
		 else 
		  return null; 
		} 

		<!--new space application reminder -->
		spacereminder();
		function spacereminder(){
			var str = $.ajax({url:'<?php bloginfo('template_directory'); ?>/cgi-bin/space_new_app_reminder.php' ,type:'GET',async:false,cache:false}).responseText;
			if(str>0){
				$("#space_reminder").html('<span class=\'glyphicon glyphicon-star\'></span>New');
			}else{
				$("#space_reminder").html('');
			}
		};
		$(function() {setInterval("spacereminder()", 15000);});
		
		<!--new account application reminder -->
		accountappreminder();
		function accountappreminder(){
			var str = $.ajax({url:'<?php bloginfo('template_directory'); ?>/cgi-bin/account_new_app_reminder.php' ,type:'GET',async:false,cache:false}).responseText;
			if(str>0){
				$("#accountapp_reminder").html('<span class=\'glyphicon glyphicon-star\'></span>New');
			}else{
				$("#accountapp_reminder").html('');
			}
		};
		$(function() {setInterval("accountappreminder()", 15000);});

	</script>