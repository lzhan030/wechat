<?php defined('IN_IA') or exit('Access Denied');?>
<?php //include $this -> template('header');?>

<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=3.0, user-scalable=yes">
		
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/we7/style/font-awesome.css" />
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/we7/style/common.css?v=<?php echo TIMESTAMP;?>" />
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/video-js.min.css">
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/we7/script/jquery-1.7.2.min.js"></script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/we7/script/bootstrap.js"></script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/we7/script/common.js?v=<?php echo TIMESTAMP;?>"></script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/we7/script/emotions.js"></script>
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite_mobile.css">
		
		<style type="text/css">
			a:visited {
				color: #FF00FF
			}
			body {
			    background: #ECECEC;
				font: 16px/1.5 "Microsoft Yahei","微软雅黑",Tahoma,Arial,Helvetica,STHeiti;
			}
			
		</style>
		
		<?php if($type == "pic"){?>

			</head>
			<body >
			    <div class="mobile-div img-rounded">
					<div class="mobile-hd">图片管理> <font class="fontpurple">图片展示</font></div>
					<div class="mobile-content">
						<div id="playvideo">	
							<?php echo $video_url;?>
						</div>
					</div>
				</div>
			</body>
		</html>
		<?php }else{?>

	    <!-- 加载 VideoJS js -->
	    <script src="<?php bloginfo('template_directory'); ?>/js/videojs/video.js" type="text/javascript" charset="utf-8"></script>
	   
	    <!-- 皮肤 -->
	    <link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/video-js.min.css" type="text/css" media="screen" title="Video JS">
		 <script>
			videojs.options.flash.swf = "video-js.swf";
		 </script>
	   
	</head>
    <body>
        <div class="mobile-div img-rounded">
			<div class="mobile-hd">视频管理> <font class="fontpurple">视频播放</font></div>
			<div class="mobile-content">
		   <!-- Begin VideoJS -->
		  
		   <video id="example_video_1" class="video-js vjs-default-skin" controls preload="none" width="100%" height="264"  src="<?php echo $videourl;?>" 
		   poster="<?php echo $videourl."?vframe/jpg/offset/1/w/460/h/320";?>"
			  data-setup="{}" >
			<source src="<?php echo $videourl;?>" type='video/mp4' />
			<track kind="captions" src="demo.captions.vtt" srclang="en" label="English"></track><!-- Tracks need an ending tag thanks to IE9 -->
			<track kind="subtitles" src="demo.captions.vtt" srclang="en" label="English"></track><!-- Tracks need an ending tag thanks to IE9 -->
		  </video>
		  </div>
		</div>
    </body>
</html>
<?php }?>
<?php include $this -> template('footer');?>