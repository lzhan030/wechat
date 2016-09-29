<?php

require_once './wp-content/themes/ReeooV3/wesite/common/dbaccessor.php';

if(isset($_GET['del']) && !empty($_GET['del']) ){
	require_once './wp-admin/includes/user.php';
	wp_delete_user( $_GET['del'] );
	}
?>

<div class="main-title">
	<div class="title-1">当前位置：空间扩容管理 > <font class="fontpurple">历史扩容列表 </font>
	</div>
</div>
<div class="bgimg"></div>
<input type="button" class="btn btn-primary" onclick="location.href='?admin&page=spacemanage'" name="del" id="buttondel" value="返回" style="margin-top:10px;" >				
<div class="panel panel-default" style="margin-right:50px; margin-top:30px">
	<div class="panel-heading">历史扩容列表</div>
	<table class="table table-striped" width="800" border="1" align="center">
	<tbody>
		<tr>
			<td scope="col" width="110" align="center" style="font-weight:bold">用户名</td>
			<td scope="col" width="130" align="center" style="font-weight:bold">申请空间大小</td>
			<td scope="col" width="130" align="center" style="font-weight:bold">描述</td>
			<td scope="col" width="120" align="center" style="font-weight:bold">申请时间</td>
			<td scope="col" width="100" align="center" style="font-weight:bold">状态</td>
		</tr>
		
		
		<?php 
		    
			$pagesize=7; //设定每一页显示的记录数						
			//-----------------------------------------------------------------------------------------------//
			//分页逻辑处理
			//-----------------------------------------------------------------------------------------------
			//$tmpArr = mysql_fetch_array($rs);
	        //$numAL = mysql_num_rows($site_list);  //取得记录总数$rs
			
			//$websitesCount=web_admin_count_website($current_user->ID);//获取记录总数
			$accountsCount = web_admin_count_spaceallaccount();
			//echo $usersCount;//获取wp_users表的记录总数
			
			foreach($accountsCount as $accountsnumber){
				 $countnumber=$accountsnumber->accountCount;
				 
			}
			//echo $countnumber;
			
			$pages=intval($countnumber/$pagesize); //计算总页数

			if ($countnumber % $pagesize) $pages++;

			//设置缺省页码
			//↓判断“当前页码”是否赋值过
			if (isset($_GET['spacehistorypage'])){ $page=intval($_GET['spacehistorypage']); }else{ $page=1; }//否则，设置为第一页
           
			//↓计算记录偏移量
				$offset=$pagesize*($page - 1);

			//↓读取指定记录数
				//$rs=web_admin_array_website($offset,$pagesize,$current_user->ID);//取得—当前页—记录集！
				$rs=web_admin_array_spaceallaccount($offset,$pagesize);//取得—当前页—记录集！
				
				//一个function活的总个数
				//foreach (){$curNem=iii->as bianliang}
				//$curNum = mysql_num_rows($rs); //$curNum - 当前页实际记录数，for循环输出用
				
				//$arraysCount=web_admin_array_website_count($offset,$pagesize,$current_user->ID);
				$arraysCount=web_admin_array_spaceallaccount_count($offset,$pagesize);
				foreach($arraysCount as $arraynumber){
					 $count_number=$arraynumber->arrayCount;
					 //echo $count_number;
				}
		?> 
		
		
		<?php
		//$blogusers = get_users('meta_key=wp_user_level&meta_value=2&orderby=ID');
		//foreach ($blogusers as $user) {
		foreach ($rs as $spaceaccount) {
		?>
		<tr>
			<!--<td align="center"><input type="checkbox" name="checkUser[]" value="check_user" style="20px 10px 0px 50px"></input></td>-->
			<td align="center"><?php echo $spaceaccount->user_nicename; ?> </td>
			<td align="center"><?php echo $spaceaccount->space." M"; ?></td>
			<td align="center"><?php echo $spaceaccount->desc; ?></td>
			<td align="center"><?php echo $spaceaccount->time; ?></td>
			<td align="center"><?php if($spaceaccount->status == 0) {echo "未审核";} else if($spaceaccount->status == 1){echo "审核通过";} else {echo "不通过";}?></td>
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
		echo "<a href='?admin&page=spacehistory&spacehistorypage=".$first."'>首页</a>  ";
		echo "<a href='?admin&page=spacehistory&spacehistorypage=".$prev."'>上一页</a>  ";
		}

		if ($page < $pages)
		{
		echo "<a href='?admin&page=spacehistory&spacehistorypage=".$next."'>下一页</a>  ";
		echo "<a href='?admin&page=spacehistory&spacehistorypage=".$last."'>尾页</a>  ";
		}

		//============================//
		//  翻页显示 二               
		//============================//
		echo " | 共有 ".$pages." 页(".$page."/".$pages.")";

		for ($i=1;$i< $page;$i++){echo "<a href='?admin&page=spacehistory&spacehistorypage=".$i."'>[".$i ."]</a>  ";}  // 1-先输出当前页之前的

		if ($page > 0) echo " [".$page."]";; // 2-再输出当前页

		for ($i=$page+1;$i<=$pages;$i++){echo "<a href='?admin&page=spacehistory&spacehistorypage=".$i."'>[".$i ."]</a>  ";}// 3-接着输出当前页之后

		//echo "转到第 <INPUT maxLength=3 size=3 value=".($page+1)." name=gotox> 页 <INPUT hideFocus onclick=\"location.href='?page=gotox.value';\" type=button value=Go name=cmd_goto>"; 
		echo "</p>";

		?>
	
					
