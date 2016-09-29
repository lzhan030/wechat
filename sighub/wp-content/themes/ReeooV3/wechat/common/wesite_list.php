<?php
@session_start(); 

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
global  $current_user;
if( !isset($current_user->user_login)|| empty($current_user->user_login)){
		wp_redirect(wp_login_url());
}
    include '../../wesite/common/dbaccessor.php';
    include '../../wesite/common/web_constant.php';
    global $wpdb;
   //判断是否是分组管理员中的用户
	$groupadminflag = web_admin_issuperadmin($current_user->ID);
	$currentuser =  ((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($groupadminflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
	//$currentuser= (is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
	
   //2014-07-15新增修改
   $gweid = $_SESSION['GWEID'];
	//获取所有的site
	$site_list=web_admin_list_site($currentuser);
	
	//为了默认选中
	$sidsel = $_GET['sidsel'];
?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<!--<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/table.css" />-->
	<link rel="pingback" href="<?php bloginfo('stylesheet_url'); ?>" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/table1.css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js"></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css" />
	<title>站点选择</title>
</head>
<body>
	<div id="primary" class="site-content">
		<div id="content" role="main">
			<form action="<?php echo constant("CONF_THEME_DIR"); ?>/wesite/common/website_create.php?beIframe" onSubmit="return validate()" method="post" >
				<div>
					<div class="main-title">
						<div class="title-1" style="padding-left:0px;">当前位置：微官网 > 
							<font class="fontpurple">站点列表 </font>
						</div>
					</div>
					<div class="bgimg"></div>
					<div class="submenu">
						<div class="panel panel-default" style="margin-left:30px; margin-right:0px; margin-top:30px" >
							<div class="panel-heading">已建站点列表</div>
							<table class="table table-striped" border="1" align="center">
								<tr>
									<th scope="col" width="50"></th> 
									<th scope="col" width="100">编号</th>
									<th scope="col" width="200">网站名称</th>
									<th scope="col" width="200">网站类型</th>
									<th scope="col" width="200">链接</th>
								</tr>
								<?php 
								$pagesize=7; //设定每一页显示的记录数						
								//-----------------------------------------------------------------------------------------------//
								//分页逻辑处理
								//-----------------------------------------------------------------------------------------------
								//$tmpArr = mysql_fetch_array($rs);
						//		$numAL = mysql_num_rows($site_list);  //取得记录总数$rs
								
								//$websitesCount=web_admin_count_website($currentuser);//获取记录总数
								//2014-07-15新增修改
								$websitesCount=web_admin_count_websiteNew($currentuser, $gweid);
								foreach($websitesCount as $websitenumber){
									 $countnumber=$websitenumber->websiteCount;
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
									//$rs=web_admin_array_website($offset,$pagesize,$currentuser);//取得—当前页—记录集！
									//2014-07-15新增修改
									$rs=web_admin_array_websiteNew($offset,$pagesize,$currentuser,$gweid);//取得—当前页—记录集！
									
									//一个function活的总个数
									//foreach (){$curNem=iii->as bianliang}
									//$curNum = mysql_num_rows($rs); //$curNum - 当前页实际记录数，for循环输出用
									
									//$arraysCount=web_admin_array_website_count($offset,$pagesize,$currentuser);
									//2014-07-15新增修改
									$arraysCount=web_admin_array_website_countNew($offset,$pagesize,$currentuser, $gweid);
									foreach($arraysCount as $arraynumber){
										 $count_number=$arraynumber->arrayCount;
										 //echo $count_number;
									}
							?> 
							<?php   foreach($rs as $site){
									$stid=$site->themes_key;
									$template=web_admin_template($stid,$site->id);
									$check= $sidsel == $site->id ? "checked='checked'" : '';
									foreach($template as $tplate){
									echo "<tr>";
									echo "<td><input type='radio' {$check} id='myRadio' name='inputRadio' value='".home_url().$site->site_url."'/></td>";									
									echo "<td>$site->id </td>";					
									echo "<td>$site->site_name</td>";
									echo "<td>$tplate->themename</td>";
									echo "<td>".home_url()."$site->site_url</td>";
									echo "<input name='site_id' type='hidden' id='site_id' value='{$site->id}' maxlength='100' /> ";
									echo "</tr>";
								}
								}
							?>
							</table>
						</div>
						<?php
						//============================//
						//  翻页显示 一               
						//============================//
							echo '<p style="margin-left: 33px;">';  //  align=center
							$first=1;
							$prev=$page-1;   
							$next=$page+1;
							$last=$pages;

						if ($page > 1)
							{
								echo "<a href='?beIframe&page=".$first."&sidsel=".$sidsel."'>首页</a>  ";
								echo "<a href='?beIframe&page=".$prev."&sidsel=".$sidsel."'>上一页</a>  ";
							}

						if ($page < $pages)
							{
								echo "<a href='?beIframe&page=".$next."&sidsel=".$sidsel."'>下一页</a>  ";
								echo "<a href='?beIframe&page=".$last."&sidsel=".$sidsel."'>尾页</a>  ";
							}

							//============================//
							//  翻页显示 二               
							//============================//
						echo " | 共有 ".$pages." 页(".$page."/".$pages.")";

						for ($i=1;$i< $page;$i++){echo "<a href='?beIframe&page=".$i."&sidsel=".$sidsel."'>[".$i ."]</a>  ";}  // 1-先输出当前页之前的

						if ($page > 0) echo " [".$page."]";; // 2-再输出当前页

						for ($i=$page+1;$i<=$pages;$i++){echo "<a href='?beIframe&page=".$i."&sidsel=".$sidsel."'>[".$i ."]</a>  ";}// 3-接着输出当前页之后

							//echo "转到第 <INPUT maxLength=3 size=3 value=".($page+1)." name=gotox> 页 <INPUT hideFocus onclick=\"location.href='?page=gotox.value';\" type=button value=Go name=cmd_goto>"; 
							echo "</p>";

					?>
						<div align="center"> 
							<input type='button'  id='buttonqd' value='确定' class='btn btn-sm btn-info' onclick='OK();'/> 
							<input type='button'  id='buttoncle' value='取消' class='btn btn-sm btn-warning' onclick='Cancle();'/>
						</div>
					</div>
				</div>
			</form>
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

	function deleSite(id){	   
		createXMLHttpRequest();
		xmlHttp.open("GET","<?php echo constant("CONF_THEME_DIR"); ?>/wesite/common/website_delete.php?beIframe&siteId="+id,true);
		xmlHttp.onreadystatechange = function(){
			//if(xmlHttp.readyState == 4 && xmlHttp.status == 200)
			//alert("服务器返回: " + xmlHttp.responseText);
			window.location.reload();
		}
		xmlHttp.send(null);
	}
		
	function updateSite(id){
		
		window.param=id;
	    location.href='<?php echo constant("CONF_THEME_DIR"); ?>/wesite/common/website_update_check.php?beIframe&siteId='+id;
	   
	  
	}	
	function OK(){
		if($('#myRadio:checked').val() == undefined){
			alert('您未选中任何微官网');
			return false;
		}
		var aRadio=document.getElementsByName('inputRadio');

		for(var i=0; i<aRadio.length; i++)
		{
			if(aRadio[i].getAttribute('type')=='radio'){
				if(aRadio[i].checked==true)
				{	
					opener.siurl.style.visibility="visible";
					opener.siurl.value=aRadio[i].value;
					//$("#opener.siurl").change();	
					//为了给input赋值的时候能够触发change事件
					var event = document.createEvent('HTMLEvents');
					event.initEvent("change",true,true);
					opener.siurl.dispatchEvent(event);
					window.close();	
				}
			}
		}
	}	
	function Cancle(){
		var aRadio=document.getElementsByName('inputRadio');

		for(var i=0; i<aRadio.length; i++){
			if(aRadio[i].getAttribute('type')=='radio'){
				aRadio[i].checked=false;
			}
		}
		window.opener=null;
		setTimeout("self.close()",0);
	}		
	function validate(){
		if($('#myRadio:checked').val() == undefined){
			alert('您未选中任何微官网');
		}
		return true;
	}
</script>
</html>
<?php get_sidebar( 'front' ); ?>
<?php get_footer(); ?>
