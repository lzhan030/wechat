<?php
@session_start(); 

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
get_header(); ?>


<?php
    include '../common/dbaccessor.php';
	include '../common/web_constant.php';
    
	$post_id=$_GET["postid"];
	$sid=$_REQUEST["sid"];
    if($sid==null){$sid=$post_id;}
	$post_id=$sid;

?>



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<!--<script src="/wp-content/themes/silver-blue/js/jquery.min.js"></script>-->
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/table1.css" />
	<link rel="stylesheet" type="text/css"  href="<?php bloginfo('template_directory'); ?>/css/webpage2.css" />
	<link rel="stylesheet" href="../../css/wsite.css" />
	<link rel="stylesheet" href="../../css/bootstrap.min.css">
	<script src="../../js/bootstrap.min.js"></script>

	<title>MobileTheme主题</title>
</head>

<body>
<div id="primary" class="site-content">
	<div id="content" role="main">
		<form action="" method="post">
			<div>
				<div class="main-title">
					<div class="title-1">当前位置：微官网 >文章><font class="fontpurple"><?php echo "文章评论" ?>  </font>   
					</div>
				</div>
				<div class="bgimg"></div>

				<div style="margin-left:20px;">
					
					<div class="panel panel-default" style="margin-right:50px; margin-top:30px">
						<div class="panel-heading" >文章评论列表</div>
						<table class="table table-striped" width="800"  border="1" align="center" >
							<tr>
								<th width="100">作者</th>
								<th width="200">发布时间</th>
								<th width="200">内容</th>
								<th width="200">操作</th>
							</tr>
																	
							<?php 
								$pagesize=2; //设定每一页显示的记录数						
								//-----------------------------------------------------------------------------------------------//
								//分页逻辑处理
								//-----------------------------------------------------------------------------------------------
								//$tmpArr = mysql_fetch_array($rs);
						//		$numAL = mysql_num_rows($site_list);  //取得记录总数$rs
								
								$commentCount=web_admin_count_comment($sid);//获取记录总数						
								foreach($commentCount as $commentnumber){
									 $comment_number=$commentnumber->commentcount;
									// echo "这是评论的总数 $comment_number";
								}
								
								$pages=intval($comment_number/$pagesize); //计算总页数
								if ($comment_number % $pagesize) $pages++;

								//设置缺省页码
								//↓判断“当前页码”是否赋值过
								if (isset($_GET['page'])){ $page=intval($_GET['page']); }else{ $page=1; }//否则，设置为第一页

								//↓计算记录偏移量
									$offset=$pagesize*($page - 1);

								//↓读取指定记录数
									$rs=web_admin_array_comment($offset,$pagesize,$sid);//取得—当前页—记录集！
									
									//一个function的总个数
									//foreach (){$curNem=iii->as bianliang}
									//$curNum = mysql_num_rows($rs); //$curNum - 当前页实际记录数，for循环输出用
									
									$arraysCount=web_admin_array_comment_count($offset,$pagesize,$sid);
									foreach($arraysCount as $arraynumber){
										 $count_number=$arraynumber->arrayCount;
										 //echo " 这是每一页的评论数 $count_number";
									}
							?> 
					
							<?php foreach($rs as $mobile_item){
							
								$comment=substr($mobile_item->comment_content,0,24);
								echo "<tr>";
								echo "	<td>$mobile_item->comment_author</td>";
								echo "	<td>$mobile_item->comment_date</td>";
								if(strlen($mobile_item->comment_content)>24){
								echo "	<td title='$mobile_item->comment_content'>$comment......</td>";}
								else{echo "	<td title='$mobile_item->comment_content'>$comment</td>";}
								echo "<td><input type='button' onClick='deleteComment({$mobile_item->comment_ID})' name='del' class='btn btn-sm btn-warning' id='buttondel' value='删除' class='btn_add'> " ;
								echo "</tr>	";
								}
							?>
						</table>
					</div>
					
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
								echo "<a href='?beIframe&page=".$first."&sid=".$sid."'>首页</a>  ";
								echo "<a href='?beIframe&page=".$prev."&sid=".$sid."'>上一页</a>  ";
							}

						if ($page < $pages)
							{
								echo "<a href='?beIframe&page=".$next."&sid=".$sid."'>下一页</a>  ";
								echo "<a href='?beIframe&page=".$last."&sid=".$sid."'>尾页</a>  ";
							}

							//============================//
							//  翻页显示 二               
							//============================//
						echo " | 共有 ".$pages." 页(".$page."/".$pages.")";
						for ($i=1;$i< $page;$i++){echo "<a href='?beIframe&page=".$i."&sid=".$sid."'>[".$i ."]</a>";}  // 1-先输出当前页之前的
						if ($page > 0) echo " [".$page."]";; // 2-再输出当前页

						for ($i=$page+1;$i<=$pages;$i++){echo "<a href='?beIframe&page=".$i."&sid=".$sid."'>[".$i ."]</a>  ";}// 3-接着输出当前页之后

							//echo "转到第 <INPUT maxLength=3 size=3 value=".($page+1)." name=gotox> 页 <INPUT hideFocus onclick=\"location.href='?page=gotox.value';\" type=button value=Go name=cmd_goto>"; 
							echo "</p>";

					?>
				</div>
				<div style="margin-left: 30px;">
					<input type='submit' class="btn btn-primary" name='submit' id='buttondep' value='完成' onclick='closeit()' class='btn_add' style="margin-top:10px; width:120px; margin-left:20px;"> 
					<input type='submit' class="btn btn-default" name='submit' id='buttondep' value='取消' onclick='close2()' class='btn_add' style="margin-top:10px; width:120px; margin-right:20px;"> 
				</div>
			</div>
		</form><!--主体结束--onClick='completeSite()'-->			
	</div>
</div>
</body>

<script language='javascript'>
		
	var xmlHttp;
    function createXMLHttpRequest(){
    if(window.ActiveXObject)
     xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
    else if(window.XMLHttpRequest)
     xmlHttp = new XMLHttpRequest();
    }

	function deleteComment(ID){	   
		if(confirm("确定删除吗？")){
			createXMLHttpRequest();
			xmlHttp.open("GET","<?php echo constant("CONF_THEME_DIR"); ?>/wesite/mobiletheme/comment_delete.php?beIframe&commentid="+ID,true);
			xmlHttp.onreadystatechange = function(){
				if(xmlHttp.readyState == 4 && xmlHttp.status == 200)
				alert("删除成功");
				window.location.reload();
			}
			xmlHttp.send(null);
		}
	}
		
	function closeit() {
		opener.location.reload(); 
		setTimeout("self.close()",0);
	}
	
	function close2(){
		window.opener=null;
		setTimeout("self.close()",0);
	}
	window.resizeTo(850,550);
</script>
</html>
<?php get_sidebar( 'front' ); ?>
<?php get_footer(); ?>
