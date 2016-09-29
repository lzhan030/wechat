<?php
/*
Plugin Name: ThemesDetector
Plugin URI: http://1.wpforsae.sinaapp.com/
Description: OrangeWeChat平台多模板设置插件、自定义用户上传目录插件,可以根据$_GET['site'},到数据库表wp_md_mobilemeta取得相应的主题模板
Version: 0.1
Author: Saxon
*/
define('TABLE_MOBILEMETA', $table_prefix.'md_mobilemeta');

require(dirname(__FILE__) . '/md-includes/function.php');
require(dirname(__FILE__) . '/md-includes/upload.php');
require(dirname(__FILE__) . '/md-includes/register.php');
require(dirname(__FILE__) . '/md-includes/login.php');
require(dirname(__FILE__) . '/md-includes/cron.php');
require(dirname(__FILE__) . '/md-includes/editor.php');
require(dirname(__FILE__) . '/md-includes/wechat_response.php');
require_once(dirname(__FILE__) . '/md-includes/access_token.php');
require_once(dirname(__FILE__) . '/md-includes/file.php');

show_admin_bar(false);


$pluginversion = md_pluginversion();
$pluginname = md_pluginname();
// Activation of plugin
if(function_exists('register_activation_hook')) {
	register_activation_hook( __FILE__, 'md_install' );
}

// Uninstallation of plugin
if(function_exists('register_uninstall_hook')) {
	register_uninstall_hook(__FILE__, 'md_uninstall');
}


if(isset($_GET['site']) || isset($_GET['page_id']) || isset($_GET['p']) || isset($_GET['admin']))
{
	add_filter('stylesheet', 'mobileDetect');
	add_filter('template', 'mobileDetect');
	if(isset($_GET['site'])){
		global $wpdb;
		$statisticsTable = $wpdb->prefix."wechat_website_statistics";
		$siteLink = $_SERVER['REQUEST_URI'];
		$siteIP = $_SERVER['REMOTE_ADDR'];
		$wpdb->query( $wpdb->prepare("INSERT INTO ".$statisticsTable."(site_id, site_link, time, site_ip)VALUES (%d, %s, NOW(), %s)",$_GET['site'], $siteLink, $siteIP));
		
		//add data to the file
		//$num = file_put_contents("add.txt", $_GET['site']." ".$siteLink." ".date("Y-m-d H:i:s")." ".$siteIP."\n", FILE_APPEND);
		//echo $num;
		
	}
}


if(isset($_GET['module']) || isset($_GET['do']) || isset($_GET['pcate']) || isset($_GET['ccate']) || isset($_GET['id']) ||  isset($_GET['goodsid']) ||  isset($_GET['goodsgid']))
{
	add_filter('stylesheet', 'mobileDetect');
	add_filter('template', 'mobileDetect');
	
	global $wpdb;
	date_default_timezone_set('PRC');
	$Link = $_SERVER['REQUEST_URI'];
	$IP = $_SERVER['REMOTE_ADDR'];
	$gweid=$_GET['gweid'];//??是否需要修改gweid
	$data = array(
		'type_id' => "",
		'type' => "",
		'link' => $Link,
		'time' => date('Y-m-d H:i:s'),
		'ip' => $IP,
		'gweid' => $gweid
	);
	if(($_GET['module'])=='weshopping'){//list/allcategories/pcate/ccate/goodsid
		
		if($_GET['do']=='detail' && isset($_GET['id'])){//某个商品
			$data['type_id']=$_GET['id'];
			$data['type']="goodsid";
			$wpdb -> insert("{$wpdb->prefix}shopping_statistics",$data);
		}
	}else{
		if($_GET['do']=='goodsinfo' && isset($_GET['goodsgid'])){//微支付首页
			$data['type_id']=$_GET['goodsgid'];
			$data['type']="wepaygoodsinfo";
			$wpdb -> insert("{$wpdb->prefix}shopping_statistics",$data);
		}
	}
}

//根据公众号信息修改站点标题
function edit_bloginfo($text,$show ){
	if($show == 'name'){
	    global $wpdb;
		$siteTable = $wpdb->prefix."orangesite";
		$groupTable = $wpdb->prefix."wechat_group";
		$usewechatTable = $wpdb->prefix."wechat_usechat";
		$wechatTable = $wpdb->prefix."wechats";
		$postsTable = $wpdb->prefix."posts";
		
	    if(!empty($_GET['page_id']) && empty($_GET['site']))
		{
		    //2014-11-20新增修改
			$postinfos = $wpdb->get_results("SELECT post_content_filtered from ".$postsTable." p1 WHERE p1.ID = ".intval($_GET['page_id']));
			foreach($postinfos as $postinfo)
			{
				$post_siteid = $postinfo->post_content_filtered;	
			} 
			$_GET['site'] = $post_siteid;
		}
		if(!empty($_GET['p']) && empty($_GET['site']))
		{
		    //2014-11-20新增修改
			$postinfos = $wpdb->get_results("SELECT post_content_filtered from ".$postsTable." p1 WHERE p1.ID = ".intval($_GET['p']));
			foreach($postinfos as $postinfo)
			{
				$post_siteid = $postinfo->post_content_filtered;	
			} 
			$_GET['site'] = $post_siteid;
		}

		//如果链接中同时存在site和gweid，则以site为准，通过site取到的gweid一定是真实的，gweid有可能对应的是虚拟号
		if(!empty($_GET['site'])){
			//if(从数据库，根据$_GET['site']取出公众号名称name);
				//$text = $name;
			//2014-07-15新增修改
			$groupinfos = $wpdb->get_results("SELECT GWEID from ".$siteTable." t1 WHERE t1.id = ".intval($_GET['site']));
			foreach($groupinfos as $groupinfo)
			{
				$gweid = $groupinfo->GWEID;	
			} 
			
			$userid = $wpdb->get_results("SELECT site_user from ".$siteTable." WHERE id = ".intval($_GET['site']));
			
			foreach($userid as $id)
			{
				$user_id = $id->site_user;	
			}
			
			//20140812判断该号是否共享
			$shareflags = $wpdb->get_results("SELECT shared_flag from ".$groupTable." WHERE user_id = ".$user_id." AND GWEID = ".$gweid);
			foreach($shareflags as $shareflag)
			{
				$flag = $shareflag->shared_flag;	
			}
			//如果该号不是共享号则读取其本身的wechat_name，如果改号是共享的则读取共享号中的设置
			if( $flag == 0)
			{
			
				$widinfos = $wpdb->get_results("SELECT wid from ".$usewechatTable." WHERE user_id = ".$user_id." AND GWEID = ".$gweid);
				
				foreach($widinfos as $widinfo)
				{
					$wid = $widinfo->wid;	
				}
				
				$chatname = $wpdb->get_var("SELECT wechat_name from ".$usewechatTable." WHERE user_id = ".$user_id." AND GWEID = ".$gweid);
			}else   //为1表示共享，则读取为2的值的gweid对应的站点名称,所有的共享号用其中一个激活的号的站点名称20140820
			{
			    $getweids = $wpdb->get_results("SELECT WEID from ".$groupTable." WHERE user_id = ".$user_id." AND GWEID = ".$gweid." AND shared_flag = 2");
				foreach($getweids as $getweid)
				{
					$WEID = $getweid->WEID;	
				} 
			    $chatname = $wpdb->get_var("SELECT wechat_name from ".$usewechatTable." WHERE user_id = ".$user_id." AND GWEID = ".$gweid." AND WEID = ".$WEID);
				
			}
			if($chatname)
			{
			    $text = $chatname;
			}

		}
		
		//如果是普通的手机页面也需要动态给页面的title赋值20140910
		if((!empty($_GET['gweid']) || !empty($_GET['GWEID'])) && empty($_GET['site'])){
			//if(从数据库，根据$_GET['site']取出公众号名称name);
				//$text = $name;
			
			if(!empty($_GET['gweid']))
			{
				$gweid = $_GET['gweid'];
			}
			if(!empty($_GET['GWEID']))
			{
				$gweid = $_GET['GWEID'];
			}
			$userid = $wpdb->get_results("SELECT user_id from ".$groupTable." WHERE GWEID = ".$gweid);
			
			foreach($userid as $id)
			{
				$user_id = $id->user_id;	
			}
			//echo "没获取到userid吗".$user_id;
			//20140812判断该号是否共享
			$shareflags = $wpdb->get_results("SELECT shared_flag from ".$groupTable." WHERE user_id = ".$user_id." AND GWEID = ".$gweid);
			foreach($shareflags as $shareflag)
			{
				$flag = $shareflag->shared_flag;	
			}
			//如果该号不是共享号则读取其本身的wechat_name，如果改号是共享的则读取共享号中的设置
			if( $flag == 0)
			{
			
				$widinfos = $wpdb->get_results("SELECT wid from ".$usewechatTable." WHERE user_id = ".$user_id." AND GWEID = ".$gweid);
				
				foreach($widinfos as $widinfo)
				{
					$wid = $widinfo->wid;	
				}
				
				$chatname = $wpdb->get_var("SELECT wechat_name from ".$usewechatTable." WHERE user_id = ".$user_id." AND GWEID = ".$gweid);
			}else   //为1表示共享，则读取为2的值的gweid对应的站点名称,所有的共享号用其中一个激活的号的站点名称20140820
			{
			    $getweids = $wpdb->get_results("SELECT WEID from ".$groupTable." WHERE user_id = ".$user_id." AND GWEID = ".$gweid." AND shared_flag = 2");
				foreach($getweids as $getweid)
				{
					$WEID = $getweid->WEID;	
				} 
			    $chatname = $wpdb->get_var("SELECT wechat_name from ".$usewechatTable." WHERE user_id = ".$user_id." AND GWEID = ".$gweid." AND WEID = ".$WEID);
				
			}
			if($chatname)
			{
			    $text = $chatname;
			}
		}

	} 
    return $text;

}
add_filter( 'bloginfo', 'edit_bloginfo', 10, 2 );
if(isset($_GET['site']) && !empty($_GET['site'])){
	global $wpdb;
	$isPublic = $wpdb -> get_var("SELECT `site_value` FROM `wp_orangesitemeta` WHERE `site_id`={$_GET['site']} AND `site_key`='mobilethemeIsShowVipmember'");
	if($isPublic === 'true')
		define('DONOTCACHEPAGE', true);
}
?>