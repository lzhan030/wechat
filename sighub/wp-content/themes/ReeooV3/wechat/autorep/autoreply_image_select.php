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
 
include_once '../../wesite/common/dbaccessor.php';
include_once '../common/wechat_dbaccessor.php';
include_once 'autoreply_permission_check.php';
//obtain current newsid from autoreply page
$selectnewsid = $_GET['selectnewsid'];
$news=material_news_getlist_group($_SESSION['GWEID']);

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
		<input class="btn btn-primary" type="button" value="创建新图文" onclick="createNews()" ></input>
		<div class="submenu">
		<div class="panel panel-default" style="margin-top:10px;margin-bottom:10px">
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
					$check= $selectnewsid == $ns->news_item_id ? "checked='checked'" : '';

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
						echo "<a href='?beIframe&page=".$first."&selectnewsid=".$selectnewsid."'>首页</a>  ";
						echo "<a href='?beIframe&page=".$prev."&selectnewsid=".$selectnewsid."'>上一页</a>  ";
					}

				if ($page < $pages)
					{
						echo "<a href='?beIframe&page=".$next."&selectnewsid=".$selectnewsid."'>下一页</a>  ";
						echo "<a href='?beIframe&page=".$last."&selectnewsid=".$selectnewsid."'>尾页</a>  ";
					}

					//============================//
					//  翻页显示 二               
					//============================//
				echo " | 共有 ".$pages." 页(".$page."/".$pages.")";

				for ($i=1;$i< $page;$i++){echo "<a href='?beIframe&page=".$i."&selectnewsid=".$selectnewsid."'>[".$i ."]</a>  ";}  // 1-先输出当前页之前的

				if ($page > 0) echo " [".$page."]";; // 2-再输出当前页

				for ($i=$page+1;$i<=$pages;$i++){echo "<a href='?beIframe&page=".$i."&selectnewsid=".$selectnewsid."'>[".$i ."]</a>  ";}// 3-接着输出当前页之后

					//echo "转到第 <INPUT maxLength=3 size=3 value=".($page+1)." name=gotox> 页 <INPUT hideFocus onclick=\"location.href='?page=gotox.value';\" type=button value=Go name=cmd_goto>"; 
					echo "</p>";

			?>
			
			<div style="text-align:center;margin-bottom:10px">
					<input class=" btn btn-primary btmtxtbtn" type="button" value="保存" onclick="OK()"/>
					<input class="btn btn-default btmtxtbtn"  type="button" value="取消" onclick="Cancle()"/>
			</div>
		</div>
	</div>
		<script type="text/javascript">
		
		function OK(){
			var m=0;
			var aCheckBox=document.getElementsByName('inputCheckBox');

			for(var i=0; i<aCheckBox.length; i++){
				if(aCheckBox[i].getAttribute('type')=='radio'){
					if(aCheckBox[i].checked==true){
						var nid = aCheckBox[i].value;
						opener.location.href='<?php echo get_template_directory_uri(); ?>/wechat/autorep/autoreply.php?beIframe&isselcet=1&tab=1&newsid='+nid;		
						m=m+1;
						window.close();	
					}
				}
			}
			if(m==0){
				alert("请先选择一个素材！");
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
			window.opener=null;
			setTimeout("self.close()",0);
	    }
		
		function editMaterial(nid)
		{
		    window.showModalDialog('<?php echo get_template_directory_uri(); ?>/wechat/material/material_edit.php?beIframe&netId='+nid,'_blank','dialogWidth=920;dialogHeight=600;center=yes;scroll=yes;resizable=no;status=no')		
						

			var url="<?php echo get_template_directory_uri(); ?>";	
			var newsel="<?php echo $selectnewsid; ?>";
			window.location.href=url+'/wechat/autorep/autoreply_image_select.php?beIframe&artType=post&selectnewsid='+newsel;
		}
		
		function createNews(){	   
			window.showModalDialog('<?php echo get_template_directory_uri(); ?>/wechat/material/material_edit.php?beIframe&netId=0','_blank','dialogWidth=920;dialogHeight=600;center=yes;scroll=yes;resizable=no;status=no')			
			var url="<?php echo get_template_directory_uri(); ?>";
			var newsel="<?php echo $selectnewsid; ?>";
			window.location.href=url+'/wechat/autorep/autoreply_image_select.php?beIframe&artType=post&selectnewsid='+newsel;
		}
		
	</script>
	</body>
</html>