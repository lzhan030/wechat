<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
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
<?php
if($counts)
{ ?> 
	
    <body>
		 
		<div>	
			<!--<div class="mobile-div img-rounded">
			<div class="mobile-hd">微作业 > <font class="fontpurple">上传视频、图片</font></div>
			<div class="mobile-content">
				<div style="">
				<input type="button" class="btn btn-sm btn-info" onclick="location.href='<?php echo $this->createMobileUrl('swfvideoupload',array('GWEID' => $gweid, 'fromuser' =>$fromuser));?>'" name="del" id="buttondel" value="上传视频" style="font-size:13px;">	
				<input type="button" class="btn btn-sm btn-info" onclick="location.href='<?php echo $this->createMobileUrl('pictureupload',array('GWEID' => $gweid, 'fromuser' =>$fromuser));?>'" name="del" id="buttondel" value="上传图片" style="margin-left:5%;font-size:13px;">	
				</div>
			</div>
			</div>-->	
			<div class="mobile-div img-rounded">
			    <div class="mobile-hd">微作业 > <font class="fontpurple">作业列表 </font></div>
			<span></span>
			
			<?php 
				$pagesize=5; //设定每一页显示的记录数						
				//-----------------------------------------------------------------------------------------------//
				//分页逻辑处理
				//-----------------------------------------------------------------------------------------------
				/* foreach($counthomework as $homeworksnumber){
					 $countnumber=$homeworksnumber['countnumbers'];
				} */
				
				$pages=intval($countnumber/$pagesize); //计算总页数

				if ($countnumber % $pagesize) $pages++;

				//设置缺省页码
				//↓判断“当前页码”是否赋值过
				if (isset($_GET['homelist'])){ $page=intval($_GET['homelist']); }else{ $page=1; }//否则，设置为第一页
			   
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
				$hworks=$this -> doWebCountHomeWork($offset,$pagesize,$gweid,$fromuser);//取得—当前页—记录集！
					
			?>
			<div style="position:relative;height:400px;">
			<?php foreach($hworks as $hwork){ ?>
			    <div style="margin-top: 15px; margin-left:15px;border-bottom: 1px solid #E2E2E2;">
					<!--<a style="font-size: 16px; font-weight: bold; padding-top: 5px;" href="<?php //echo $this->createMobileUrl('viewhomework',array('GWEID' => $gweid,'fromuser'=>$fromuser,'homeworkId'=>$hwork['homework_id']));?>"><?php echo $hwork['homework_title']; ?></a>-->
					<a style="font-size: 16px; font-weight: bold; padding-top: 5px;" href="<?php echo $this->createMobileUrl('viewhomework',array('gweid' => $gweid,'homeworkId'=>$hwork['homework_id']));?>"><?php echo $hwork['homework_title']; ?></a>
					
				<div>
					<span style="font-size: 13px;"> <i>起止时间：<?php echo $hwork['homework_starttime']." -- ".$hwork['homework_endtime']; ?></i></span>
				</div>
				</div>
			<?php }?>
			
			<?php
				//============================//
				//  翻页显示 一               
				//============================//
				echo "<p style='position:relative;top:15px'>";  //  align=center
				$first=1;
				$prev=$page-1;   
				$next=$page+1;
				$last=$pages;

				if ($page > 1)
				{
					echo "<a href='{$this->createMobileUrl('homeworklist',array('homelist' => $first,'gweid' => $gweid))}'>首页</a>  &nbsp;&nbsp;&nbsp";
					echo "<a href='{$this->createMobileUrl('homeworklist',array('homelist' => $prev,'gweid' => $gweid))}'>上一页</a>  ";
					
				}

				if ($page < $pages)
				{
					echo "<a href='{$this->createMobileUrl('homeworklist',array('homelist' => $next,'gweid' => $gweid))}'>下一页</a>  &nbsp;&nbsp;&nbsp";
					echo "<a href='{$this->createMobileUrl('homeworklist',array('homelist' => $last,'gweid' => $gweid))}'>尾页</a>  ";
					
				}

				echo "</p>";

		    ?>
			
			
			<!--<?php foreach($homeworks as $homework){ ?>
			<div style="margin-top: 25px; border-bottom: 1px solid #E2E2E2;">
			    <a style="font-size: 14px; font-weight: bold; padding-top: 5px;" href="<?php echo $this->createMobileUrl('viewhomework',array('GWEID' => '','homeworkId'=>$homework['homework_id']));?>"><?php echo $homework['homework_title']; ?></a> 
			</div>
			<?php }?> -->
			</div>	
		</div>
		</div>
	</body>
</html>
<?php
    }
/*else if(($fromuser==null)&&(!$counts)){

?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<script>
			alert("请从微信公众号处进行访问");
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