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
	
	//$refreshOpener=$_GET["refreshOpener"];
	
	
	// if(isset($_POST['submit'])){    
	// echo "<script language='javascript'>alert('发布成功');</script>";  
?>

  <!-- <script>
		// location.href="<?php echo constant("CONF_THEME_DIR"); ?>/web_manage_website_list.php?beIframe";
	// </script>
//<?php		
// }?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<!--<script src="/wp-content/themes/silver-blue/js/jquery.min.js"></script>-->
	<link rel="pingback" href="<?php bloginfo('stylesheet_url'); ?>" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/table1.css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js"></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css" />
	<script src="<?php bloginfo('template_directory'); ?>/js/checkbox.js"></script>
<title><?php bloginfo('name'); ?></title>
</head>
<body>
<div id="primary" class="site-content">
	<!--<div id="content" role="main">-->
		<form action=" " method="post">
			<div style="margin-left:10px;">
				<div class="main-title">
					<div class="title-1"><font class="fontpurple">文章管理</font>  
					</div>
				</div>
				<div class="bgimg"></div>
					<div class="panel panel-default" style="margin-left:30px;margin-right:30px; margin-top:20px">
					<div class="panel-heading">已建文章列表</div>
					<table class="table table-striped" width="800"  border="1" align="center">
						<tr>
						    <th  width="50"></th> 
							<th  width="200">标题</th>
							<th  width="200">发布时间</th>
							<th  width="200">操作</th>
						</tr>
																	
					<?php 
						$pagesize=5; //设定每一页显示的记录数

						
						//-----------------------------------------------------------------------------------------------//
                        //分页逻辑处理
                        //-----------------------------------------------------------------------------------------------
						//$tmpArr = mysql_fetch_array($rs);
				//		$numAL = mysql_num_rows($site_list);  //取得记录总数$rs
						
                        $postsCount=web_admin_count_post($sid,"post");//获取记录总数						
						foreach($postsCount as $postnumber){
						     $countnumber=$postnumber->postCount;
							 //echo "第一个".$countnumber;
						}
						
						$pages=intval($countnumber/$pagesize); //计算总页数

						if ($countnumber % $pagesize) $pages++;

						//设置缺省页码
						//↓判断“当前页码”是否赋值过
						if (isset($_GET['page'])){ 
							$page=intval($_GET['page']); 
						    //echo "有没有执行！";
						}else{ $page=1; }//否则，设置为第一页

						//↓计算记录偏移量
							$offset=$pagesize*($page - 1);

						//↓读取指定记录数
							//echo "offset=".$offset;
							$rs=web_admin_array_post($offset,$pagesize,$sid,"post");//取得—当前页—记录集！
							
							//一个function活的总个数
							//foreach (){$curNem=iii->as bianliang}
							//$curNum = mysql_num_rows($rs); //$curNum - 当前页实际记录数，for循环输出用
							
							$arraysCount=web_admin_array_post_count($offset,$pagesize,$sid,"post");//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
						
							foreach($arraysCount as $arraynumber){
							     $count_number=$arraynumber->arrayCount;
								 //echo $count_number;
							}
					?> 
					
					<?php foreach($rs as $mov2_item)
						{	/*如果没有http，证明为内链，加上home_url显示，插入时，判断有再截取入库*/
							$tmp = stristr($mov2_item->guid,"http");
							if(($tmp===false)&&(!empty($mov2_item->guid))){
								$gallerylink=home_url().$mov2_item->guid;
							}else{				
								$gallerylink=$mov2_item->guid;
							}
							
							echo "<tr>";
							echo "	<td><input type='checkbox' id='myCheck' name='inputCheckBox' value='".$gallerylink."'/></td>";
							echo "	<td>$mov2_item->post_title</td>";
							//echo "	<td>$mov2_item->post_author</td>";
							echo "	<td>$mov2_item->post_date</td>";
							echo "<td><input type='button' onClick='deletePost({$mov2_item->ID})' name='del' id='buttondel' value='删除' class='btn btn-sm btn-default'/>"  ;
							echo "<input type='button' onClick='updatePost({$mov2_item->ID})' name='upd' id='buttonupd' value='修改' class='btn btn-sm btn-primary' /> </td>" ;
							echo "</tr>	";
						}
						?>
						
					</table>
					</div>
					<div style="margin-left:30px">
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
								echo "<a href='?beIframe&page=".$prev."&sid=".$sid."'> 上一页</a>  ";
							}

						if ($page < $pages)
							{
								echo "<a href='?beIframe&page=".$next."&sid=".$sid."'>下一页</a>  ";
								echo "<a href='?beIframe&page=".$last."&sid=".$sid."'>尾页</a>  ";
							}

							//============================//
							//  翻页显示 二               
							//============================//
						echo " | 共有".$pages."页(".$page."/".$pages.")";

						for ($i=1;$i< $page;$i++){echo "<a href='?beIframe&page=".$i."&sid=".$sid."'>[".$i ."]</a>  ";}  // 1-先输出当前页之前的

						if ($page > 0) echo "[".$page."]";; // 2-再输出当前页

						for ($i=$page+1;$i<=$pages;$i++){echo "<a href='?beIframe&page=".$i."&sid=".$sid."'>[".$i ."]</a>  ";}// 3-接着输出当前页之后

							//echo "转到第 <INPUT maxLength=3 size=3 value=".($pag+1)." name=gotox> 页 <INPUT hideFocus onclick=\"location.href='?pag=gotox.value&sid=".$sid."';\" type=button value=Go name=cmd_goto>"; 

							echo "</p>";

					?>
					</div>	
				</div>
				<div align=right style="margin-right:30px">  
					<input type='button'  id='buttonqd' value='确定' class="btn btn-primary" onclick='OK();'/> 
					<input type='button'  id='buttoncle' value='取消' class="btn btn-default" onclick='Cancle();'/> 
				</div>
			</div>
		</form><!--主体结束--onClick='completeSite()'-->			
	<!--</div>-->
</div>
</body>

<!--<script language='javascript'>
	function completeSite() {			
	}		
		
</script>-->
<script language='javascript'>
		
	var xmlHttp;
    function createXMLHttpRequest(){
    if(window.ActiveXObject)
     xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
    else if(window.XMLHttpRequest)
     xmlHttp = new XMLHttpRequest();
    }

	function deletePost(Id){	   
		createXMLHttpRequest();
		xmlHttp.open("GET","<?php echo constant("CONF_THEME_DIR"); ?>/wesite/common/page_delete.php?beIframe&postid="+Id,true);
		xmlHttp.onreadystatechange = function(){
			//if(xmlHttp.readyState == 4 && xmlHttp.status == 200)
			//alert("服务器返回: " + xmlHttp.responseText);
			window.location.reload();
		}
		xmlHttp.send(null);
	}
		
	function updateSite(id){
		
		 window.param=id;
	     location.href='<?php echo constant("CONF_THEME_DIR"); ?>/wesite/common/post_insert_dialog.php?beIframe&siteId='+id;
	    xmlHttp.onreadystatechange = function(){
			//if(xmlHttp.readyState == 4 && xmlHttp.status == 200)
			//alert("服务器返回: " + xmlHttp.responseText);
			window.location.reload();
			}
			}
	
	function updatePost(id){
	   
	   window.param=id;
	 
	   window.open('post_insert_dialog.php?refreshOpener=yes&beIframe&postid='+id,'_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')
	 xmlHttp.onreadystatechange = function(){
			//if(xmlHttp.readyState == 4 && xmlHttp.status == 200)
			//alert("服务器返回: " + xmlHttp.responseText);
			window.location.reload();
			}
	
	}
function OK(){
	var aCheckBox=document.getElementsByName('inputCheckBox');

		for(var i=0; i<aCheckBox.length; i++){
			if(aCheckBox[i].getAttribute('type')=='checkbox'){
				if(aCheckBox[i].checked==true){
				//document.write(aCheckBox[i].value);
				//location.href='<?php echo constant("CONF_THEME_DIR"); ?>/wesite/mobilepagev2/menu_insert_dialog.php?posturl='+aCheckBox[i].value;
					opener.Wmenuurl.style.visibility="visible";
					opener.Wmenuurl.value=aCheckBox[i].value;
					
					window.close();	
				}
			
		}
}
}
function Cancle(){
	var aCheckBox=document.getElementsByName('inputCheckBox');

		for(var i=0; i<aCheckBox.length; i++)
		{
			if(aCheckBox[i].getAttribute('type')=='checkbox')
			{
				aCheckBox[i].checked=false;
			}
		}
}		
</script>
</html>
<?php get_sidebar( 'front' ); ?>
<?php get_footer(); ?>
