<?php

require_once './wp-content/themes/ReeooV3/wesite/common/dbaccessor.php';
require_once './wp-content/themes/ReeooV3/wechat/common/wechat_dbaccessor.php';

	
	$flag=$_REQUEST['flag'];
   $f=$_REQUEST["f"];
   if($f==null){$f=$flag;}
  // echo "flag的值".$flag;
  // echo "f的值".$f;
   $indata=$_REQUEST['Ipad'];
   $in=$_REQUEST["in"];
   if($in==null){$in=$indata;}
   $indata=$in;
   // echo $indata;
	//$weid=$_SESSION['WEID'];
	$rg=$_REQUEST['range'];
	$r=$_REQUEST["r"];
	if($r==null){$r=$rg;}
	//echo "这是我的值".$r;

?>
<script>
    function checknull(obj, warning)
	{
	  if (obj.value == "") {
		alert(warning);
		obj.focus();
		return true;
	  }
	return false;
	}
    function validateform()
	{
	  var selectone = document.getElementById("range"); 
	  var index = selectone.selectedIndex;
	  var value = selectone.options[index].value; 
	  if (checknull(document.content.indata, "请输入查询内容!") == true) {
		return false;
	  }
	}
  	
	function createGroup(){
        window.open('<?php bloginfo('template_directory'); ?>/groupadd.php','_blank','height=228,width=588,top=120,left=240,toolbar=no,menubar=no,scrollbars=no,resizable=no,location=no,status=no,titlebar=no')	
	}
	function updateGroup(id){
        window.open('<?php bloginfo('template_directory'); ?>/groupupdate.php?groupid='+id,'_blank','height=228,width=588,top=120,left=240,toolbar=no,menubar=no,scrollbars=no,resizable=no,location=no,status=no,titlebar=no')	
	}
</script>
<div class="main-title">
	<div class="title-1">当前位置：分组管理 > <font class="fontpurple">分组信息列表 </font>
	</div>
</div>

<input type="button" class="btn btn-primary" onClick="createGroup()" name="cg_btn" id="cg_btn" value="添加新分组" style="margin-top:10px;">	

<form name ="content" onSubmit="return validateform()" action="?admin&page=groupsearch" method="post" enctype="multipart/form-data">	
<div class="panel panel-default" style="margin-right:50px; margin-top:30px">
	<!--<div class="panel-heading">用户列表</div>-->
	<table class="table table-striped" width="800" border="0" align="center">
	<tbody>
	
	    <tr>
			 <td colspan=10 scope="col" width="100" align="left" >
				 <select id="range" name="range" class="sltfield">
					<option value="">请选择
					<option value="group_name">组名</option>
					<!--<option value="display_name">显示昵称</option>-->
				</select>
			    <input id="indata" name="indata" class="sltfield" value="" />
				<input id="search1" class="btn btn-sm btn-default" type="submit" value="查询" />
			</td>
		</tr>
		<tr>
			<td scope="col" width="100" align="center" style="font-weight:bold">编号</td>
			<td scope="col" width="100" align="center" style="font-weight:bold">分组名称</td>
			<td scope="col" width="100" align="center" style="font-weight:bold">分组管理员</td>
			<td scope="col" width="150" align="center" style="font-weight:bold">操作</td>
		</tr>
		
		<?php 
		
		if($f==1){

		$pagesize=6; //设定每一页显示的记录数						
		$vgroupsCount = web_admin_count_selectusergroup($in,$r);
		//echo $vmembersCount;//获取表的记录总数
		
		foreach($vgroupsCount as $vgroupsnumber){
			 $countnumber=$vgroupsnumber->memberCount;
			 
		}
		
		$pages=intval($countnumber/$pagesize); //计算总页数

		if ($countnumber % $pagesize) $pages++;

		//设置缺省页码
		//↓判断“当前页码”是否赋值过
		if (isset($_GET['userpage'])){ $page=intval($_GET['userpage']); }else{ $page=1; }//否则，设置为第一页
	   
		//↓计算记录偏移量
			$offset=$pagesize*($page - 1);

		//↓读取指定记录数
		//$rs=web_admin_array_vmember($_SESSION['WEID'],$offset,$pagesize);//取得—当前页—记录集！
		$rs=web_admin_array_selectusergroup($in,$r,$offset,$pagesize);//取得—当前页—记录集！
		if($rs!==false){
		
		foreach($rs as $group) 
		{
	?>
	   <tr>
			<td align="center"><?php echo $group->ID; ?></td>
			<td align="center"><?php echo $group->group_name; ?> </td>
			<td align="center"><?php if(empty($group->user_login)){echo "无";}else{echo $group->user_login;} ?>  </td>
			<td class="row" align="center">
				<?php if($group->ID != 0){?>
				<input type="button" class="btn btn-sm btn-warning" onclick="delgroup('<?php echo $group->ID ?>')" name="del" id="buttondel" value="删除">
				<?php }?>
				<input type="button" class="btn btn-sm btn-info" onclick="updateGroup('<?php echo $group->ID ?>')" name="upd" id="buttonupd" value="更新"> 
			</td>
<!--
			<input type="button" class="btn btn-sm btn-info" onclick="location.href='?admin&page=adminwechatgroupedit&id=<?php echo $group->ID ?>'" name="upd" id="buttonupd" value="更新"> </td>
-->
		</tr>
	<?php
		}
	?>
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
			echo "<a href='?admin&page=groupmanage&grouppage=".$first."&f=".$f."&in=".$in."&r=".$r."'>首页</a>  ";
			echo "<a href='?admin&page=groupmanage&grouppage=".$prev."&f=".$f."&in=".$in."&r=".$r."'>上一页</a>  ";
		}

	if ($page < $pages)
		{
			echo "<a href='?admin&page=groupmanage&grouppage=".$next."&f=".$f."&in=".$in."&r=".$r."'>下一页</a>  ";
			echo "<a href='?admin&page=groupmanage&grouppage=".$last."&f=".$f."&in=".$in."&r=".$r."'>尾页</a>  ";
		}

		//============================//
		//  翻页显示 二               
		//============================//
	echo " | 共有 ".$pages." 页(".$page."/".$pages.")";

	for ($i=1;$i< $page;$i++){echo "<a href='?admin&page=groupmanage&grouppage=".$i."&f=".$f."&in=".$in."&r=".$r."'>[".$i ."]</a>  ";}  // 1-先输出当前页之前的

	if ($page > 0) echo " [".$page."]";; // 2-再输出当前页

	for ($i=$page+1;$i<=$pages;$i++){echo "<a href='?admin&page=groupmanage&grouppage=".$i."&f=".$f."&in=".$in."&r=".$r."'>[".$i ."]</a>  ";}// 3-接着输出当前页之后
		echo "</p>";
		}
		if($rs!==False && empty($rs)&&!isset($_REQUEST['deleteflag']))
		{
			echo "<script language='javascript'>alert('没有符合该条件的查询结果');</script>";
		}
		}else{
		
		
			$pagesize=6; //设定每一页显示的记录数						
			//-----------------------------------------------------------------------------------------------//
			//分页逻辑处理
			//-----------------------------------------------------------------------------------------------
			//$tmpArr = mysql_fetch_array($rs);
	        //$numAL = mysql_num_rows($site_list);  //取得记录总数$rs
			
			//$websitesCount=web_admin_count_website($current_user->ID);//获取记录总数
			$groupsCount = web_admin_count_usergroup();
			//echo $usersCount;//获取wp_users表的记录总数
			
			foreach($groupsCount as $groupsnumber){
				 $countnumber=$groupsnumber->groupCount;
				 //echo $countnumber;
			}
			
			$pages=intval($countnumber/$pagesize); //计算总页数

			if ($countnumber % $pagesize) $pages++;

			//设置缺省页码
			//↓判断“当前页码”是否赋值过
			if (isset($_GET['grouppage'])){ $page=intval($_GET['grouppage']); }else{ $page=1; }//否则，设置为第一页
           
			//↓计算记录偏移量
				$offset=$pagesize*($page - 1);

			//↓读取指定记录数
				//$rs=web_admin_array_website($offset,$pagesize,$current_user->ID);//取得—当前页—记录集！
				$rs=web_admin_array_usergroup($offset,$pagesize);//取得—当前页—记录集！
				
		?> 
		
		
		<?php
		foreach ($rs as $group) 
		{		
		?>
		<tr>
			<td align="center"><?php echo $group->ID; ?></td>
			<td align="center"><?php echo $group->group_name; ?> </td>
			<td align="center"><?php if(empty($group->user_login)){echo "无";}else{echo $group->user_login;} ?> </td>
			<td class="row" align="center">
			<?php if($group->ID != 0){?>
			<input type="button" class="btn btn-sm btn-warning" onclick="delgroup('<?php echo $group->ID ?>')" name="del" id="buttondel" value="删除"> 
			<?php }?>
			<input type="button" class="btn btn-sm btn-info" onclick="updateGroup('<?php echo $group->ID ?>')" name="upd" id="buttonupd" value="更新"> </td>
		</tr>
		<?php
		}
        ?>
		</tr>
	</tbody>
</table>
</form>
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
		echo "<a href='?admin&page=groupmanage&grouppage=".$first."'>首页</a>  ";
		echo "<a href='?admin&page=groupmanage&grouppage=".$prev."'>上一页</a>  ";
		}

		if ($page < $pages)
		{
		echo "<a href='?admin&page=groupmanage&grouppage=".$next."'>下一页</a>  ";
		echo "<a href='?admin&page=groupmanage&grouppage=".$last."'>尾页</a>  ";
		}

		//============================//
		//  翻页显示 二               
		//============================//
		echo " | 共有 ".$pages." 页(".$page."/".$pages.")";

		for ($i=1;$i< $page;$i++){echo "<a href='?admin&page=groupmanage&grouppage=".$i."'>[".$i ."]</a>  ";}  // 1-先输出当前页之前的

		if ($page > 0) echo " [".$page."]";; // 2-再输出当前页

		for ($i=$page+1;$i<=$pages;$i++){echo "<a href='?admin&page=groupmanage&grouppage=".$i."'>[".$i ."]</a>  ";}// 3-接着输出当前页之后

		//echo "转到第 <INPUT maxLength=3 size=3 value=".($page+1)." name=gotox> 页 <INPUT hideFocus onclick=\"location.href='?page=gotox.value';\" type=button value=Go name=cmd_goto>"; 
		echo "</p>";

		}?>
<script language='javascript'>
	var i="<?php echo $indata ?>";
	//$("#indata").attr("value",i);
	document.getElementById("indata").value=i;
	var g="<?php echo $r ?>";
	document.getElementById("range").value=g;
	var xmlHttp;
	function createXMLHttpRequest(){
		if(window.ActiveXObject)
			xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
		else if(window.XMLHttpRequest)
			xmlHttp = new XMLHttpRequest();
	}
	function delgroup(id){
	    if(id == 1)  //未分组状态的分组id是固定的，名称可以更新
		{
		    alert("该分组不允许删除");
		}else{
			if (confirm("确认要删除？")) {
				createXMLHttpRequest();
				xmlHttp.open("GET","?admin&page=groupdelete&header=0&footer=0&del="+id,true);
				xmlHttp.onreadystatechange = function(){
					if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
						if(xmlHttp.responseText)
							alert(xmlHttp.responseText);
						//alert(window.location.href);
						if((window.location.href).indexOf("deleteflag") == -1)
						{
							window.location.href = window.location.href + "&deleteflag";
						  
						}
						else
						{
						   window.location.reload();
						}
						
					}
				}
				xmlHttp.send(null);
			}
		}
	}
</script>
					
					
