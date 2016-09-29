<?php

require_once './wp-content/themes/ReeooV3/wesite/common/dbaccessor.php';

if(isset($_GET['del']) && !empty($_GET['del']) ){
	require_once './wp-admin/includes/user.php';
	wp_delete_user( $_GET['del'] );
	}
?>

<div class="main-title">
	<div class="title-1">当前位置：菜单管理 > <font class="fontpurple">菜单模板列表 </font>
	</div>
</div>
<div class="bgimg"></div>
<input type="button" class="btn btn-primary" onclick="window.open('?admin&page=adminmenu/add_menu_name&header=0','_blank','height=320,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')" name="del" id="buttondel" value="添加菜单模板名称" style="margin-top:10px;">	
<div class="panel panel-default" style="margin-right:50px; margin-top:30px">
	<div class="panel-heading">菜单模板列表</div>
	<table class="table table-striped" width="800" border="1" align="center">
	<tbody>
		<tr>
			<td scope="col" width="100" align="center" style="font-weight:bold">编号</td>
			<td scope="col" width="150" align="center" style="font-weight:bold">菜单模板名称</td>
			<td scope="col" width="150" align="center" style="font-weight:bold">操作</td>
		</tr>
		
		
		<?php 
			$pagesize=5; //设定每一页显示的记录数
			//-----------------------------------------------------------------------------------------------//
			//分页逻辑处理
			//-----------------------------------------------------------------------------------------------
			//$tmpArr = mysql_fetch_array($rs);
	        //$numAL = mysql_num_rows($site_list);  //取得记录总数$rs
			
			//$websitesCount=web_admin_count_website($current_user->ID);//获取记录总数
			$menuCount = web_admin_count_adminmenu();
			//echo $usersCount;//获取wp_users表的记录总数
			
			foreach($menuCount as $menusnumber){
				 $countnumber=$menusnumber->menuCount;
				 //echo $countnumber;
			}
			
			$pages=intval($countnumber/$pagesize); //计算总页数

			if ($countnumber % $pagesize) $pages++;

			//设置缺省页码
			//↓判断“当前页码”是否赋值过
			if (isset($_GET['userpage'])){ $page=intval($_GET['userpage']); }else{ $page=1; }//否则，设置为第一页
           
			//↓计算记录偏移量
				$offset=$pagesize*($page - 1);

			//↓读取指定记录数
				//$rs=web_admin_array_website($offset,$pagesize,$current_user->ID);//取得—当前页—记录集！
				$rs=web_admin_array_adminmenu($offset,$pagesize);//取得—当前页—记录集！
				
				//一个function活的总个数
				//foreach (){$curNem=iii->as bianliang}
				//$curNum = mysql_num_rows($rs); //$curNum - 当前页实际记录数，for循环输出用
				
				//$arraysCount=web_admin_array_website_count($offset,$pagesize,$current_user->ID);
				$msCount=web_admin_array_adminmenu_count($offset,$pagesize);
				foreach($msCount as $arraynumber){
					 $count_number=$arraynumber->arrayCount;
					 //echo $count_number;
				}
		?> 
		
		
		<?php
		//$blogusers = get_users('meta_key=wp_user_level&meta_value=2&orderby=ID');
		//foreach ($blogusers as $user) {
		foreach ($rs as $menu) {
		?>
		<tr>
			<td align="center"><?php echo $menu->M_id; ?> </td>
			<td align="center"><?php echo $menu->M_name; ?></td>
			<td class="row" align="center"><input type="button" class="btn btn-sm btn-default" onclick="location.href='?admin&page=adminmenu/add_new_menu&Mid=<?php echo $menu->M_id; ?>&Mname=<?php echo $menu->M_name?>'" name="upd" id="buttonupd" value="菜单编辑"> 
			<input name="site_id" type="hidden" id="site_id" value="308" maxlength="100"> <input type="button" class="btn btn-sm btn-warning" onClick="delemenu(<?php echo $menu->M_id; ?>)" name="del" id="buttondel" value="删除"> 
			<input type="button" class="btn btn-sm btn-info" onclick="window.open('?admin&page=adminmenu/updatemenu&header=0&Mid=<?php echo $menu->M_id; ?>&Mname=<?php echo $menu->M_name?>','_blank','height=320,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')" name="upd" id="buttonupd" value="更新菜单名称"> </td>
		</tr>
		<?php
		}
        ?>
		</tr>
	</tbody>
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
		echo "<a href='?admin&page=adminmenu&userpage=".$first."'>首页</a>  ";
		echo "<a href='?admin&page=adminmenu&userpage=".$prev."'>上一页</a>  ";
		}

		if ($page < $pages)
		{
		echo "<a href='?admin&page=adminmenu&userpage=".$next."'>下一页</a>  ";
		echo "<a href='?admin&page=adminmenu&userpage=".$last."'>尾页</a>  ";
		}

		//============================//
		//  翻页显示 二               
		//============================//
		echo " | 共有 ".$pages." 页(".$page."/".$pages.")";

		for ($i=1;$i< $page;$i++){echo "<a href='?admin&page=adminmenu&userpage=".$i."'>[".$i ."]</a>  ";}  // 1-先输出当前页之前的

		if ($page > 0) echo " [".$page."]";; // 2-再输出当前页

		for ($i=$page+1;$i<=$pages;$i++){echo "<a href='?admin&page=adminmenu&userpage=".$i."'>[".$i ."]</a>  ";}// 3-接着输出当前页之后

		//echo "转到第 <INPUT maxLength=3 size=3 value=".($page+1)." name=gotox> 页 <INPUT hideFocus onclick=\"location.href='?page=gotox.value';\" type=button value=Go name=cmd_goto>"; 
		echo "</p>";

		?>
					
<script language='javascript'>
		
	var xmlHttp;
    function createXMLHttpRequest(){
    if(window.ActiveXObject)
     xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
    else if(window.XMLHttpRequest)
     xmlHttp = new XMLHttpRequest();
    }

	function delemenu(mid){	   
		if(confirm("确定删除该菜单模板吗？")){	
			$.ajax({
				type:'get',
				dataType: 'json',
				url:"?admin&page=adminmenu/count_menu_del_check&header=0&footer=0&Mid="+mid,
				success: function(data){
					if(data.iscandel=="error"){
						alert("出现错误");
					}else if(data.iscandel=="yes"){
						$.ajax({
							type:'post',
							dataType: 'json',
							url:"?admin&page=adminmenu/count_menu_del&header=0&footer=0&Mid="+mid,
							success: function(data){
								  if(data.status=='error'){
									alert(data.message);
								  }
								  window.location.reload();
							},
				        	error: function(data){
								alert("出现错误");
							}			
						});
					}else{
						var bness="已有微信号"+data.business+" 正在使用该菜单模板，如果删除，则该微信号下的菜单将被删除，是否仍旧强行删除该菜单模板";
						if (confirm(bness)) {
							$.ajax({
								type:'post',
								dataType: 'json',
								url:"?admin&page=adminmenu/count_menu_del&header=0&footer=0&Mid="+mid,
								success: function(data){
									if(data.status=='error'){
										alert(data.message);
									}
									window.location.reload();									
								},
					        	error: function(data){
									alert("出现错误");
								}			
							});						
						}					
					}
				},
		        error: function(data){
					alert("出现错误");
				}			
			});	
		}
	}

</script>	
