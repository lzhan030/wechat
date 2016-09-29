<?php defined('IN_IA') or exit('Access Denied');?>
<?php //include $this -> template('header');?>

<?php

if(($counts) && (!$countstu) )
{ ?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=3.0, user-scalable=yes" />
		<!--<link type="text/css" rel="stylesheet" href="<?php //bloginfo('template_directory'); ?>/we7/style/bootstrap.css" />-->
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/we7/style/font-awesome.css" />
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/we7/style/common.css?v=<?php echo TIMESTAMP;?>" />
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/video.css">
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
	</head>
    <body style="width:100%;">
		<div>	
			<!--<div class="main-title">
				<div class="title-1">当前位置：微视频 > <font class="fontpurple">视频列表 </font>
				</div>
			</div>
			<div class="bgimg"></div>-->
			<div class="mobile-div img-rounded">
			    <div class="mobile-hd">视频 > <font class="fontpurple">上传视频、图片</font></div>
				<div class="mobile-content">
					<div style="">
					<!--<input type="button" class="btn btn-sm btn-info" onclick="location.href='<?php //echo $this->createMobileUrl('swfvideoupload',array('GWEID' => $gweid, 'fromuser' =>$fromuser));?>'" name="del" id="buttondel" value="上传视频" style="font-size:13px;">	
					<input type="button" class="btn btn-sm btn-info" onclick="location.href='<?php //echo $this->createMobileUrl('pictureupload',array('GWEID' => $gweid, 'fromuser' =>$fromuser));?>'" name="del" id="buttondel" value="上传图片" style="margin-left:5%;font-size:13px;">-->	
					<input type="button" class="btn btn-sm btn-info" onclick="location.href='<?php echo $this->createMobileUrl('swfvideoupload',array('gweid' => $gweid));?>'" name="del" id="buttondel" value="上传视频" style="font-size:13px;">	
					<input type="button" class="btn btn-sm btn-info" onclick="location.href='<?php echo $this->createMobileUrl('pictureupload',array('gweid' => $gweid));?>'" name="del" id="buttondel" value="上传图片" style="margin-left:5%;font-size:13px;">	
					</div>
			    </div>
			</div>
			
			<div class="mobile-div img-rounded">
			    <div class="mobile-hd">视频 > <font class="fontpurple">视频、图片列表 </font></div>
			
			
			<?php 
				$pagesize=5; //设定每一页显示的记录数						
				//-----------------------------------------------------------------------------------------------//
				//分页逻辑处理
				//-----------------------------------------------------------------------------------------------
				
				/* foreach($videosCount as $videosnumber){
					 $countnumber=$videosnumber['videoCount'];
				} */
				
				$pages=intval($countnumber/$pagesize); //计算总页数

				if ($countnumber % $pagesize) $pages++;

				//设置缺省页码
				//↓判断“当前页码”是否赋值过
				if (isset($_GET['videopage'])){ $page=intval($_GET['videopage']); }else{ $page=1; }//否则，设置为第一页
			   
				//↓计算记录偏移量
					$offset=$pagesize*($page - 1);

				//↓读取指定记录数
				if($page==$pages){
					$ps=$countnumber%$pagesize;
					if($ps == 0)
					{
					   $pagesize = 5;
					}
					else
					$pagesize=$ps;
				}	
				$videos=$this -> doMobileCountVideoPage($offset,$pagesize,$gweid);//取得—当前页—记录集！
					
			?>
			<div style="position:relative;height:350px;">	
			<?php foreach($videos as $video)
			{ $vt=mb_substr($video['video_title'],0,16,'UTF-8');
			?>
				<div style="margin-top:15px;margin-left:20px;border-bottom: 1px solid #E2E2E2;">
				<?php if(mb_strlen($video['video_title'])>16){ ?>
				
				<!--<a style="font-size: 15px; font-weight: bold; padding-top: 3px;" href="<?php //echo $this->createMobileUrl('videoplay',array('id' => $video['video_id'], 'GWEID' => $gweid));?>" title="<?php echo $video['video_title']; ?>"><?php //echo $vt ;?>...</a> -->
				<a style="font-size: 15px; font-weight: bold; padding-top: 3px;" href="<?php echo $this->createMobileUrl('videoplay',array('id' => $video['video_id'], 'gweid' => $gweid));?>" title="<?php echo $video['video_title']; ?>"><?php echo $vt ;?>...</a>
				<?php } else  {?>
				<!--<a style="font-size: 15px; font-weight: bold; padding-top: 3px;" href="<?php //echo $this->createMobileUrl('videoplay',array('id' => $video['video_id'], 'GWEID' => $gweid));?>"><?php echo $vt ;?></a>-->
				<a style="font-size: 15px; font-weight: bold; padding-top: 3px;" href="<?php echo $this->createMobileUrl('videoplay',array('id' => $video['video_id'], 'gweid' => $gweid));?>"><?php echo $vt ;?></a>
				
				<?php }?>
			    <!--<span style="font-size: 14px;">  <?php //echo $video['tea_name']; ?></span>-->
				<!--<span style="font-size: 14px; margin-left:5%;"> <i>发布人:<font color=Indiqo> <?php //echo $video['tea_name']; ?></font></i></span>-->
				<div>
				<span style="font-size: 13px;"> <i><?php echo $video['tea_name']."发布于：".date('m-j,Y',strtotime($video['video_time'])); ?></i></span>
				</div>
			</div>
			<?php }?>
			
			
			<?php
				//============================//
				//  翻页显示 一               
				//============================//
				echo "<p style='position:absolute;bottom:5px;left:15px;font-size:15px;'>";  //  align=center
				$first=1;
				$prev=$page-1;   
				$next=$page+1;
				$last=$pages;

				if ($page > 1)
				{
					echo "<a href='{$this->createMobileUrl('videolist',array('videopage' => $first,'gweid' => $gweid))}'>首页</a>&nbsp;&nbsp  ";
					echo "<a href='{$this->createMobileUrl('videolist',array('videopage' => $prev,'gweid' => $gweid))}'>上一页</a>&nbsp;&nbsp  ";
					
				}

				if ($page < $pages)
				{
					echo "<a href='{$this->createMobileUrl('videolist',array('videopage' => $next,'gweid' => $gweid))}'>下一页</a>&nbsp;&nbsp  ";
					echo "<a href='{$this->createMobileUrl('videolist',array('videopage' => $last,'gweid' => $gweid))}'>尾页</a>  ";
					
				}

				echo "</p>";

		?>
			</div>	
		</div>	
		</div>
	</body>
</html>
<?php
}else if((!$counts) && ($countstu))
{ ?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes" />
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/we7/style/font-awesome.css" />
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/we7/style/common.css?v=<?php echo TIMESTAMP;?>" />
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/video.css">
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/we7/script/jquery-1.7.2.min.js"></script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/we7/script/bootstrap.js"></script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/we7/script/common.js?v=<?php echo TIMESTAMP;?>"></script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/we7/script/emotions.js"></script>
		
		<style type="text/css">
			a:visited {
				color: #FF00FF
			}
			body {
			    background: #ECECEC;
				font: 16px/1.5 "Microsoft Yahei","微软雅黑",Tahoma,Arial,Helvetica,STHeiti;
			}
			
		</style>		
	</head>
    <body>
		<div>	
			<div class="mobile-div img-rounded">
			    <div class="mobile-hd">视频 > <font class="fontpurple">视频、图片列表 </font></div>
			
			
			<?php 
				$pagesize=5; //设定每一页显示的记录数						
				//-----------------------------------------------------------------------------------------------//
				//分页逻辑处理
				//-----------------------------------------------------------------------------------------------
				
				/* foreach($videostuCount as $videosnumber){
					 $countnumber=$videosnumber['videoCount'];
				} */
				$pages=intval($countnumber/$pagesize); //计算总页数

				if ($countnumber % $pagesize) $pages++;

				//设置缺省页码
				//↓判断“当前页码”是否赋值过
				if (isset($_GET['videopage'])){ $page=intval($_GET['videopage']); }else{ $page=1; }//否则，设置为第一页
			   
				//↓计算记录偏移量
					$offset=$pagesize*($page - 1);

				//↓读取指定记录数
				if($page==$pages){
					$ps=$countnumber%$pagesize;
					if($ps == 0)
					{
					   $pagesize = 5;
					}
					else
					$pagesize=$ps;
				}	
				
				//$videos=$this -> doWebCountVideoPage($offset,$pagesize,$gweid);//取得—当前页—记录集！
				$videos=$this -> doWebCountVideoStuPage($offset,$pagesize,$gweid,$grade);//取得—当前页—记录集！
					
			?>
			<div style="position:relative;height:350px;">
			<?php foreach($videos as $video)	
			{ $vt=mb_substr($video['video_title'],0,16,'UTF-8');
			?>
			<div style="margin-top:15px;margin-left:20px;border-bottom: 1px solid #E2E2E2;">
				<?php if(mb_strlen($video['video_title'])>16){ ?>
			  
				<a style="font-size: 15px; font-weight: bold; padding-top: 3px;" href="<?php echo $this->createMobileUrl('videoplay',array('id' => $video['video_id'], 'gweid' => $gweid));?>" title="<?php echo $video['video_title']; ?>"><?php echo $vt ;?>...</a> 
				<?php } else  {?>
				<a style="font-size: 15px; font-weight: bold; padding-top: 3px;" href="<?php echo $this->createMobileUrl('videoplay',array('id' => $video['video_id'], 'gweid' => $gweid));?>"><?php echo $vt ;?></a>
				
				<?php }?>
			    <!--<span style="font-size: 14px;">  <?php //echo $video['tea_name']; ?></span>-->
				<div>
				<span style="font-size: 13px;"> <i><?php echo $video['tea_name']."发布于：".date('m-j,Y',strtotime($video['video_time'])); ?></i></span>
				</div>
			</div>
			<?php }?>
			
			
			<?php
				//============================//
				//  翻页显示 一               
				//============================//
				echo "<p style='position:absolute;bottom:5px;left:15px;font-size:15px;'>";  //  align=center
				$first=1;
				$prev=$page-1;   
				$next=$page+1;
				$last=$pages;

				if ($page > 1)
				{
					echo "<a href='{$this->createMobileUrl('videolist',array('videopage' => $first,'gweid' => $gweid))}'>首页</a>&nbsp;&nbsp  ";
					echo "<a href='{$this->createMobileUrl('videolist',array('videopage' => $prev,'gweid' => $gweid))}'>上一页</a>&nbsp;&nbsp ";
					
				}

				if ($page < $pages)
				{
					echo "<a href='{$this->createMobileUrl('videolist',array('videopage' => $next,'gweid' => $gweid))}'>下一页</a>&nbsp;&nbsp  ";
					echo "<a href='{$this->createMobileUrl('videolist',array('videopage' => $last,'gweid' => $gweid))}'>尾页</a>  ";
					
				}

				echo "</p>";

		?>
			   </div>
			</div>
		</div>
	</body>
</html>
<?php
    }
/*else if(($fromuser==null)&&(!$counts)&&(!$countstu)){

?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <script>
        alert("请从微信公众号处进行访问");
	</script>

<?php
	//include 'vip_register.php';
?>
		
		<script>
		<?php if(isset($_SERVER['HTTP_REFERER'])&&!empty($_SERVER['HTTP_REFERER']))
		{?>
		   location.href="<?php echo $_SERVER['HTTP_REFERER']; ?>";
		<?php }?>
		</script>
			</head>
<body>
</body>
</html>
<?php
	} */

else{
?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <script>
        alert("您未通过身份认证");
	</script>

<?php
	//include 'vip_register.php';
?>
		
	<script>
		//location.href="teacher_verify.php?GWEID=<?php echo $gweid;?>&fromuser=<?php echo $fromuser;?>";
		//location.href = "<?php echo $this->createMobileUrl('verifyuser',array('GWEID' => $gweid, 'fromuser' =>$fromuser, 'redirect_url' => urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])))?>";
		location.href = "<?php echo $this->createMobileUrl('verifyuser',array('gweid' => $gweid, 'redirect_url' => urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])))?>";
	</script>
	</head>
	<body>
	</body>	
</html>
<?php
	} 
?>
<?php include $this -> template('footer');?>
