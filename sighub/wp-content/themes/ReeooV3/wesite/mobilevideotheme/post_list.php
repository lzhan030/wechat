<?php
@session_start(); 

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
get_header(); ?>


<?php
    include '../common/dbaccessor.php';
	include '../common/web_constant.php';
    
	$siteId=$_GET["siteId"];
	$sid=$_REQUEST["sid"];
    if($sid==null){$sid=$siteId;}
	$siteId=$sid;
	// Get Site information. If site does not exist, use the default value. 
	$site_title = $_POST["theme_title"];
	$site_footer = $_POST["theme_footer"];
	$site_size = $_POST["theme_size"];
	$site_color = $_POST["theme_color"];
	$site_contact = $_POST["theme_contact"];
	$site_pic= $_POST["theme_picture"];
	$site_editor = $_POST["theme_editor"];
	$site_postpermission = $_POST["post_permission"];
	$site_vipmember = $_POST["theme_vipmember"];
	$site_vipmember_editor = $_POST["theme_vipmember_editor"];
	$site_needapproval = $_POST["theme_needapproval"];
	$qiniu_access = $_POST["qiniu_access"];
	$qiniu_secret = $_POST["qiniu_secret"];
	$qiniu_bucket = $_POST["qiniu_bucket"];
	
	$site_list = web_admin_get_post_list($siteId, "post");
	web_admin_set_mobile_themes_parameter($siteId, $site_title, $site_footer, $site_size, $site_color, $site_pic, $site_editor, $site_postpermission, $site_vipmember,$site_vipmember_editor,$site_contact);
	$wpdb -> insert(web_admin_get_table_name("orangesitemeta"),array('site_id'=>$siteId, 'site_key'=>'mobilethemeNeedApproval', 'site_value'=>(!empty($site_needapproval))?$site_needapproval:'false'));
	$wpdb -> insert(web_admin_get_table_name("orangesitemeta"),array('site_id'=>$siteId, 'site_key'=>'qiniu_access', 'site_value'=>(!empty($qiniu_access))?$qiniu_access:''));
	$wpdb -> insert(web_admin_get_table_name("orangesitemeta"),array('site_id'=>$siteId, 'site_key'=>'qiniu_secret', 'site_value'=>(!empty($qiniu_secret))?$qiniu_secret:''));
	$wpdb -> insert(web_admin_get_table_name("orangesitemeta"),array('site_id'=>$siteId, 'site_key'=>'qiniu_bucket', 'site_value'=>(!empty($qiniu_bucket))?$qiniu_bucket:''));
	if(isset($_POST['submit'])){    
		echo "<script language='javascript'></script>";   
?>

	<script>
		location.href="<?php echo constant("CONF_THEME_DIR"); ?>/wesite/common/website_list.php?beIframe";
	</script>
<?php		
}?>

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

	<title>视频手机上传模板</title>
</head>

<body>
<div id="primary" class="site-content">
	<div id="content" role="main">
		<form action="" method="post">
			<div>
				<div class="main-title">
					<div class="title-1">当前位置：微官网 ><font class="fontpurple"><?php if(!isset($_GET['isupdate'])){echo "创建新站点第四步：编辑视频>";}else{echo "视频编辑>";} ?>  </font>   
					</div>
				</div>
				<div class="bgimg"></div>

				<div>
					<td>
						<a href="#"
							onclick="javascript:window.open('<?php echo get_template_directory_uri(); ?>/wesite/mobilevideotheme/post_insert_dialog_fast.php?beIframe&artType=post&addAttachment=<?php echo $site_pic ?>&refreshOpener=yes&siteId=<?php echo $siteId ?>','_blank','height=520,width=725,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')">
							<input type="button" class="btn btn-primary" onClick=  id="menu" style="margin-top:10px;width:120px" value="创建新页面"/>
						</a>
					</td>
					<div class="panel panel-default" style="margin-right:50px; margin-top:30px">
						<div class="panel-heading">已建页面列表</div>
						<table class="table table-striped" width="800"  border="1" align="center">
							<tr>
								<th width="100">标题</th>
								<th width="120">发布时间</th>
								<th width="50">视频状态</th>
								<th width="260">操作</th>
							</tr>
																	
							<?php 
								$pagesize=7; //设定每一页显示的记录数						
								//-----------------------------------------------------------------------------------------------//
								//分页逻辑处理
								//-----------------------------------------------------------------------------------------------
								//$tmpArr = mysql_fetch_array($rs);
						//		$numAL = mysql_num_rows($site_list);  //取得记录总数$rs
								
								$postsCount=web_admin_count_post($sid,"post");//获取记录总数						
								foreach($postsCount as $postnumber){
									 $countnumber=$postnumber->postCount;
									 //echo $countnumber;
								}
								
								$pages=intval($countnumber/$pagesize); //计算总页数

								if ($countnumber % $pagesize) $pages++;

								//设置缺省页码
								//↓判断“当前页码”是否赋值过
								if (isset($_GET['page'])){ $page=intval($_GET['page']); }else{ $page=1; }//否则，设置为第一页

								//↓计算记录偏移量
									$offset=$pagesize*($page - 1);

								//↓读取指定记录数
								    
									$rs=web_admin_array_post($offset,$pagesize,$sid,"post");//取得—当前页—记录集！
									
									//一个function活的总个数
									//foreach (){$curNem=iii->as bianliang}
									//$curNum = mysql_num_rows($rs); //$curNum - 当前页实际记录数，for循环输出用
									
									$arraysCount=web_admin_array_post_count($offset,$pagesize,$sid,"post");
									foreach($arraysCount as $arraynumber){
										 $count_number=$arraynumber->arrayCount;
										 //echo $count_number;
									}
							?> 
					
							<?php foreach($rs as $mobile_item){
							   
							    echo "<tr>";
								echo "	<td>$mobile_item->post_title</td>";
								echo "	<td>$mobile_item->post_date</td>";
								if($mobile_item->post_status=='pending') 
									echo"	<td>待审核</td>";
								else
									echo"	<td>已发布</td>";
								echo " <td><input type='button' onClick='deletePost({$mobile_item->ID})' name='del' class='btn btn-sm btn-warning' id='buttondel' value='删除' class='btn_add'> " ;
								//echo "<input type='button' onClick='updateSite({$mobile_item->ID})' name='upd' class='btn btn-sm btn-info' id='buttonupd' value='修改' class='btn_add'> </td>" ;
							?>		
							<a href="#" onclick="javascript:window.open('../mobilevideotheme/post_insert_dialog_fast.php?beIframe&artType=<?php echo "page" ?>&refreshOpener=yes&siteId=<?php echo $siteId?>&postid=<?php echo $mobile_item->ID ?>','_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')">
						    <input type="button" class="btn btn-sm btn-info" onClick=  id="menu" value="修改"/>
			                </a>
							<a href="#" onclick="javascript:window.open('<?php echo get_template_directory_uri(); ?>/wesite/mobilevideotheme/comment_manage.php?beIframe&postid=<?php echo $mobile_item->ID ?>','_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')">
							<input type='button' onClick= name='comm' class='btn btn-sm btn-warning' id='buttondel' value='评论管理' class='btn_add'>
							</a>														
							<input type='button' onclick="stickypot(<?php echo $mobile_item->ID ?>)" name='stickyp'  id='stickyp' style="width:70px;" <?php if(!is_sticky($mobile_item->ID)){ ?> value='置顶' class='btn btn-sm btn-info' <?php } else{ ?> value='取消置顶' class='btn btn-sm btn-warning'<?php } ?> >
							
							<a href="#" onclick="javascript:window.open('<?php echo get_template_directory_uri(); ?>/wesite/mobilevideotheme/video_play_approve.php?beIframe&postid=<?php echo $mobile_item->ID ?>','_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')">
							<input type='button' onClick= name='comm' class='btn btn-sm btn-warning' id='buttondel' value='查看内容(审核)' class='btn_add'>
							</a>
					        <?php
								
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
				<div> 
					<input type='submit' class="btn btn-primary" name='submit' id='buttondep' value='完成' class='btn_add' style="margin-top:10px; width:120px;"> 
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

	function deletePost(ID){	   
		if(confirm("确定删除吗？")){
			createXMLHttpRequest();
			xmlHttp.open("GET","<?php echo constant("CONF_THEME_DIR"); ?>/wesite/mobilevideotheme/post_delete.php?beIframe&header=0&footer=0&postid="+ID,true);
			xmlHttp.onreadystatechange = function(){
				if(xmlHttp.readyState == 4 && xmlHttp.status == 200)
				{    
				   //alert("删除成功");
				   alert(xmlHttp.responseText);	
				   location.href="<?php echo constant("CONF_THEME_DIR"); ?>/wesite/mobilevideotheme/post_list.php?beIframe&siteId="+"<?php echo $siteId;?>";
				}	
				
			}
			xmlHttp.send(null);
		}
	}
	/* function comment(ID){
	   window.param=ID;
	   window.open('comment_manage.php?beIframe&postid='+ID,'_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')
	} */
	//function updateSite(id){
		
		//window.param=id;
	    //location.href='<?php echo constant("CONF_THEME_DIR"); ?>/web_manage_website_update_check.php?beIframe&siteId='+id+'&mark=1';	   
	  
	//}	
	function stickypot(pid){
		createXMLHttpRequest();
		xmlHttp.open("GET","<?php echo constant("CONF_THEME_DIR"); ?>/wesite/common/post_sticky.php?beIframe&postid="+pid,true);
		xmlHttp.onreadystatechange = function(){
			if(xmlHttp.readyState == 4 && xmlHttp.status == 200)
			alert("设置成功");
			location.href="<?php echo constant("CONF_THEME_DIR"); ?>/wesite/mobilevideotheme/post_list.php?beIframe&siteId="+"<?php echo $siteId;?>";
		}
		xmlHttp.send(null);
	}
		
</script>
</html>
<?php get_sidebar( 'front' ); ?>
<?php get_footer(); ?>
