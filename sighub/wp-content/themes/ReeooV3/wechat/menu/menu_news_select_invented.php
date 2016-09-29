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
include_once '../../wesite/common/dbaccessor.php';
include 'menu_permission_check.php';
//obtain current newsid from autoreply page
$selectnewsid = $_GET['selectnewsid'];
$news=material_news_getlist_group($_SESSION['GWEID']);
$menuId=$_GET["menuId"];
$menuType=$_GET["menuType"];
$menuKey=$_GET["menuKey"];
$menuPad=$_GET["menuPad"];
 ?>
 <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
	<meta name="Author" content="SeekEver">
	<meta name="Keywords" content="">
	<meta name="Description" content="">
	<meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery-1.10.2.min.js" type="text/javascript"></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.Jcrop.js" type="text/javascript"></script>
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/reset.css" type="text/css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/jquery.Jcrop.css" type="text/css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/test.css" type="text/css" />
	<script src="<?php bloginfo('template_directory'); ?>/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">

	</head>
	<body>
	<div style="padding:0 30px">
	    <div class="main-title">
			<div class="title-1"><font class="fontpurple">图文列表： </font>
			</div>
		</div>
		<div class="bgimg"></div>
		<div class="submenu">
			<div class="panel panel-default" style="margin-top:30px">
				<div class="panel-heading">已建图文列表</div>				
				<table class="table table-striped" width="800"  border="1" align="center">
						<tr>
							<td align="center" style="font-weight:bold"></td>
							<td align="center" style="font-weight:bold">编号</td>
							<td align="center" style="font-weight:bold">素材名称</td>
							<td align="center" style="font-weight:bold">操作</td>
						</tr>
						
						<?php 
					$pagesize=7; //设定每一页显示的记录数						
					$materialsCount = web_admin_count_material_group($_SESSION['GWEID']);
					foreach($materialsCount as $materialsnumber){
						 $countnumber=$materialsnumber->materialCount;						 
					}
					
					$pages=intval($countnumber/$pagesize); //计算总页数

					if ($countnumber % $pagesize) $pages++;

					//设置缺省页码
					if (isset($_GET['page'])){ $page=intval($_GET['page']); }else{ $page=1; }//否则，设置为第一页
				   
					$offset=$pagesize*($page - 1);

					$rs=web_admin_array_material_group($_SESSION['GWEID'],$offset,$pagesize);//取得—当前页—记录集！
					$arraysCount=web_admin_array_material_count_group($_SESSION['GWEID'],$offset,$pagesize);
					foreach($arraysCount as $arraynumber){
						 $count_number=$arraynumber->arrayCount;
					}
				?> 
						<?php   
						
				        foreach($rs as $ns){
						    $check=$_GET['menuKey']== $ns->news_item_id ? "checked='checked'" : '';
							echo "<tr>";								
							echo "<td width=50 style='text-align:center'><input type='radio' {$check} id='myCheck' name='inputCheckBox' value='".$ns->news_item_id."'/></td>";					
							echo "<td style='text-align:center'>$ns->news_item_id</td>";
							echo "<td style='text-align:center'>$ns->news_name</td>";
							echo "<td style='text-align:center'> <input type='button' class='btn btn-sm btn-warning' onclick=\"editMaterial('$ns->news_item_id')\"  value='编辑'> </td>";
							echo "</tr>";
						}
						?>
				</table>	
			</div>
			
			<?php
				
				//  翻页显示 一               
					echo "<p>";  //  align=center
					$first=1;
					$prev=$page-1;   
					$next=$page+1;
					$last=$pages;

				if ($page > 1)
					{
						echo "<a href='?beIframe&page=".$first."&selectnewsid=".$selectnewsid."&menuId=".$menuId."&menuType=".$menuType."&menuKey=".$menuKey."&menuPad=".$menuPad."'>首页</a>  ";
						echo "<a href='?beIframe&page=".$prev."&selectnewsid=".$selectnewsid."&menuId=".$menuId."&menuType=".$menuType."&menuKey=".$menuKey."&menuPad=".$menuPad."'>上一页</a>  ";
					}

				if ($page < $pages)
					{
						echo "<a href='?beIframe&page=".$next."&selectnewsid=".$selectnewsid."&menuId=".$menuId."&menuType=".$menuType."&menuKey=".$menuKey."&menuPad=".$menuPad."'>下一页</a>  ";
						echo "<a href='?beIframe&page=".$last."&selectnewsid=".$selectnewsid."&menuId=".$menuId."&menuType=".$menuType."&menuKey=".$menuKey."&menuPad=".$menuPad."'>尾页</a>  ";
					}

					
				//  翻页显示 二               
				echo " | 共有 ".$pages." 页(".$page."/".$pages.")";

				for ($i=1;$i< $page;$i++){echo "<a href='?beIframe&page=".$i."&selectnewsid=".$selectnewsid."&menuId=".$menuId."&menuType=".$menuType."&menuKey=".$menuKey."&menuPad=".$menuPad."'>[".$i ."]</a>  ";}  // 1-先输出当前页之前的

				if ($page > 0) echo " [".$page."]";; // 2-再输出当前页

				for ($i=$page+1;$i<=$pages;$i++){echo "<a href='?beIframe&page=".$i."&selectnewsid=".$selectnewsid."&menuId=".$menuId."&menuType=".$menuType."&menuKey=".$menuKey."&menuPad=".$menuPad."'>[".$i ."]</a>  ";}// 3-接着输出当前页之后
				echo "</p>";

			?>
			
			<div style="text-align:center">
					<input class="savedata btn btn-primary btmtxtbtn" type="button" value="保存" />
					<input class="cancel btn btn-default btmtxtbtn" type="button" value="取消" />
			</div>
			
		</div>
	</div>

<script type="text/javascript">
	    $(".cancel").click(function(){
			var aCheckBox=document.getElementsByName('inputCheckBox');

			for(var i=0; i<aCheckBox.length; i++)
			{
				if(aCheckBox[i].getAttribute('type')=='checkbox')
				{
					aCheckBox[i].checked=false;
				}
			}
			window.opener=null;
			setTimeout("self.close()",0);
	    })
		
		function editMaterial(nid){
			window.showModalDialog('<?php echo get_template_directory_uri(); ?>/wechat/material/material_edit.php?beIframe&netId='+nid,'_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')			
			window.location.reload();
		}
		
		$(".savedata").click(function(){
			var m=0;
			var aCheckBox=document.getElementsByName('inputCheckBox');
			 var title=$("#title").val();
			 var itemUrl=$("#itemUrl").val();
			 var itemId=$("#itemId").val();
			
			var menuId=<?php echo $menuId ?>;
			var menuType='<?php echo $menuType ?>';
			var menuKey='<?php echo $menuKey ?>';
			var menuPad=<?php echo $menuPad ?>;
			for(var i=0; i<aCheckBox.length; i++){
				if(aCheckBox[i].getAttribute('type')=='radio'){
					if(aCheckBox[i].checked==true){
						var nid = aCheckBox[i].value;
						opener.location.href='<?php echo get_template_directory_uri(); ?>/wechat/menu/menu_invented.php?beIframe&menusecid='+menuId+'&isselcet=1&newid='+nid;
						m=m+1;
						window.close();	
					}
				}
			}
			if(m==0){
				alert("请先选择一个素材！");
			}
		})
		</script>		
	</body>
</html>