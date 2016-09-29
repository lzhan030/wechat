<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>



<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes" />
		
		
	</head>
    <body>
		<div>	
			<div class="main-title">
				<div class="title-1">当前位置：视频 > <font class="fontpurple">视频列表 </font>
				</div>
			</div>
			<div class="bgimg"></div>
			<input type="button" class="btn btn-sm btn-info" onclick="location.href='<?php echo $this->createWebUrl('videoupload');?>'" name="del" id="buttondel" value="上传视频">	
			
			<span> </span>
			
			
			<?php 
				$pagesize=5; //设定每一页显示的记录数						
				//-----------------------------------------------------------------------------------------------//
				//分页逻辑处理
				//-----------------------------------------------------------------------------------------------
				
				foreach($videosCount as $videosnumber){
					 $countnumber=$videosnumber['videoCount'];
				}
				
				$pages=intval($countnumber/$pagesize); //计算总页数

				if ($countnumber % $pagesize) $pages++;

				//设置缺省页码
				//↓判断“当前页码”是否赋值过
				if (isset($_GET['videopage'])){ $page=intval($_GET['videopage']); }else{ $page=1; }//否则，设置为第一页
			   
				//↓计算记录偏移量
					$offset=$pagesize*($page - 1);

				//↓读取指定记录数
					
				$videos=$this -> doWebCountVideoPage($offset,$pagesize,$gweid);//取得—当前页—记录集！
					
			?>
			
			<?php foreach($videos as $video){ ?>
			<div style="margin-top: 25px; border-bottom: 1px solid #E2E2E2;">
			    <a style="font-size: 18px; font-weight: bold; padding-top: 5px;" href="<?php echo $video['video_url']; ?>"><?php echo $video['video_title']; ?></a> 
			    <span style="font-size: 17px;">  <?php echo $video['tea_name']; ?></span>
			</div>
			<?php }?>
			
			<?php
				//============================//
				//  翻页显示 一               
				//============================//
				echo "<p>";  //  align=center
				$first=1;
				$prev=$page-1;   
				$next=$page+1;
				$last=$pages;

				if ($page > 1)
				{
					echo "<a href='{$this->createWebUrl('testvideo',array('videopage' => $first))}'>首页</a>  &nbsp;&nbsp;&nbsp";
					echo "<a href='{$this->createWebUrl('testvideo',array('videopage' => $prev))}'>上一页</a>  ";
					
				}

				if ($page < $pages)
				{
					echo "<a href='{$this->createWebUrl('testvideo',array('videopage' => $next))}'>下一页</a>  &nbsp;&nbsp;&nbsp";
					echo "<a href='{$this->createWebUrl('testvideo',array('videopage' => $last))}'>尾页</a>  ";
					
				}

				echo "</p>";

		    ?>
			
			
		</div>
	</body>
</html>
<?php //} ?>