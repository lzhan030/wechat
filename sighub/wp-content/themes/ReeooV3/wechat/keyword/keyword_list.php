<?php
$tmp_path = explode ( 'ReeooV3', __FILE__ );
$template_path=$tmp_path[0];
require_once $template_path.'ReeooV3/wechat/common/session.php';

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
global  $current_user;
if( !isset($current_user->user_login)|| empty($current_user->user_login)){
		wp_redirect(wp_login_url());
}	
get_header(); 
include '../common/wechat_dbaccessor.php';
include 'keyword_permission_check.php';
//get all keywords list
$keywords=wechat_mess_kw_list_group($_SESSION['GWEID']);
?>
 
 <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
	<meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js" type="text/javascript"></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.Jcrop.js" type="text/javascript"></script>
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/reset.css" type="text/css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/jquery.Jcrop.css" type="text/css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/test.css" type="text/css" />	
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/table1.css" />
	<script src="<?php bloginfo('template_directory'); ?>/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/js/editor/themes/default/default.css" />
	<script charset="utf-8" src="<?php bloginfo('template_directory'); ?>/js/editor/kindeditor.js"></script>
	<script charset="utf-8" src="<?php bloginfo('template_directory'); ?>/js/editor/lang/zh_CN.js"></script>
	<style>
		.panel{border-radius: 0px;-webkit-box-shadow: 0 0px 0px rgba(0,0,0,0.05);box-shadow: 0 0px 0px rgba(0,0,0,0.05);}
		.alert{border-radius: 0px;}
	</style>
	</head>
	<body>
		<form>
		<div class="main_auto">
			<div class="main-title">
				<div class="title-1">当前位置：关键词应答> <font class="fontpurple">关键词列表 </font>
				</div>
			</div>
			<div class="bgimg"></div>
			<div class="submenu">
				<div>
					<input type="button" class="btn btn-primary" value="创建新关键词" onclick="newKeyword()" style="margin-top:10px;width:120px"/>
				</div>
				<div class="panel panel-default" style="margin-top:30px">
					<div class="panel-heading">已建关键词列表</div>
					<table class="table table-striped" width="800"  border="1" align="center">
						<tr>
							<th>编号</th>
							<th>关键词</th>
							<th>类型</th>
							<th>操作</th>
						</tr>
						<!--分页逻辑处理-->
						<?php 
						$pagesize=7; //设定每一页显示的记录数
						$kwCount=webchat_mess_kw_count_group($_SESSION['GWEID']);//获取记录总数
						foreach($kwCount as $count){
							$countnumber=$count->kwCount;
						}
								
						$pages=intval($countnumber/$pagesize); //计算总页数

						if ($countnumber % $pagesize) $pages++;

						//设置缺省页码
						//↓判断“当前页码”是否赋值过
						if (isset($_GET['page'])){ $page=intval($_GET['page']); }else{ $page=1; }//否则，设置为第一页
						//↓计算记录偏移量
						$offset=$pagesize*($page - 1);
						//↓读取指定记录数
						$keywords=wechat_array_kw_group($offset,$pagesize,$_SESSION['GWEID']);//取得—当前页—记录集！
							
						$arraysCount=wechat_array_kw_count_group($offset,$pagesize,$_SESSION['GWEID']); //当前页的个数
						foreach($arraysCount as $arraynumber){
							$count_number=$arraynumber->arrayCount;
						}
						?> 
						<?php   
						foreach($keywords as $keyword){
							echo "<tr>";								
							echo "<td>$keyword->arply_id</td>";		
							echo "<td>$keyword->arply_keyword</td>";							
							echo "<td>";
							if ($keyword->arply_type == 'weChat_news') {
								echo "图文消息";
							} else {
								echo "文本消息";
							}
							echo "</td>";
							echo "<td class='row'><input name='keyword_id' type='hidden' id='site_id' value='{$keyword->arply_id}' maxlength='100' /> ";
							echo "<input type='button' class='btn btn-sm btn-warning' onClick='deleSite({$keyword->arply_id})' name='del' id='buttondel' value='删除' class='btn_add'> " ;
							echo "<input type='button' class='btn btn-sm btn-info' onClick='updateKeyword({$keyword->arply_id})' name='upd' id='buttonupd' value='更新' class='btn_add'> </td>" ;
							echo "</tr>";
						}
						?>
					</table>
				</div>
				<?php
				// 翻页显示 1              
				echo "<p>";  //  align=center
				$first=1;
				$prev=$page-1;   
				$next=$page+1;
				$last=$pages;

				if ($page > 1) {
					echo "<a href='?beIframe&page=".$first."'>首页</a>  ";
					echo "<a href='?beIframe&page=".$prev."'>上一页</a>  ";
				}
				if ($page < $pages) {
					echo "<a href='?beIframe&page=".$next."'>下一页</a>  ";
					echo "<a href='?beIframe&page=".$last."'>尾页</a>  ";
				}

				//  翻页显示 2              
				echo " | 共有 ".$pages." 页(".$page."/".$pages.")";

				for ($i=1;$i< $page;$i++){
					echo "<a href='?beIframe&page=".$i."'>[".$i ."]</a>  ";
				}  // 1-先输出当前页之前的
				if ($page > 0) echo " [".$page."]";; // 2-再输出当前页
				for ($i=$page+1;$i<=$pages;$i++){
					echo "<a href='?beIframe&page=".$i."'>[".$i ."]</a>  ";
				}// 3-接着输出当前页之后
				echo "</p>";
				?>
			</div>
		</div>	
		</form>
	</body>
	<script language='javascript'>
		var xmlHttp;
		function createXMLHttpRequest(){
			if(window.ActiveXObject){
				xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
			}else if(window.XMLHttpRequest) {
				xmlHttp = new XMLHttpRequest();
			}else {
				alert("您的浏览器不支持XMLHTTP！");
			}
		}	
	
	function newKeyword() {
	    location.href='<?php echo constant("CONF_THEME_DIR"); ?>/wechat/keyword/new_keyword.php?beIframe';
	}
	
	function updateKeyword(id){
		window.param=id;
	    location.href='<?php echo constant("CONF_THEME_DIR"); ?>/wechat/keyword/keyword.php?beIframe&isupdate&keywordId='+id;
	}

	function deleSite(keywordId) {
		if(confirm("确定删除吗？")){
			window.param=keywordId;
			createXMLHttpRequest();
			xmlHttp.open("GET","<?php echo constant("CONF_THEME_DIR");?>/wechat/keyword/delete_keyword.php?keywordId="+keywordId, true);
			xmlHttp.onreadystatechange = function(){
				if (xmlHttp.readyState==4) {
					if (xmlHttp.status==200||xmlHttp.status==0) {
						alert("关键词删除成功！");
					} else {
						alert("关键词删除失败！");
					}
				}
				window.location.reload();
			}
			xmlHttp.send(null);
		}
	}
	</script>
</html>