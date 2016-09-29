<?php

	require_once './wp-content/themes/ReeooV3/wesite/common/dbaccessor.php';
	require_once './wp-content/themes/ReeooV3/wechat/common/wechat_dbaccessor.php';

	$sql = "SELECT * FROM ".$wpdb->prefix."group order by ID ASC";
	$getgroups = $wpdb->get_results($sql);		

	if(isset($_GET['del']) && !empty($_GET['del']) )
	{
		require_once './wp-admin/includes/user.php';
		
		$username = $wpdb->get_var( "SELECT user_login from ".$wpdb->prefix."users WHERE ID = ".intval($_GET['del']));
		if(!empty($username))
		{
		   //var_dump(ABSPATH);  //得到网站的根目录
			  
			$filedir = ABSPATH."wp-content/uploads/".$username;
			deldir($filedir);
		   
		}
		
		wp_delete_user( $_GET['del'] );
		//对应space的表也应该删除
		wp_delete_space($_GET['del']);
		//删除用户的公众号
		wp_delete_wechatnumber($_GET['del']);
		//删除用户所在的
		wp_delete_usergroup( $_GET['del'] );
		//echo ABSPATH."wp-content/uploads/";

		$uploadpath = wp_upload_dir();
		$dir = $uploadpath['basedir'].'/'.$username;
		exec("rm -rf {$path}");
	}
	
	$flag=$_REQUEST['flag'];
	$f=$_REQUEST["f"];
	if($f==null){$f=$flag;}

	$indata=$_REQUEST['Ipad'];
	$in=$_REQUEST["in"];
	if($in==null){$in=$indata;}
	$indata=$in;
	$rg=$_REQUEST['range'];
	$r=$_REQUEST["r"];
	if($r==null){$r=$rg;}
	$gid=$_REQUEST["groupid"];

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
		
  	
</script>
<style type="text/css">
	.sltfield{height: 34px;padding: 6px 12px;font-size: 14px;line-height: 1.428571429;color: #555;vertical-align: middle;background-color: #fff;background-image: none;border: 1px solid #ccc;border-radius: 4px;-webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);-webkit-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;}
</style>

<div class="main-title">
	<div class="title-1">当前位置：用户管理 > <font class="fontpurple">用户信息列表 </font>
	</div>
</div>

<div style="width:94.5%; margin-top:30px;">
	<label>请选择组：</label>
	
	<select name="usergroup2" class="sltfield" id="usergroup2" maxlength="20" onchange="selGroupUser1(this.options[this.selectedIndex].value)" style="width:300px;">
	   <option value="-1" selected="selected">全部</option>
	   <?php 
		    foreach($getgroups as $getgroup){
			?>
			<option value="<?php echo $getgroup -> ID;?>" ><?php echo $getgroup -> group_name;?></option>
		<?php }?>	
	</select>


	<input type="button" class="btn btn-primary" onclick="location.href='?admin&page=adminwechatuseradd'" name="del" id="buttondel" value="添加新用户" style="float:right">	
</div>

<form name ="content" onSubmit="return validateform()" action="?admin&page=usersearch" method="post" enctype="multipart/form-data">	
<div class="panel panel-default" style="margin-right:50px; margin-top:20px">
	<table class="table table-striped" width="800" border="0" align="center">
	<tbody>
	    <tr>
			 <td colspan=10 scope="col" width="100" align="left" >
				<select id="range" name="range" class="sltfield">
					<option value="">请选择
					<option value="user_login">用户名</option>
					<option value="display_name">显示昵称</option>
				</select>
			    <input id="indata" class="sltfield" name="indata" value="" />
				<input id="search1" class="btn btn-sm btn-default" type="submit" value="查询" />
			</td>
		</tr>
		<tr>
			<td scope="col" width="50" align="center" style="font-weight:bold">编号</td>
			<td scope="col" width="100" align="center" style="font-weight:bold">用户名</td>
			<td scope="col" width="100" align="center" style="font-weight:bold">分组</td>
			<td scope="col" width="150" align="center" style="font-weight:bold">显示昵称</td>
			<td scope="col" width="200" align="center" style="font-weight:bold">Email</td>
			<td scope="col" width="100" align="center" style="font-weight:bold">粉丝总数</td>
			<td scope="col" width="150" align="center" style="font-weight:bold">操作</td>
		</tr>
		
		<?php 
		
		if($f==1){

		$pagesize=6; //设定每一页显示的记录数						
		$vmembersCount = web_admin_count_selectuser($in,$r);
		//echo $vmembersCount;//获取表的记录总数
		
		foreach($vmembersCount as $vmembersnumber){
			 $countnumber=$vmembersnumber->memberCount;
			 
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
		$rs=web_admin_array_selectuser($in,$r,$offset,$pagesize);//取得—当前页—记录集！
		if($rs!==false){
		
		foreach($rs as $user) 
		{
			//Get the total number of the fans
			$userid=$user->ID;
			//Get the number of fans
			$fans_count=wechat_get_count_fans($userid);
			foreach($fans_count as $fan) 
			{
				$fan_count = $fan->fans_count;
			}
			//Get the total init number of the fans
			$usechat_info = wp_wechat_usechat_info($userid);	
			$wechat_init_fans = 0;
			if (!empty($usechat_info)) 
			{
				foreach($usechat_info as $u) 
				{
					$init_fan = $u->wechat_fan_init;
					$wechat_init_fans = $wechat_init_fans + $init_fan;
				}
			}
			//fans + init fans
			$fan_count = $fan_count + $wechat_init_fans;
			//get groupname
			$groupnames = wechat_get_group_name($userid);
			foreach($groupnames as $groupn) 
			{
				$groupname = $groupn->name;
			}	
			if(empty($groupname))  //如果是之前的用户，默认为未分组状态
			{
			    $groupiduser = 0; //未分组状态的id是固定的，name可以更改
				$getgroupnames = wechat_get_group_name_byid($groupiduser);
				foreach($getgroupnames as $getgroupname) 
				{
					$groupname = $getgroupname->group_name;
				}
			}
	?>
	   <tr>
			<td align="center"><?php echo $user->ID; ?></td>
			<td align="center"><?php echo $user->user_login; ?> </td>
			<td align="center"><?php echo $groupname;?> </td>
			<td align="center"><?php echo $user->display_name; ?></td>
			<td align="left"><?php echo $user->user_email ?></td>
			<td align="center"><?php echo $fan_count ?></td>
			
			<td class="row" align="center"><input name="site_id" type="hidden" id="site_id" value="308" maxlength="100"> <input type="button" class="btn btn-sm btn-warning" onclick="deluser('<?php echo $user->ID ?>','<?php echo $user->wid ?>','<?php echo $user->wechat_type; ?>')" name="del" id="buttondel" value="删除"> <input type="button" class="btn btn-sm btn-info" onclick="location.href='?admin&page=useredit&id=<?php echo $user->ID ?>&wid=<?php echo $user->wid ?>&wtype=<?php echo $user->wechat_type; ?>&header=0'" name="upd" id="buttonupd" value="更新"> </td>
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
			echo "<a href='?admin&page=usermanage&userpage=".$first."&f=".$f."&in=".$in."&r=".$r."'>首页</a>  ";
			echo "<a href='?admin&page=usermanage&userpage=".$prev."&f=".$f."&in=".$in."&r=".$r."'>上一页</a>  ";
		}

	if ($page < $pages)
		{
			echo "<a href='?admin&page=usermanage&userpage=".$next."&f=".$f."&in=".$in."&r=".$r."'>下一页</a>  ";
			echo "<a href='?admin&page=usermanage&userpage=".$last."&f=".$f."&in=".$in."&r=".$r."'>尾页</a>  ";
		}

		//============================//
		//  翻页显示 二               
		//============================//
	echo " | 共有 ".$pages." 页(".$page."/".$pages.")";

	for ($i=1;$i< $page;$i++){echo "<a href='?admin&page=usermanage&userpage=".$i."&f=".$f."&in=".$in."&r=".$r."'>[".$i ."]</a>  ";}  // 1-先输出当前页之前的

	if ($page > 0) echo " [".$page."]";; // 2-再输出当前页

	for ($i=$page+1;$i<=$pages;$i++){echo "<a href='?admin&page=usermanage&userpage=".$i."&f=".$f."&in=".$in."&r=".$r."'>[".$i ."]</a>  ";}// 3-接着输出当前页之后
		echo "</p>";
		}
		if($rs!==False && empty($rs)&&!isset($_REQUEST['deleteflag']))
		{
			echo "<script language='javascript'>alert('没有符合该条件的查询结果');</script>";
		}
		}else{
		    //如果有值就是按照对应的分组进行的list
			if(isset($_GET['groupid']))
			{
			    $groupid = $_GET['groupid'];
			}
		    
			$pagesize=6; //设定每一页显示的记录数						
			//-----------------------------------------------------------------------------------------------//
			//分页逻辑处理
			//-----------------------------------------------------------------------------------------------
			//$tmpArr = mysql_fetch_array($rs);
	        //$numAL = mysql_num_rows($site_list);  //取得记录总数$rs
			
			//$websitesCount=web_admin_count_website($current_user->ID);//获取记录总数
			
			//if(!empty($groupid))
			if(isset($_GET['groupid']))
			{
			    $groupid = $_GET['groupid'];
			    $usersCount = web_admin_count_user_groupid($groupid);
			}
			else{
			    $usersCount = web_admin_count_user();
			}
			
			foreach($usersCount as $usersnumber){
				 $countnumber=$usersnumber->userCount;
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
				
				
				//if(!empty($groupid))
				if(isset($_GET['groupid']))
				{
					$groupid = $_GET['groupid'];
					$rs=web_admin_array_user_groupid($offset,$pagesize,$groupid);//取得—当前页—记录集！
				}
				else{
					$rs=web_admin_array_user($offset,$pagesize);//取得—当前页—记录集！
				}
				
				//一个function活的总个数
				//foreach (){$curNem=iii->as bianliang}
				//$curNum = mysql_num_rows($rs); //$curNum - 当前页实际记录数，for循环输出用
				
				//$arraysCount=web_admin_array_website_count($offset,$pagesize,$current_user->ID);
				$arraysCount=web_admin_array_user_count($offset,$pagesize);
				foreach($arraysCount as $arraynumber){
					 $count_number=$arraynumber->arrayCount;
					 //echo $count_number;
				}
		?> 
		
		
		<?php
		foreach ($rs as $user) 
		{
			//Get the total number of the fans
			$userid=$user->ID;
			$fans_count=wechat_get_count_fans($userid);
			foreach($fans_count as $fans) 
			{
				$fan_count = $fans->fans_count;
			}
			//Get the total init number of the fans
			$usechat_info = wp_wechat_usechat_info($userid);	
			$wechat_init_fans = 0;
			if (!empty($usechat_info)) 
			{
				foreach($usechat_info as $u) 
				{
					$init_fan = $u->wechat_fan_init;
					$wechat_init_fans = $wechat_init_fans + $init_fan;
				}
			}
			//fans + init fans
			$fan_count = $fan_count + $wechat_init_fans;	
			//get groupname
			$groupnames = wechat_get_group_name($userid);	
            foreach($groupnames as $groupn) 
			{
				$groupname = $groupn->name;
			}	
            if(empty($groupname))  //如果是之前的用户，默认为未分组状态
			{
			    $groupiduser = 0; //未分组状态的id是固定的，name可以更改
				$getgroupnames = wechat_get_group_name_byid($groupiduser);
				foreach($getgroupnames as $getgroupname) 
				{
					$groupname = $getgroupname->group_name;
				}
			}			
			
		?>
		<tr>
			<td align="center"><?php echo $user->ID; ?></td>
			<td align="center"><?php echo $user->user_login; ?> </td>
			<td align="center"><?php echo $groupname;?> </td>
			<td align="center"><?php echo $user->display_name; ?></td>
			<td align="left"><?php echo $user->user_email ?></td>
			<td align="center"><?php echo $fan_count ?></td>			
			<td class="row" align="center"><input name="site_id" type="hidden" id="site_id" value="308" maxlength="100"> <input type="button" class="btn btn-sm btn-warning" onclick="deluser('<?php echo $user->ID ?>','<?php echo $user->wid ?>','<?php echo $user->wechat_type; ?>')" name="del" id="buttondel" value="删除"> <input type="button" class="btn btn-sm btn-info" onclick="location.href='?admin&page=useredit&id=<?php echo $user->ID ?>&wid=<?php echo $user->wid ?>&wtype=<?php echo $user->wechat_type; ?>&header=0'" name="upd" id="buttonupd" value="更新"> </td>
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
		//if(!empty($groupid))
		if(isset($_GET['groupid']))
		{
			$groupid = $_GET['groupid'];
			if ($page > 1)
			{
			echo "<a href='?admin&page=usermanage&groupid=".$groupid."&userpage=".$first."'>首页</a>  ";
			echo "<a href='?admin&page=usermanage&groupid=".$groupid."&userpage=".$prev."'>上一页</a>  ";
			}

			if ($page < $pages)
			{
			echo "<a href='?admin&page=usermanage&groupid=".$groupid."&userpage=".$next."'>下一页</a>  ";
			echo "<a href='?admin&page=usermanage&groupid=".$groupid."&userpage=".$last."'>尾页</a>  ";
			}

			//============================//
			//  翻页显示 二               
			//============================//
			echo " | 共有 ".$pages." 页(".$page."/".$pages.")";

			for ($i=1;$i< $page;$i++){echo "<a href='?admin&page=usermanage&groupid=".$groupid."&userpage=".$i."'>[".$i ."]</a>  ";}  // 1-先输出当前页之前的

			if ($page > 0) echo " [".$page."]";; // 2-再输出当前页

			for ($i=$page+1;$i<=$pages;$i++){echo "<a href='?admin&page=usermanage&groupid=".$groupid."&userpage=".$i."'>[".$i ."]</a>  ";}// 3-接着输出当前页之后

			//echo "转到第 <INPUT maxLength=3 size=3 value=".($page+1)." name=gotox> 页 <INPUT hideFocus onclick=\"location.href='?page=gotox.value';\" type=button value=Go name=cmd_goto>"; 
			echo "</p>";
		}else{
			if ($page > 1)
			{
			echo "<a href='?admin&page=usermanage&userpage=".$first."'>首页</a>  ";
			echo "<a href='?admin&page=usermanage&userpage=".$prev."'>上一页</a>  ";
			}

			if ($page < $pages)
			{
			echo "<a href='?admin&page=usermanage&userpage=".$next."'>下一页</a>  ";
			echo "<a href='?admin&page=usermanage&userpage=".$last."'>尾页</a>  ";
			}

			//============================//
			//  翻页显示 二               
			//============================//
			echo " | 共有 ".$pages." 页(".$page."/".$pages.")";

			for ($i=1;$i< $page;$i++){echo "<a href='?admin&page=usermanage&userpage=".$i."'>[".$i ."]</a>  ";}  // 1-先输出当前页之前的

			if ($page > 0) echo " [".$page."]";; // 2-再输出当前页

			for ($i=$page+1;$i<=$pages;$i++){echo "<a href='?admin&page=usermanage&userpage=".$i."'>[".$i ."]</a>  ";}// 3-接着输出当前页之后

			//echo "转到第 <INPUT maxLength=3 size=3 value=".($page+1)." name=gotox> 页 <INPUT hideFocus onclick=\"location.href='?page=gotox.value';\" type=button value=Go name=cmd_goto>"; 
			echo "</p>";
        }
		}?>
<script language='javascript'>
	var i="<?php echo $indata ?>";
	//$("#indata").attr("value",i);
	document.getElementById("indata").value=i;
	var g="<?php echo $r ?>";
	document.getElementById("range").value=g;
	var grid="<?php echo $gid ?>";
	document.getElementById("usergroup2").value=grid;
	
	var xmlHttp;
	function createXMLHttpRequest(){
		if(window.ActiveXObject)
			xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
		else if(window.XMLHttpRequest)
			xmlHttp = new XMLHttpRequest();
	}
	function deluser(id, wid, type){
        if (confirm("确认要删除？")) {
			createXMLHttpRequest();
			xmlHttp.open("GET","?admin&page=usermanage&del="+id+"&wid="+wid+"&wtype="+type,true);
			xmlHttp.onreadystatechange = function(){
				if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
					alert("删除成功");
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
	function selGroupUser1(groupid)
	{
		createXMLHttpRequest();
		xmlHttp.open("GET","?admin&page=usermanage&groupid="+groupid,true);
		xmlHttp.onreadystatechange = function(){
			if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
				/*alert("删除成功");
				//alert(window.location.href);
				if((window.location.href).indexOf("deleteflag") == -1)
				{
				   window.location.href = window.location.href + "&deleteflag";
				  
				}
				else
				{
				  window.location.reload();
				}*/
				window.location.href = "<?php echo home_url();?>/?admin&page=usermanage&groupid="+groupid;
				
			}
		}
		xmlHttp.send(null);
	}
</script>
					
					
