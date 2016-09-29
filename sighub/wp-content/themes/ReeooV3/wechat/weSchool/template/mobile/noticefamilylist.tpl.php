<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>

<?php

if($counts)
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
                <style type="text/css">
                        ruby {
                                font-size:60px;
                                color:red;
                                font-family:微软雅黑;
                        }
                        .tab {
                                /*border:solid 1px;*/
                                border-color:rgb(80,160,91);
                                /*width:500px;*/
                                margin:0 auto;
                                text-align:left;
                        }
                        .hd {
								background:#f2f2f2;
								z-index:99999;
                                /*background-color:rgb(230,242,230);*/
                                height:10%;
                                width:100%;
                                /*border:solid 1px;*/
                                border-color:#5b8a98;
								border-radius:6px;
                        }
                        .hd ul {
                                padding:0;
                                margin:0;
                                height:43px;
                                overflow: hidden;
								border-radius:6px;
                        }
                        .hd .nomal {
								color:#4c4c4c
                                font-size:16px;
                                height:43px;
                                line-height:41px;
                                float:left;
                                width:25%;
                                text-align:center;
                                border:solid 1px;
                                border-color:#CCC;
								border-height:30px;
                                cursor:pointer;
                                list-style:none;
								background: url("/bg.png") no-repeat 100% 0;
                        }
                        .hd .activeTab {
                                /*background:white;*/
                                font-weight: bold;
								font-size:20px;
								color:#663333;
                        }
                        #content {
                                padding:20px 10px;
                        }
						a:visited {
								color: #FF00FF
						}
                </style>
                <script type="text/javascript">
							var tab_ul=<?php echo $tab_ul?$tab_ul:0; ?>;
                        function tabClick(){		
                                if($(this).hasClass('activeTab')) 
                                        return;
                                $('.hd ul li').removeClass('activeTab');
                                $(this).addClass('activeTab');
                                var tabId = $(this).attr('tabId');
                                $('#content > div').hide();
                                $('#' + tabId).show();
                        }
                        $(document).ready(function(){
								if(tab_ul==1){
									$('.hd ul li').removeClass('activeTab');
									$('#b').addClass('activeTab');
									 var tabId = $('#b').attr('tabId');
									$('#content > div').hide();
									$('#' + tabId).show();
								}else if(tab_ul==2){
									$('.hd ul li').removeClass('activeTab');
									$('#c').addClass('activeTab');
									 var tabId = $('#c').attr('tabId');
									$('#content > div').hide();
									$('#' + tabId).show();
								}else if(tab_ul==3){
									$('.hd ul li').removeClass('activeTab');
									$('#d').addClass('activeTab');
										 var tabId = $('#d').attr('tabId');
									$('#content > div').hide();
									$('#' + tabId).show();
								}else{
									if($('#a').hasClass('activeTab')) 
                                        return;
									$('.hd ul li').removeClass('activeTab');
									$('#a').addClass('activeTab');
									var tabId = $('#a').attr('tabId');
									$('#content > div').hide();
									$('#' + tabId).show();
									}
									$('.hd ul li').click(tabClick);
								})
                </script>
        </head>
        <body>
                <div class="tab">
						<div class="mobile-div img-rounded">
						<div class="mobile-hd">微公告 > <font class="fontpurple">发布公告</font></div>
						<div class="mobile-content">
                        <div class="hd">
                                <ul>
                                        <li class="nomal " id="a" tabId="content1">老师</li>
                                        <li class="nomal"  id="b" tabId="content2">家长</li>
                                        <li class="nomal"  id="c" tabId="content3">自己</li>
                                        <!--<li class="nomal"  id="d" tabId="content4" onclick="location.href='<?php //echo $this->createMobileUrl('AddFamilyNotice',array('GWEID' => $gweid,'fromuser'=>$fromuser));?>'">发公告</li>-->
										<li class="nomal"  id="d" tabId="content4" onclick="location.href='<?php echo $this->createMobileUrl('AddFamilyNotice',array('gweid' => $gweid));?>'">发公告</li>
                                </ul>
                        </div>
						</div>
						</div>
						
						<div class="mobile-div img-rounded">
						<div class="mobile-hd">微公告 > <font class="fontpurple">公告列表 </font></div>
                        <div id="content" style="margin-left:-0px;">
                                <div id="content1" style="display:block; margin-top:-30px;">
                                        <?php 
				$pagesize=5; //设定每一页显示的记录数						
				//-----------------------------------------------------------------------------------------------//
				//分页逻辑处理
				//-----------------------------------------------------------------------------------------------
				/* foreach($countnoticeteachers as $noticeteachers){
					 $countteacher=$noticeteachers['Cnoticeteachers'];
				} */
				//echo "这是老师的个数：".$countteacher;
				$pages=intval($countteacher/$pagesize); //计算总页数

				if ($countteacher % $pagesize) $pages++;

				//设置缺省页码
				//↓判断“当前页码”是否赋值过
				if (isset($_GET['noticteacher'])){ $tpage=intval($_GET['noticteacher']); }else{ $tpage=1; }//否则，设置为第一页
			   
				//↓计算记录偏移量
					$offset=$pagesize*($tpage - 1);

				//↓读取指定记录数
				if($tpage==$pages){
					$ps=$countteacher%$pagesize;
					if($ps == 0)
					{
					    $pagesize = 5;
					}
					else
					$pagesize=$ps;
				}
				$teachernotice=$this -> doWebCountNoctieteacher($offset,$pagesize,$gweid,$fromuser);//取得—当前页—记录集！
					
			?>
			<div style="position:relative;height:350px;">
			<?php foreach($teachernotice as $tnotice){ ?>
					<div style="margin-top: 15px; border-bottom: 1px solid #E2E2E2;">
					<!--<a style="font-size: 16px; font-weight: bold; padding-top: 5px;" href="<?php echo $this->createMobileUrl('viewfamilynotice',array('GWEID' => $gweid,'fromuser'=>$fromuser,'noticeId'=>$tnotice['notice_id']));?>"><?php echo $tnotice['notice_title']; ?></a> -->
					<a style="font-size: 16px; font-weight: bold; padding-top: 5px;" href="<?php echo $this->createMobileUrl('viewfamilynotice',array('gweid' => $gweid,'noticeId'=>$tnotice['notice_id']));?>"><?php echo $tnotice['notice_title']; ?></a>
					<div>
					<span style="font-size: 13px;"><i><?php echo $tnotice['tea_name']; ?>发布于：<?php echo date('m-j,Y',strtotime($tnotice['notice_date'])); ?></i></span>
					</div>
					</div>
			<?php }?>
			
			<?php
				//============================//
				//  翻页显示 一               
				//============================//
				echo "<p style='position:relative;top:30px;'>";  //  align=center
				$first=1;
				$prev=$tpage-1;  
				$next=$tpage+1;
				$last=$pages;

				if ($tpage > 1)
				{
					echo "<a href='{$this->createMobileUrl('noticefamilylist',array('noticteacher' => $first, 'tab_ul' => 0,'gweid' => $gweid))}'>首页</a>  &nbsp;&nbsp;&nbsp";
					echo "<a style='margin-top:5%;' href='{$this->createMobileUrl('noticefamilylist',array('noticteacher' => $prev, 'tab_ul' => 0,'gweid' => $gweid))}'>上一页</a>  ";
					
				}

				if ($tpage < $pages)
				{
					echo "<a style='margin-top:5%;' href='{$this->createMobileUrl('noticefamilylist',array('noticteacher' => $next, 'tab_ul' => 0,'gweid' => $gweid))}'>下一页</a>  &nbsp;&nbsp;&nbsp";
					echo "<a style='margin-top:5%;' href='{$this->createMobileUrl('noticefamilylist',array('noticteacher' => $last, 'tab_ul' => 0,'gweid' => $gweid))}'>尾页</a>  ";
					
				}

				echo "</p>";

		    ?>
			</div>
                                </div>
                                <div id="content2" style="display:none;margin-top:-30px;">
                                        <?php 
				$pagesize=5; //设定每一页显示的记录数						
				//-----------------------------------------------------------------------------------------------//
				//分页逻辑处理
				//-----------------------------------------------------------------------------------------------
				/* foreach($countnoticestudents as $noticestudents){
					 $countnumber=$noticestudents['Cnoticestudents'];
				} */
				$pages=intval($countnumber/$pagesize); //计算总页数

				if ($countnumber % $pagesize) $pages++;

				//设置缺省页码
				//↓判断“当前页码”是否赋值过
				if (isset($_GET['noticfamily'])){ $spage=intval($_GET['noticfamily']); }else{ $spage=1; }//否则，设置为第一页
			   
				//↓计算记录偏移量
					$offset=$pagesize*($spage - 1);

				//↓读取指定记录数
				if($spage==$pages){
					$ps=$countnumber%$pagesize;
					if($ps == 0)
					{
					    $pagesize = 5;
					}
					else
					$pagesize=$ps;
				}	
				$studentsnotice=$this -> doWebCountNoticestudents($offset,$pagesize,$gweid,$fromuser);//取得—当前页—记录集！
					
			?>
			<div style="position:relative;height:400px;">
			<?php foreach($studentsnotice as $snotice){ ?>
					<div style="margin-top: 15px; border-bottom: 1px solid #E2E2E2;">
					<!--<a style="font-size: 16px; font-weight: bold; padding-top: 5px;" href="<?php echo $this->createMobileUrl('viewfamilynotice',array('GWEID' => $gweid, 'fromuser' => $fromuser,'noticeId'=>$snotice['notice_id']));?>"><?php echo $snotice['notice_title']; ?></a> -->
					<a style="font-size: 16px; font-weight: bold; padding-top: 5px;" href="<?php echo $this->createMobileUrl('viewfamilynotice',array('gweid' => $gweid,'noticeId'=>$snotice['notice_id']));?>"><?php echo $snotice['notice_title']; ?></a>
					<div>
					<span style="font-size: 13px;"><i><?php echo $snotice['stu_name']; ?>发布于：<?php echo date('m-j,Y',strtotime($snotice['notice_date'])); ?></i></span>
					</div>
					</div>
			<?php }?>
			
			<?php
				//============================//
				//  翻页显示 一               
				//============================//
				echo "<p style='position:relative;top:30px;'>";  //  align=center
				$first=1;
				$prev=$spage-1;   
				$next=$spage+1;
				$last=$pages;

				if ($spage > 1)
				{
					echo "<a href='{$this->createMobileUrl('noticefamilylist',array('noticfamily' => $first, 'tab_ul' => 1,'gweid' => $gweid))}'>首页</a>  &nbsp;&nbsp;&nbsp";
					echo "<a href='{$this->createMobileUrl('noticefamilylist',array('noticfamily' => $prev, 'tab_ul' => 1,'gweid' => $gweid))}'>上一页</a>  ";
					
				}

				if ($spage < $pages)
				{
					echo "<a href='{$this->createMobileUrl('noticefamilylist',array('noticfamily' => $next, 'tab_ul' => 1,'gweid' => $gweid))}'>下一页</a>  &nbsp;&nbsp;&nbsp";
					echo "<a href='{$this->createMobileUrl('noticefamilylist',array('noticfamily' => $last, 'tab_ul' => 1,'gweid' => $gweid))}'>尾页</a>  ";
					
				}

				echo "</p>";

		    ?>
			</div>
                                </div>
                                <div id="content3" style="display:none;margin-top:-30px;">
                                        <?php 
				$pagesize=5; //设定每一页显示的记录数						
				//-----------------------------------------------------------------------------------------------//
				//分页逻辑处理
				//-----------------------------------------------------------------------------------------------
				/* foreach($countnoticepeoples as $noticepeoples){
					 $countpeople=$noticepeoples['Cnoticepeoples'];
				} */
				
				$pages=intval($countpeople/$pagesize); //计算总页数

				if ($countpeople % $pagesize) $pages++;

				//设置缺省页码
				//↓判断“当前页码”是否赋值过
				if (isset($_GET['noticper'])){ $ppage=intval($_GET['noticper']); }else{ $ppage=1; }//否则，设置为第一页
			   
				//↓计算记录偏移量
					$offset=$pagesize*($ppage - 1);

				//↓读取指定记录数
				if($ppage==$pages){
					$ps=$countpeople%$pagesize;
					if($ps == 0)
					{
					    $pagesize = 5;
					}
					else
					$pagesize=$ps;
				}	
				$noticepeople=$this -> doWebCountNoticepeoples($offset,$pagesize,$gweid,$fromuser);//取得—当前页—记录集！
					
			?>
			<div style="position:relative;height:400px;">
			<?php foreach($noticepeople as $pnotice){ ?>
					<div style="margin-top: 15px; border-bottom: 1px solid #E2E2E2;">
					<!--<a style="font-size: 16px; font-weight: bold; padding-top: 5px;" href="<?php //echo $this->createMobileUrl('viewfamilynotice',array('GWEID' => $gweid, 'fromuser' => $fromuser,'noticeId'=>$pnotice['notice_id']));?>"><?php echo $pnotice['notice_title']; ?></a> -->
					<a style="font-size: 16px; font-weight: bold; padding-top: 5px;" href="<?php echo $this->createMobileUrl('viewfamilynotice',array('gweid' => $gweid,'noticeId'=>$pnotice['notice_id']));?>"><?php echo $pnotice['notice_title']; ?></a>
					<div>
					<span style="font-size: 13px;"> <i><?php echo $pnotice['stu_name']; ?>发布于：<?php echo date('m-j,Y',strtotime($pnotice['notice_date'])); ?></i></span>
					</div>
					</div>
			<?php }?>
			
			<?php
				//============================//
				//  翻页显示 一               
				//============================//
				echo "<p style='position:relative;top:30px;'>";  //  align=center
				$first=1;
				$prev=$ppage-1;   
				$next=$ppage+1;
				$last=$pages;

				if ($ppage > 1)
				{
					echo "<a href='{$this->createMobileUrl('noticefamilylist',array('noticper' => $first, 'tab_ul' => 2,'gweid' => $gweid))}'>首页</a>  &nbsp;&nbsp;&nbsp";
					echo "<a href='{$this->createMobileUrl('noticefamilylist',array('noticper' => $prev, 'tab_ul' => 2,'gweid' => $gweid))}'>上一页</a>  ";
					
				}

				if ($ppage < $pages)
				{
					echo "<a href='{$this->createMobileUrl('noticefamilylist',array('noticper' => $next, 'tab_ul' => 2,'gweid' => $gweid))}'>下一页</a>  &nbsp;&nbsp;&nbsp";
					echo "<a href='{$this->createMobileUrl('noticefamilylist',array('noticper' => $last, 'tab_ul' => 2,'gweid' => $gweid))}'>尾页</a>  ";
					
				}

				echo "</p>";

		    ?>
			</div>
                                </div>
                                <div id="content4" style="display:none;">
                                        <!--<div><ruby>汉<rt>hàn</rt>字<rt>zì</rt></ruby></div>
                                        <div><ruby>漢<rt>かん</rt>字<rt>じ</rt></ruby></div>
                                        <div><ruby>张<rt>zhāng</rt>军<rt>jūn</rt></ruby></div>-->
                                </div>
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
}*/
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