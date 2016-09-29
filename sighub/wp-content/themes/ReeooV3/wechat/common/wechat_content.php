<?php
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
include 'wechat_dbaccessor.php';
$newsid=$_GET['newsid'];
$newsinfo=material_item_get_info($newsid);
$upload =wp_upload_dir();
foreach($newsinfo as $info){
	$title=$info->news_item_title;
	$description=$info->news_item_description;
	if((empty($info->news_item_picurl))||(stristr($info->news_item_picurl,"http")!==false)){
		$picurl=$info->news_item_picurl;
	}else{
		$picurl=$upload['baseurl'].$info->news_item_picurl;
	}
	
}

  
?>
<!DOCTYPE html>
<html> 
	<head>     
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">    
		<link rel="dns-prefetch" href="http://mmbiz.qpic.cn">    
		<link rel="dns-prefetch" href="http://res.wx.qq.com">    
		<title><?php echo $title; ?></title>    
		<meta http-equiv="X-UA-Compatible" content="IE=edge">    
		<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />    
		<meta name="apple-mobile-web-app-capable" content="yes">    
		<meta name="apple-mobile-web-app-status-bar-style" content="black">    
		<meta name="format-detection" content="telephone=no">
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
		<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js?t=123" type="text/javascript"></script>
		<script src="<?php bloginfo('template_directory'); ?>/js/jquery.Jcrop.js" type="text/javascript"></script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/wechat_nourl.js"></script>
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wechat_nourl.css" type="text/css" />     
		<link media="screen and (min-width:1023px)" rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory'); ?>/css/wechat_content.css"/>
	</head> 
	<body id="activity-detail">        
		<div class="rich_media">        
			<div class="rich_media_inner">            
				<h2 class="rich_media_title" id="activity-name">
					<?php echo $title; ?>                            
				</h2>
				<div id="page-content">                
					<div id="img-content">
						<div class="rich_media_thumb" id="media">                        
							<img onerror="this.parentNode.removeChild(this)" src="<?php echo $picurl; ?>" />
						</div>                                                            
						<div class="rich_media_content" id="js_content">
							<?php echo str_replace('\"', '"', $description);?>
						</div>             
					</div>                                            
				</div>
			</div>    
		</div>     
	</body>
</html>