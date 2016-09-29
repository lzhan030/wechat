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
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite_mobile.css">
        <title><?php bloginfo('name'); ?></title>
		
		<style type="text/css">
			a:visited {
				color: #FF00FF
			}
			body {
			    background: #ECECEC;
				font: 16px/1.5 "Microsoft Yahei","微软雅黑",Tahoma,Arial,Helvetica,STHeiti;
			}
			
		</style>
	<script>
	   function contentcheck()
	   {     
		   if($("#reply_content").val()=="")
			{
			   alert("内容是必填项");
			   return false;
			}	
			return true;
	   }
	</script>
	</head>
	
    <body>
	<form id="reply" onSubmit="return contentcheck();" action="" method="post">		
			<div class="mobile-div img-rounded">
				<div class="mobile-hd">微评论 > <font class="fontpurple">评论列表 </font></div>
			
			<?php 
				$pagesize=5; //设定每一页显示的记录数						
				//-----------------------------------------------------------------------------------------------//
				//分页逻辑处理
				//-----------------------------------------------------------------------------------------------
				foreach($countreplys as $replyteacher){
					 $countreplyteacher=$replyteacher['Cteacher'];
				}
				//echo "这是老师的个数：".$countteacher;
				$pages=intval($countreplyteacher/$pagesize); //计算总页数

				if ($countreplyteacher % $pagesize) $pages++;

				//设置缺省页码
				//↓判断“当前页码”是否赋值过
				if (isset($_GET['replyteacher'])){ $page=intval($_GET['replyteacher']); }else{ $page=1; }//否则，设置为第一页
			   
				//↓计算记录偏移量
					$offset=$pagesize*($page - 1);

				//↓读取指定记录数
				$teacherreply=$this -> doWebReplyteacher($offset,$pagesize,$gweid,$notice_id);//取得—当前页—记录集！
					
			?>
			
			<?php foreach($teacherreply as $treply){ ?>
					<div style="margin-top: 5px; border-bottom: 1px solid #E2E2E2;font-size: 18px;">
					<?php echo $treply['reply_content']; ?>
					<div>
					<span style="font-size: 13px;"><i><font color=DodgerBlue>  
					<?php if(substr($treply['reply_author'],0,1)=='t'){
								$teachername=$this -> doWebReplyfamilytname(substr($treply['reply_author'],1));
								foreach($teachername as $tname){
									echo $tname['tea_name']." 回复于：".$treply['reply_time'];
								}
							}else{
								$familyname=$this -> doWebReplyfamilysname(substr($treply['reply_author'],1));
								foreach($familyname as $fname){
								echo $fname['stu_name']." 回复于：".$treply['reply_time'];
								}
						} ?>
					</font></i></span>
					</div>
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
					echo "<a href='{$this->createMobileUrl('replyteacherlist',array('replyteacher' => $first, 'no' => $no,'gweid' => $gweid))}'>首页</a>  &nbsp;&nbsp;&nbsp";
					echo "<a href='{$this->createMobileUrl('replyteacherlist',array('replyteacher' => $prev, 'no' => $no,'gweid' => $gweid))}'>上一页</a>  ";
					
				}

				if ($page < $pages)
				{
					echo "<a href='{$this->createMobileUrl('replyteacherlist',array('replyteacher' => $next, 'no' => $no,'gweid' => $gweid))}'>下一页</a>  &nbsp;&nbsp;&nbsp";
					echo "<a href='{$this->createMobileUrl('replyteacherlist',array('replyteacher' => $last, 'no' => $no,'gweid' => $gweid))}'>尾页</a>  ";
					
				}

				echo "</p>";

		    ?>
			
			<!--<?php foreach($replys as $reply){ ?>
			<div style="margin-top: 25px; border-bottom: 1px solid #E2E2E2;">
			    <?php echo $reply['reply_content']; ?>
			</div>
			<?php }?> -->
		</div>
		<div class="mobile-div img-rounded">
		<div style="height:20%;">
			<div class="mobile-hd">发表评论：</div>
			<tr>
				<td width="65%"><div style=""><textarea cols="50%" rows="10%"  class="form-control" id="reply_content" name="reply_content"></textarea></div></td>
			</tr>			
		</div>						
		<div style="margin-top:3%; margin-left:35%;">
				<input type="submit"  class="btn btn-primary" value="发布" id="content_sub" style="width:70px">
		</div>
		</div>
		</form>
	</body>
</html>
<?php include $this -> template('footer');?>