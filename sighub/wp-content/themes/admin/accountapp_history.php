<?php

require_once './wp-content/themes/ReeooV3/wesite/common/dbaccessor.php';

if(isset($_GET['del']) && !empty($_GET['del']) ){
	require_once './wp-admin/includes/user.php';
	wp_delete_user( $_GET['del'] );
	}
?>

<div class="main-title">
	<div class="title-1">当前位置：用户公众号数目管理 > <font class="fontpurple">历史公众号数目列表 </font></div>
</div>
<input type="button" class="btn btn-primary" onclick="location.href='?admin&page=accountappmgt'" name="del" id="buttondel" value="返回" style="width:130px;margin-top:10px;">				
<div class="panel panel-default" style="margin-right:50px; margin-top:10px">
	<div class="panel-heading">历史公众号申请列表</div>
	<table class="table table-striped" width="800" border="1" align="center">
	<tbody>
		<tr>
			<td scope="col" width="90" align="center" style="font-weight:bold">申请编号</td>
			<td scope="col" width="110" align="center" style="font-weight:bold">用户名</td>
			<td scope="col" width="130" align="center" style="font-weight:bold">申请公众号数目</td>
			<td scope="col" width="120" align="center" style="font-weight:bold">申请时间</td>
			<td scope="col" width="100" align="center" style="font-weight:bold">状态</td>
			<td scope="col" width="130" align="center" style="font-weight:bold">申请理由</td>
		</tr>
		
		
		<?php 
			$pagesize=10; //设定每一页显示的记录数						
			$accountappCount = web_admin_count_accountappall();
			
			foreach($accountappCount as $accountsnumber){
				 $countnumber=$accountsnumber->accountappcount;
			}
			$pages=intval($countnumber/$pagesize); //计算总页数
			if ($countnumber % $pagesize) $pages++;
			//↓判断“当前页码”是否赋值过
			if (isset($_GET['accountapphistorypage'])){ $page=intval($_GET['accountapphistorypage']); }else{ $page=1; }//否则，设置为第一页
			//↓计算记录偏移量
			$offset=$pagesize*($page - 1);
			//↓读取指定记录数
			$rs=web_admin_array_accountappall($offset,$pagesize);//取得—当前页—记录集！
		?> 
		
		<?php
		foreach ($rs as $accountapp) {
		?>
		<tr>
			<td align="center"><?php echo $accountapp->id; ?> </td>
			<td align="center"><?php echo $accountapp->user_nicename; ?> </td>
			<td align="center"><?php echo $accountapp->app_account; ?></td>
			<td align="center"><?php echo $accountapp->time; ?></td>
			<td align="center"><?php 
				if($accountapp->status == 0) {echo "未审核";} 
				if($accountapp->status == 1) {echo "审核通过";} 
				if($accountapp->status == -1) {echo "审核未通过";}?>
			</td>
			<td align="center"><?php echo $accountapp->desc; ?></td>
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

		if ($page > 1) {
			echo "<a href='?admin&page=accountapp_history&accountapphistorypage=".$first."'>首页</a>  ";
			echo "<a href='?admin&page=accountapp_history&accountapphistorypage=".$prev."'>上一页</a>  ";
		}

		if ($page < $pages)	{
			echo "<a href='?admin&page=accountapp_history&accountapphistorypage=".$next."'>下一页</a>  ";
			echo "<a href='?admin&page=accountapp_history&accountapphistorypage=".$last."'>尾页</a>  ";
		}

		//============================//
		//  翻页显示 二               
		//============================//
		echo " | 共有 ".$pages." 页(".$page."/".$pages.")";
		for ($i=1;$i< $page;$i++){echo "<a href='?admin&page=accountapp_history&accountapphistorypage=".$i."'>[".$i ."]</a>  ";}  // 1-先输出当前页之前的
		if ($page > 0) echo " [".$page."]";; // 2-再输出当前页
		for ($i=$page+1;$i<=$pages;$i++){echo "<a href='?admin&page=accountapp_history&accountapphistorypage=".$i."'>[".$i ."]</a>  ";}// 3-接着输出当前页之后
		echo "</p>";
	?>
