<?php

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
//get_header(); 
require_once ('../wesite/common/dbaccessor.php');
require_once ('../wechat/common/wechat_dbaccessor.php');

global  $current_user;
//判断是否是分组管理员中的用户
$groupadminflag = web_admin_issuperadmin($current_user->ID);
$user_id =  ((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($groupadminflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
//$user_id =  (is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
//echo $user_id; 
$user = get_userdata( $user_id );  
//echo $user->user_login;
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css" />
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
		<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js"></script>
		<script src="<?php bloginfo('template_directory'); ?>/js/bootstrap.min.js"></script>
		<title>初始化</title>
	</head>
    <body>
	    <div id="commondiv" >
					<div class="panel panel-default" style="margin-right:15px; margin-top:50px">
						<!-- Default panel contents class="gridtable"-->
						<div class="panel-heading" align="center">公用服务号菜单模板预览表</div>
						<table class="table table-striped" width="500"  border="1" align="center">
							<tr>
								<td scope="col"  align="center" width="200" style="font-weight:bold">服务号昵称</td>
								<td scope="col"  align="center" width="200" style="font-weight:bold">菜单模板</td>
								<td scope="col"  align="center" width="150" style="font-weight:bold">操作</td>
							</tr>
							
							<?php 
								$pagesize=3; //设定每一页显示的记录数						
								//-----------------------------------------------------------------------------------------------//
								//分页逻辑处理
								//-----------------------------------------------------------------------------------------------
								//$tmpArr = mysql_fetch_array($rs);
						//		$numAL = mysql_num_rows($site_list);  //取得记录总数$rs
								$menusCount=wechat_view_select_menu();//获取记录总数
								foreach($menusCount as $myrows){
									 $menusnumber=$myrows->m_count;
									//echo "这是公共条目总数".$menusnumber;
								}
								
								$pages=intval($menusnumber/$pagesize); //计算总页数

								if ($menusnumber % $pagesize) $pages++;

								//设置缺省页码
								//↓判断“当前页码”是否赋值过
								if (isset($_GET['page'])){ $page=intval($_GET['page']); }else{ $page=1; }//否则，设置为第一页

								//↓计算记录偏移量
									$offset=$pagesize*($page - 1);

								//↓读取指定记录数
									$rs=web_admin_array_view_menu($offset,$pagesize);//取得—当前页—记录集！
									
									//一个function活的总个数
									//foreach (){$curNem=iii->as bianliang}
									//$curNum = mysql_num_rows($rs); //$curNum - 当前页实际记录数，for循环输出用
									
									//$arraysCount=web_admin_array_website_count($offset,$pagesize,$current_user->ID);
									//foreach($arraysCount as $arraynumber){
										 //$count_number=$arraynumber->arrayCount;
										 //echo $count_number;
									//}
						
							foreach ($rs as $menu) {
							?>
								<tr>
									<td align="center"><?php echo $menu->wechat_nikename; ?> </td>
									<td align="center"><?php echo $menu->M_name; ?></td>
									<td class="row" align="center"><input type="button" class="btn btn-sm btn-info" onclick="window.open('view_menu.php?Mid=<?php echo $menu->M_id; ?>&Mname=<?php echo $menu->M_name?>','_blank','height=320,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')" name="upd" id="buttonupd" value="菜单预览"> 
									</tr>
								<?php
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
								echo "<a href='?beIframe&page=".$first."'>首页</a>  ";
								echo "<a href='?beIframe&page=".$prev."'>上一页</a>  ";
							}

						if ($page < $pages)
							{
								echo "<a href='?beIframe&page=".$next."'>下一页</a>  ";
								echo "<a href='?beIframe&page=".$last."'>尾页</a>  ";
							}

							//============================//
							//  翻页显示 二               
							//============================//
						echo " | 共有 ".$pages." 页(".$page."/".$pages.")";

						for ($i=1;$i< $page;$i++){echo "<a href='?beIframe&page=".$i."'>[".$i ."]</a>  ";}  // 1-先输出当前页之前的

						if ($page > 0) echo " [".$page."]";; // 2-再输出当前页

						for ($i=$page+1;$i<=$pages;$i++){echo "<a href='?beIframe&page=".$i."'>[".$i ."]</a>  ";}// 3-接着输出当前页之后

							//echo "转到第 <INPUT maxLength=3 size=3 value=".($page+1)." name=gotox> 页 <INPUT hideFocus onclick=\"location.href='?page=gotox.value';\" type=button value=Go name=cmd_goto>"; 
							echo "</p>";

					?>
					
				</div>
	</body>
</html>