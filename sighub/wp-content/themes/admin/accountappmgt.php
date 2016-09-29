<?php
	require_once './wp-content/themes/ReeooV3/wesite/common/dbaccessor.php';
	if(isset($_GET['del']) && !empty($_GET['del']) ){
		require_once './wp-admin/includes/user.php';
		wp_delete_user( $_GET['del'] );
	}
?>

<div class="main-title">
	<div class="title-1">当前位置：用户公众号数目管理 > <font class="fontpurple">公众号数目列表 </font>
	</div>
</div>
<input type="button" class="btn btn-primary" onclick="location.href='?admin&page=accountapp_history'" name="del" id="buttondel" value="公众号数目申请历史记录" style="margin-top:10px">
<div class="panel panel-default" style="margin-right:50px; margin-top:10px">
	<div class="panel-heading">用户公众号数目管理列表</div>
	<table class="table table-striped" width="800" border="1" align="center">
	<tbody>
		<tr>
			<td scope="col" width="90" align="center" style="font-weight:bold">申请编号</td>
			<td scope="col" width="110" align="center" style="font-weight:bold">用户名</td>
			<td scope="col" width="130" align="center" style="font-weight:bold">申请公众号数目</td>
			<td scope="col" width="130" align="center" style="font-weight:bold">申请理由</td>
			<td scope="col" width="120" align="center" style="font-weight:bold">申请时间</td>
			<td scope="col" width="100" align="center" style="font-weight:bold">状态</td>
			<td scope="col" width="180" align="center" style="font-weight:bold">操作</td>
		</tr>
		
		
		<?php 
			$pagesize=5; //设定每一页显示的记录数						
			$accountsCount = web_admin_count_accountapp();
			
			foreach($accountsCount as $accountsnumber){
				$countnumber=$accountsnumber->accountCount;
			}
			$pages=intval($countnumber/$pagesize); //计算总页数
			if ($countnumber % $pagesize) $pages++;

			//设置缺省页码
			//↓判断“当前页码”是否赋值过
			if (isset($_GET['apage'])){ $page=intval($_GET['apage']); }else{ $page=1; }//否则，设置为第一页
           
			//↓计算记录偏移量
			$offset=$pagesize*($page - 1);

			//↓读取指定记录数
			$rs=web_admin_array_accountapp($offset,$pagesize);//取得—当前页—记录集！
		?> 
		
		<?php
		foreach ($rs as $accountapp) {
		?>
		<tr>
			<td align="center"><?php echo $accountapp->id; ?> </td>
			<td align="center"><?php echo $accountapp->user_nicename; ?> </td>
			<td align="center"><?php echo $accountapp->app_account; ?></td>
			<td align="center" title=<?php echo $accountapp->desc;?>><?php echo mb_substr($accountapp->desc,0,10,'UTF-8').'......'; ?></td>
			<td align="center"><?php echo $accountapp->time; ?></td>
			<td align="center"><?php if($accountapp->status == 0) echo "未审核"; ?></td>
			<td class="row" align="center"><table><td><tr><input type="button" class="btn btn-sm btn-warning" style="margin-right:5px;" onclick="cancelApp('<?php echo $accountapp->id;?>')" name="del" id="buttondel" value="撤销"> </tr><tr><input type="button" class="btn btn-sm btn-info" onclick="passApp('<?php echo $accountapp->id;?>','<?php echo $accountapp->app_account;?>')" name="upd" id="buttonupd" value="批准"></tr></td></table>
			</td>
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

		if ($page > 1){
			echo "<a href='?admin&page=accountappmgt&apage=".$first."'>首页</a>  ";
			echo "<a href='?admin&page=accountappmgt&apage=".$prev."'>上一页</a>  ";
		}

		if ($page < $pages){
			echo "<a href='?admin&page=accountappmgt&apage=".$next."'>下一页</a>  ";
			echo "<a href='?admin&page=accountappmgt&apage=".$last."'>尾页</a>  ";
		}

		//============================//
		//  翻页显示 二               
		//============================//
		echo " | 共有 ".$pages." 页(".$page."/".$pages.")";

		for ($i=1;$i< $page;$i++){echo "<a href='?admin&page=accountappmgt&apage=".$i."'>[".$i ."]</a>  ";}  // 1-先输出当前页之前的

		if ($page > 0) echo " [".$page."]";; // 2-再输出当前页

		for ($i=$page+1;$i<=$pages;$i++){echo "<a href='?admin&page=accountappmgt&apage=".$i."'>[".$i ."]</a>  ";}// 3-接着输出当前页之后

		echo "</p>";

	?>
	<script>
	    var xmlHttp;
		function createXMLHttpRequest(){
			if(window.ActiveXObject)
				xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
			else if(window.XMLHttpRequest)
				xmlHttp = new XMLHttpRequest();
		}
	    function cancelApp(id){
            
			createXMLHttpRequest();
			xmlHttp.open("GET","?admin&page=accountapprefuse&id="+id,true);
			xmlHttp.onreadystatechange = function(){
				if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
					alert("撤销成功");
				    window.location.reload();
				}
			}
			xmlHttp.send(null);
	   }
	   function passApp(id, app_account){
            
			createXMLHttpRequest();
			xmlHttp.open("GET","?admin&page=accountappok&id="+id+"&account="+app_account,true);
			xmlHttp.onreadystatechange = function(){
				if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
					alert("批准成功");
				    window.location.reload();
				}
			}
			xmlHttp.send(null);
	   }
	</script>
					
